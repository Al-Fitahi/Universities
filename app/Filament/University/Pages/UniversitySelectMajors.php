<?php

namespace App\Filament\University\Pages;

use App\Models\College;
use App\Models\UniversityMajor;
use BackedEnum;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class UniversitySelectMajors extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $title = 'اختيار الكليات والتخصصات';
    protected string $view = 'filament.university.pages.university-select-majors';
    protected static string|BackedEnum|null $navigationIcon = 'fas-university';

    public array $data = [];

    public function mount(): void
    {
        $universityId = Auth::guard('university')->id();
        $colleges = College::with('majors')->get();

        $state = [];
        foreach ($colleges as $college) {
            $selectedMajorIds = UniversityMajor::where('university_id', $universityId)
                ->whereIn('major_id', $college->majors->pluck('id'))
                ->pluck('major_id')
                ->toArray();

            $enabled = !empty($selectedMajorIds);

            $state['colleges'][$college->id] = [
                'enabled' => $enabled,
                'majors' => $selectedMajorIds,
            ];
        }

        $this->form->fill($state);
    }
protected function getFormSchema(): array
{
    return [
        Grid::make([
            'default' => 2,   // عمودين افتراضي
            'lg' => 3,        // 3 أعمدة في الشاشات الكبيرة
            'sm' => 1,        // عمود واحد في الموبايل
        ])
        ->schema(
            College::with('majors')->get()->map(function ($college) {
                return Section::make($college->name)
                    ->icon(Heroicon::ShoppingBag)
                    ->schema([
                        Toggle::make("colleges.{$college->id}.enabled")
                            ->label("تفعيل الكلية")
                            ->reactive(),
                        CheckboxList::make("colleges.{$college->id}.majors")
                            ->label('التخصصات')
                            ->options($college->majors->pluck('name', 'id'))
                            ->disabled(fn ($get) => ! $get("colleges.{$college->id}.enabled"))
                            ->bulkToggleable()
                    ])
                    ->columns(1)
                    ->extraAttributes([
                        'class' => 'p-4 my-1 rounded-lg border shadow-sm bg-white flex flex-col justify-between h-64'
                    ]);
            })->toArray()
        )
        ->extraAttributes([
            'class' => 'max-w-6xl mx-auto gap-4' // يصغر العرض ويخليها في المنتصف
        ]),
    ];
}







    protected function getFormStatePath(): string
    {
        return 'data';
    }

    public function save(): void
    {
        $universityId = Auth::guard('university')->id();

        foreach (College::with('majors')->get() as $college) {
            $collegeData = $this->data['colleges'][$college->id] ?? [];
            $enabled = $collegeData['enabled'] ?? false;
            $selectedMajorIds = $collegeData['majors'] ?? [];

            $existingMajorIds = UniversityMajor::where('university_id', $universityId)
                ->whereIn('major_id', $college->majors->pluck('id'))
                ->pluck('major_id')
                ->toArray();

            // احذف أي تخصص لم يعد محدد
            $toDelete = array_diff($existingMajorIds, $selectedMajorIds);
            if (!empty($toDelete)) {
                UniversityMajor::where('university_id', $universityId)
                    ->whereIn('major_id', $toDelete)
                    ->delete();
            }

            if ($enabled) {
                // اضف أو حدث التخصصات المحددة
                foreach ($selectedMajorIds as $majorId) {
                    UniversityMajor::updateOrCreate(
                        [
                            'university_id' => $universityId,
                            'major_id' => $majorId,
                        ],
                        []
                    );
                }
            } else {
                // لو الكلية غير مفعلة، احذف جميع تخصصاتها
                UniversityMajor::where('university_id', $universityId)
                    ->whereIn('major_id', $college->majors->pluck('id'))
                    ->delete();
            }
        }

        Notification::make()
            ->title('تم حفظ التخصصات بنجاح')
            ->success()
            ->send();
    }
}
