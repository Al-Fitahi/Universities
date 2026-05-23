<?php

namespace App\Filament\University\Resources\Projects\Schemas;

use App\Models\Project;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class ProjectInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        TextEntry::make('name')
                            ->label('اسم المشروع')
                            ->weight('bold'),
                        TextEntry::make('projectType.name')
                            ->label('نوع المشروع')
                            ->badge()
                            ->color('info'),
                        IconEntry::make('is_active')
                            ->label('الحالة')
                            ->boolean(),
                        TextEntry::make('college_name')
                            ->label('الكلية')
                            ->state(fn (Project $record): string => $record->superUniverMajor?->universityMajor?->major?->college?->name ?? 'غير محدد')
                            ->badge()
                            ->color('primary'),
                        TextEntry::make('major_name')
                            ->label('التخصص')
                            ->state(fn (Project $record): string => $record->superUniverMajor?->universityMajor?->major?->name ?? 'غير محدد')
                            ->badge()
                            ->color('success'),
                        TextEntry::make('supervisor_name')
                            ->label('المشرف')
                            ->state(fn (Project $record): string => $record->superUniverMajor?->supervisor?->full_name ?? 'غير محدد')
                            ->badge()
                            ->color('warning'),
                        TextEntry::make('students_count')
                            ->label('عدد الطلاب')
                            ->state(fn (Project $record): int => $record->relationLoaded('students') ? $record->students->count() : (int) ($record->students_count ?? 0))
                            ->badge()
                            ->color('gray'),
                        TextEntry::make('created_at')
                            ->label('تاريخ الإنشاء')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('آخر تحديث')
                            ->dateTime(),
                        TextEntry::make('project_overview')
                            ->label('نبذة المشروع')
                            ->prose()
                            ->placeholder('لا توجد نبذة.')
                            ->columnSpanFull(),
                        TextEntry::make('student_names')
                            ->label('الطلاب المشاركون')
                            ->state(function (Project $record): string {
                                $students = $record->students;

                                if ($students->isEmpty()) {
                                    return '<span class="text-gray-500">لا يوجد طلاب مرتبطون بهذا المشروع.</span>';
                                }

                                $items = $students
                                    ->pluck('full_name')
                                    ->map(fn (string $name): string => '<li>' . e($name) . '</li>')
                                    ->implode('');

                                return '<ul class="list-disc ps-5 space-y-1">' . $items . '</ul>';
                            })
                            ->html()
                            ->placeholder('لا يوجد طلاب مرتبطون بهذا المشروع.')
                            ->columnSpanFull(),
                        TextEntry::make('pdf_path')
                            ->label('ملف المشروع')
                            ->formatStateUsing(function (?string $state): string {
                                if (blank($state)) {
                                    return 'غير متوفر';
                                }

                                return basename(str_replace('\\', '/', $state));
                            })
                            ->url(fn (Project $record): ?string => blank($record->pdf_path)
                                ? null
                                : asset('storage/' . ltrim(str_replace('\\', '/', (string) $record->pdf_path), '/')))
                            ->openUrlInNewTab()
                            ->placeholder('غير متوفر')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
