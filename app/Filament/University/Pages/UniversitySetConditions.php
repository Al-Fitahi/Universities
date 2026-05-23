<?php

namespace App\Filament\University\Pages;

use App\Models\UniversityMajor;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use ZipStream\CentralDirectoryFileHeader;

class UniversitySetConditions extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.university.pages.university-set-conditions';
    protected static ?string $title = 'إعداد شروط التخصصات';
    protected static ?string $navigationLabel = 'إعدادات التخصصات';
    protected static ?int $navigationSort = 2;
    protected static string|BackedEnum|null $navigationIcon = 'fas-university';

    public array $data = [];

    public function mount(): void
    {
        $universityId = auth('university')->id();

$this->form->fill([
    'colleges' => UniversityMajor::with('major.college')
        ->where('university_id', $universityId)
        ->get()
        ->groupBy(fn($um) => $um->major->college->name) // تجمع حسب الكلية
        ->map(fn($majors, $collegeName) => [
            'college' => $collegeName,
            'majors' => $majors->map(fn($um) => [
                'id' => $um->id,
                'major' => $um->major->name,
                'number_of_seats' => $um->number_of_seats,
                'admission_rate' => $um->admission_rate,
                'study_years' => $um->study_years,
                'tuition_fee' => $um->tuition_fee,
                'published' => (bool) $um->published,
            ])->toArray(),
        ])->values()->toArray(),
]);

    }

    protected function getFormSchema(): array
    {
        return [
Repeater::make('colleges')
->deletable(false)
->itemLabel(fn (array $state): ?string => $state['college'] ?? null)
    ->schema([
        Section::make('')
            ->schema([
                Repeater::make('majors')
                ->itemLabel(fn (array $state): ?string => $state['major'] ?? null)
                    ->deletable(false)
                    ->collapsible()
                    ->columns(6)
                    ->schema([
                        TextInput::make('major')
                            ->label('التخصص')
                            ->disabled(),
                        TextInput::make('number_of_seats')
                            ->label('عدد المقاعد')
                            ->numeric()
                            ->prefixIcon('heroicon-o-user'),
                        TextInput::make('admission_rate')
                            ->label('نسبة القبول')
                            ->numeric()
                            ->prefix('%'),

                        TextInput::make('study_years')
                            ->label('عدد سنوات الدراسة')
                            ->numeric()
                            ->prefix('سنوات'),

                        TextInput::make('tuition_fee')
                            ->label('الرسوم الدراسية')
                            ->numeric()
                            ->prefix('دولار'),

                        Toggle::make('published')
                            ->label('نشر التخصص')
                            ->default(false)
                            ->inline(false)
                    ])
                    ->addable(false)
            ]),
    ])->addable(false)
    ->collapsible()
        ];
    }

    protected function getFormStatePath(): string
    {
        return 'data';
    }

    public function save(): void
    {
        $universityId = auth('university')->id();

foreach ($this->data['colleges'] as $college) {
    foreach ($college['majors'] as $item) {
        $um = UniversityMajor::where('id', $item['id'])
            ->where('university_id', $universityId)
            ->first();

        if ($um) {
            $um->update([
                'number_of_seats' => $item['number_of_seats'],
                'admission_rate' => $item['admission_rate'],
                'study_years' => $item['study_years'],
                'tuition_fee' => $item['tuition_fee'],
                'published' => $item['published'],
            ]);
        }
    }
}
        Notification::make()
            ->title('تم تحديث الشروط بنجاح')
            ->success()
            ->send();
    }
}
