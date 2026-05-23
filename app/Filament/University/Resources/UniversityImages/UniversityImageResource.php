<?php

namespace App\Filament\University\Resources\UniversityImages;

use App\Filament\University\Resources\UniversityImages\Pages\CreateUniversityImage;
use App\Filament\University\Resources\UniversityImages\Pages\EditUniversityImage;
use App\Filament\University\Resources\UniversityImages\Pages\ListUniversityImages;
use App\Models\UniversityImage;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Filament\Facades\Filament;
use UnitEnum;

class UniversityImageResource extends Resource
{
    protected static ?string $model = UniversityImage::class;

    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static UnitEnum|string|null $navigationGroup = 'الصور';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            FileUpload::make('uploads')
                ->label('الصور')
                ->multiple()
                ->maxFiles(10)
                ->storeFiles(false)
                ->maxSize(6144)
                ->image()
                ->preserveFilenames()
                ->required()
                ->validationAttribute('الصورة')
                ->hint('يمكن رفع عدة صور دفعة واحدة (حد أقصى 10 لكل جامعة)')
                ->hidden(fn ($record, string $operation) => $operation === 'edit'),

            FileUpload::make('replacement')
                ->label('استبدال الصورة')
                ->image()
                ->storeFiles(false)
                ->maxSize(6144)
                ->preserveFilenames()
                ->validationAttribute('الصورة')
                ->hint('يتم استبدال الصورة الحالية عند رفع صورة جديدة')
                ->hidden(fn ($record, string $operation) => $operation === 'create'),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('priority')
            ->modifyQueryUsing(fn (Builder $query) => $query->orderBy('priority')->orderBy('created_at', 'desc'))
            ->columns([
                ImageColumn::make('path_thumb')
                    ->disk('public')
                    ->label('المعاينة')
                    ->square(),
                SelectColumn::make('priority')
                    ->label('الأولوية')
                    ->options(function (?UniversityImage $record) {
                        $count = UniversityImage::where('university_id', Filament::auth()->id())->count();
                        $current = $record?->priority ?? 1;
                        $max = max($count, $current, 1);

                        return collect(range(1, $max))
                            ->mapWithKeys(fn ($i) => [$i => (string) $i])
                            ->all();
                    })
                    ->selectablePlaceholder(false)
                    ->extraAttributes(['style' => 'width: 96px'])
                    ->sortable()
                    ->afterStateUpdated(function (UniversityImage $record, $state) {
                        static::reorderPriorities($record, max(1, (int) $state));
                    })
                    ->rules(['required', 'integer', 'min:1']),
                ToggleColumn::make('is_active')
                    ->label('مفعل')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('أضيفت')
                    ->since()
                    ->sortable(),
            ])
            ->recordUrl(null)
            ->paginationPageOptions([10, 25, 50])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
              DeleteBulkAction::make(),
            ]);
    }

    /**
     * Insert the given record at the requested priority and re-sequence the rest efficiently.
     */
    protected static function reorderPriorities(UniversityImage $record, int $newPriority): void
    {
        $images = UniversityImage::where('university_id', $record->university_id)
            ->orderBy('priority')
            ->orderBy('created_at', 'desc')
            ->get();

        // Remove current record from the list
        $filtered = $images->reject(fn (UniversityImage $img) => $img->id === $record->id)->values();

        // Clamp new position within range (1..count+1)
        $insertIndex = max(0, min($newPriority - 1, $filtered->count()));

        // Insert record at desired index
        $filtered->splice($insertIndex, 0, [$record]);

        // Write back priorities only where changed
        $filtered->each(function (UniversityImage $img, int $idx) {
            $desired = $idx + 1;
            if ($img->priority !== $desired) {
                $img->updateQuietly(['priority' => $desired]);
            }
        });
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('university_id', Filament::auth()->id());
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUniversityImages::route('/'),
            'create' => CreateUniversityImage::route('/create'),
            'edit' => EditUniversityImage::route('/{record}/edit'),
        ];
    }
}
