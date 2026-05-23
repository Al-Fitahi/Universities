<?php

namespace App\Filament\Resources\BannerResource\RelationManagers;

use App\Models\BannerImage;
use App\Services\Image\ImageProcessor;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class BannerImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    protected static ?string $label = 'صور البانر';

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('priority')
            ->modifyQueryUsing(fn (Builder $query) => $query->orderBy('priority')->orderBy('created_at', 'desc'))
            ->columns([
                ImageColumn::make('path_thumb')
                    ->disk('public')
                    ->label('معاينة')
                    ->square(),
                SelectColumn::make('priority')
                    ->label('الأولوية')
                    ->options(function (BannerImage $record) {
                        $count = $this->getOwnerRecord()->images()->count();
                        $current = $record?->priority ?? 1;
                        $max = max($count, $current, 1);

                        return collect(range(1, $max))
                            ->mapWithKeys(fn ($i) => [$i => (string) $i])
                            ->all();
                    })
                    ->selectablePlaceholder(false)
                    ->extraAttributes(['style' => 'width: 96px'])
                    ->sortable()
                    ->afterStateUpdated(function (BannerImage $record, $state) {
                        static::reorderPriorities($record, max(1, (int) $state));
                    }),
                TextColumn::make('link_url')
                    ->label('الرابط')
                    ->limit(30),
                ToggleColumn::make('is_active')
                    ->label('مفعل')
                    ->sortable(),
            ])
            ->headerActions([
                Action::make('upload')
                    ->label('رفع صور')
                    ->color('primary')
                    ->icon('heroicon-o-photo')
                    ->form([
                        FileUpload::make('uploads')
                            ->label('الصور')
                            ->multiple()
                            ->maxFiles(10)
                            ->storeFiles(false)
                            ->maxSize(6144)
                            ->image()
                            ->preserveFilenames()
                            ->required(),
                        TextInput::make('link_url')
                            ->label('رابط اختياري')
                            ->url()
                            ->nullable(),
                    ])
                    ->action(function (array $data, ImageProcessor $processor) {
                        $files = $data['uploads'] ?? [];
                        $banner = $this->getOwnerRecord();

                        if (empty($files)) {
                            throw ValidationException::withMessages([
                                'uploads' => 'يجب اختيار صورة واحدة على الأقل.',
                            ]);
                        }

                        $currentCount = $banner->images()->count();
                        $remaining = 10 - $currentCount;

                        if (count($files) > $remaining) {
                            throw ValidationException::withMessages([
                                'uploads' => 'الحد الأقصى 10 صور. المتاح حالياً: '.max($remaining, 0).'.',
                            ]);
                        }

                        $priorityStart = (int) $banner->images()->max('priority');
                        foreach ($files as $index => $file) {
                            $paths = $processor->processAndStore($file, "banners/{$banner->id}");

                            $banner->images()->create([
                                'path_main' => $paths['path_main'],
                                'path_thumb' => $paths['path_thumb'],
                                'priority' => $priorityStart + $index + 1,
                                'is_active' => true,
                                'link_url' => $data['link_url'] ?? null,
                            ]);
                        }

                        Notification::make()
                            ->title('تم رفع الصور')
                            ->body('تم حفظ صور البانر ومعالجتها بنجاح.')
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
               EditAction::make()
                    ->label('تعديل')
                    ->form([
                        FileUpload::make('replacement')
                            ->label('استبدال الصورة')
                            ->image()
                            ->storeFiles(false)
                            ->maxSize(6144)
                            ->preserveFilenames(),
                        TextInput::make('priority')
                            ->label('أولوية العرض')
                            ->numeric()
                            ->required(),
                        TextInput::make('link_url')
                            ->label('الرابط')
                            ->url()
                            ->nullable(),
                        Toggle::make('is_active')
                            ->label('مفعل')
                            ->default(true),
                    ])
                    ->action(function (BannerImage $record, array $data, ImageProcessor $processor) {
                        $replacement = $data['replacement'] ?? null;

                        if ($replacement) {
                            $processor->deleteStored($record->path_main);
                            $processor->deleteStored($record->path_thumb);

                            $paths = $processor->processAndStore($replacement, "banners/{$record->banner_id}");
                            $data['path_main'] = $paths['path_main'];
                            $data['path_thumb'] = $paths['path_thumb'];
                        }

                        unset($data['replacement']);

                        $record->update([
                            'path_main' => $data['path_main'] ?? $record->path_main,
                            'path_thumb' => $data['path_thumb'] ?? $record->path_thumb,
                            'priority' => $data['priority'] ?? $record->priority,
                            'link_url' => $data['link_url'] ?? null,
                            'is_active' => $data['is_active'] ?? $record->is_active,
                        ]);
                    }),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    /**
     * Insert a banner image at the requested priority and re-sequence efficiently.
     */
    protected static function reorderPriorities(BannerImage $record, int $newPriority): void
    {
        $images = BannerImage::where('banner_id', $record->banner_id)
            ->orderBy('priority')
            ->orderBy('created_at', 'desc')
            ->get();

        $filtered = $images->reject(fn (BannerImage $img) => $img->id === $record->id)->values();

        $insertIndex = max(0, min($newPriority - 1, $filtered->count()));

        $filtered->splice($insertIndex, 0, [$record]);

        $filtered->each(function (BannerImage $img, int $idx) {
            $desired = $idx + 1;
            if ($img->priority !== $desired) {
                $img->updateQuietly(['priority' => $desired]);
            }
        });
    }
}
