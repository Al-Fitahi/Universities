<?php

namespace App\Filament\University\Resources\Projects\Schemas;

use App\Models\Application;
use App\Models\Project as ProjectModel;
use App\Models\Student;
use App\Models\SuperUniverMajor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('super_univer_major_id')
                    ->label('الكلية / التخصص / المشرف')
                    ->relationship(
                        name: 'superUniverMajor',
                        titleAttribute: 'id',
                        modifyQueryUsing: function (Builder $query, Select $component): Builder {
                            $selectedSuperUniverMajorId = $component->getState();

                            return $query
                                ->where(function (Builder $scopeQuery) use ($selectedSuperUniverMajorId): void {
                                    $scopeQuery->whereHas('universityMajor', function (Builder $universityMajorQuery): void {
                                        $universityMajorQuery->where('university_id', Auth::guard('university')->id());
                                    });

                                    if (filled($selectedSuperUniverMajorId)) {
                                        $scopeQuery->orWhere('id', $selectedSuperUniverMajorId);
                                    }
                                })
                                ->with([
                                    'universityMajor.major.college:id,name',
                                    'universityMajor.major:id,name,college_id',
                                    'supervisor:id,F_name,S_name,Th_name,Su_name',
                                ]);
                        }
                    )
                    ->getOptionLabelFromRecordUsing(function (SuperUniverMajor $record): string {
                        $collegeName = $record->universityMajor?->major?->college?->name ?? 'كلية غير معروفة';
                        $majorName = $record->universityMajor?->major?->name ?? 'تخصص غير معروف';
                        $supervisorName = $record->supervisor?->full_name ?? 'مشرف غير معروف';

                        return "{$collegeName} - {$majorName} - {$supervisorName}";
                    })
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(fn ($state, $set) => $set('student_ids', []))
                    ->helperText('اختر من القائمة المرتبطة مباشرة بجدول Super_Univer_Major.')
                    ->required(),
                Select::make('student_ids')
                    ->label('الطلاب')
                    ->multiple()
                    ->dehydrated(false)
                    ->searchable()
                    ->preload()
                    ->options(function ($get, ?ProjectModel $record): array {
                        $superUniverMajorId = $get('super_univer_major_id');

                        if (blank($superUniverMajorId)) {
                            return [];
                        }

                        $universityMajorId = static::resolveUniversityMajorId((int) $superUniverMajorId);

                        if (blank($universityMajorId)) {
                            return [];
                        }

                        return Student::query()
                            ->where(function (Builder $query) use ($record): void {
                                $query->whereNull('project_id');

                                if ($record) {
                                    $query->orWhere('project_id', $record->id);
                                }
                            })
                            ->whereHas('applications', function (Builder $query) use ($universityMajorId): void {
                                $query
                                    ->where('university_major_id', $universityMajorId)
                                    ->where('status', Application::STATUS_ACCEPTED)
                                    ->where('is_active', true);
                            })
                            ->orderBy('F_name')
                            ->orderBy('S_name')
                            ->orderBy('Th_name')
                            ->orderBy('Su_name')
                            ->get()
                            ->mapWithKeys(fn (Student $student): array => [$student->id => $student->full_name])
                            ->all();
                    })
                    ->disabled(fn ($get): bool => blank($get('super_univer_major_id')))
                    ->helperText('سيظهر فقط الطلاب المقبولين والنشطين في طلبات التقديم وغير المرتبطين بأي مشروع.')
                    ->required(),
                TextInput::make('name')
                    ->label('اسم المشروع')
                    ->unique(ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'اسم المشروع مستخدم مسبقًا، اختر اسمًا آخر.',
                    ])
                    ->required(),
                Select::make('project_type_id')
                    ->relationship('projectType', 'name')
                    ->required(),
                Textarea::make('project_overview')
                    ->label('نبذة المشروع')
                    ->rows(2)
                    ->maxLength(180)
                    ->helperText('الحد الأقصى 180 حرف (تقريبًا سطر ونص).')
                    ->required()
                    ->columnSpanFull(),
                FileUpload::make('pdf_path')
                    ->label('مستند المشروع (PDF)')
                    ->disk('public')
                    ->directory('projects/pdfs')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(10240)
                    ->validationMessages([
                        'max' => 'حجم الملف يجب ألا يتجاوز 10 ميجابايت.',
                        'mimetypes' => 'يجب رفع ملف PDF فقط.',
                        'mimes' => 'يجب رفع ملف PDF فقط.',
                    ])
                    ->helperText('يسمح فقط بملف PDF وبحد أقصى 10 ميجابايت.')
                    ->required(),
                Toggle::make('is_active')
                    ->required()
                    ->default(true),
            ]);
    }

    private static function resolveUniversityMajorId(int $superUniverMajorId): ?int
    {
        static $cache = [];

        if (array_key_exists($superUniverMajorId, $cache)) {
            return $cache[$superUniverMajorId];
        }

        $cache[$superUniverMajorId] = SuperUniverMajor::query()
            ->whereKey($superUniverMajorId)
            ->value('university_major_id');

        return $cache[$superUniverMajorId];
    }
}
