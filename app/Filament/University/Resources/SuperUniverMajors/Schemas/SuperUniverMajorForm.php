<?php

namespace App\Filament\University\Resources\SuperUniverMajors\Schemas;

use App\Models\College;
use App\Models\Supervisor;
use App\Models\UniversityMajor;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SuperUniverMajorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('supervisor_id')
                    ->label('المشرف')
                    ->relationship(
                        name: 'supervisor',
                        titleAttribute: 'F_name',
                        modifyQueryUsing: function (Builder $query, Select $component): Builder {
                            $selectedSupervisorId = $component->getState();

                            return $query
                                ->where(function (Builder $scopeQuery) use ($selectedSupervisorId) {
                                    $scopeQuery->whereHas('superUniverMajor.universityMajor', function (Builder $universityMajorQuery) {
                                        $universityMajorQuery->where('university_id', Auth::guard('university')->id());
                                    });

                                    if (filled($selectedSupervisorId)) {
                                        $scopeQuery->orWhere('id', $selectedSupervisorId);
                                    }
                                })
                                ->orderBy('F_name');
                        }
                    )
                    ->getOptionLabelFromRecordUsing(fn (Supervisor $record): string => $record->full_name)
                    ->searchable(['F_name', 'S_name', 'Th_name', 'Su_name', 'phone_number'])
                    ->preload()
                    ->required()
                    ->helperText(fn ($get) => (blank($get('college_id')) || blank($get('university_major_id')))
                        ? 'حدد الكلية والتخصص أولًا، ثم تقدر تضيف مشرف جديد.'
                        : null)
                    ->createOptionAction(fn (Action $action, Select $component) => $action
                        ->tooltip('حدد الكلية والتخصص أولًا، ثم أضف المشرف.')
                        ->beforeFormFilled(function (Action $action, Select $component): void {
                            $formState = $component->getContainer()->getRawState();
                            $collegeId = data_get($formState, 'college_id') ?? data_get($formState, 'data.college_id');
                            $universityMajorId = data_get($formState, 'university_major_id') ?? data_get($formState, 'data.university_major_id');

                            if (blank($collegeId) || blank($universityMajorId)) {
                                Notification::make()
                                    ->title('حدد الكلية والتخصص أولًا قبل إنشاء مشرف جديد.')
                                    ->danger()
                                    ->send();

                                $action->halt();
                            }
                        }))
                    ->createOptionForm([
                        TextInput::make('F_name')->label('الاسم الأول')->required(),
                        TextInput::make('S_name')->label('اسم الأب')->required(),
                        TextInput::make('Th_name')->label('اسم الجد')->required(),
                        TextInput::make('Su_name')->label('اللقب')->required(),
                        TextInput::make('phone_number')
                            ->label('رقم الهاتف')
                            ->required()
                            ->maxLength(10),
                        Toggle::make('is_active')
                            ->label('نشط')
                            ->default(true),
                    ])
                    ->createOptionUsing(function (Select $component, array $data): int {
                        $formState = $component->getContainer()->getState();
                        $collegeId = data_get($formState, 'college_id') ?? data_get($formState, 'data.college_id');
                        $universityMajorId = data_get($formState, 'university_major_id') ?? data_get($formState, 'data.university_major_id');

                        if (blank($collegeId) || blank($universityMajorId)) {
                            Notification::make()
                                ->title('حدد الكلية والتخصص أولًا ثم أضف المشرف.')
                                ->danger()
                                ->send();

                            throw ValidationException::withMessages([
                                'supervisor_id' => 'حدد الكلية والتخصص أولًا ثم أضف المشرف.',
                            ]);
                        }

                        return Supervisor::create($data)->id;
                    }),

                Select::make('college_id')
                    ->label('الكلية')
                    ->options(fn () => College::query()
                        ->whereHas('majors.universityMajors', function (Builder $query) {
                            $query->where('university_id', Auth::guard('university')->id());
                        })
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all())
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->dehydrated(false)
                    ->afterStateUpdated(fn ($state, $set) => $set('university_major_id', null)),

                Select::make('university_major_id')
                    ->label('التخصص')
                    ->options(function ($get) {
                        $collegeId = $get('college_id');
                        $universityId = Auth::guard('university')->id();

                        if (! $collegeId || ! $universityId) {
                            return [];
                        }

                        return UniversityMajor::query()
                            ->select(['id', 'major_id'])
                            ->where('university_id', $universityId)
                            ->whereHas('major', function ($query) use ($collegeId) {
                                $query->where('college_id', $collegeId);
                            })
                            ->with('major:id,name')
                            ->get()
                            ->mapWithKeys(function (UniversityMajor $universityMajor) {
                                $majorName = $universityMajor->major?->name ?? ('تخصص #' . $universityMajor->id);

                                return [$universityMajor->id => $majorName];
                            })
                            ->all();
                    })
                    ->searchable()
                    ->preload()
                    ->required()
                    ->helperText('اختر كلية أولًا ثم سيظهر لك تخصصات الجامعة ضمن هذه الكلية فقط.'),
            ]);
    }
}
