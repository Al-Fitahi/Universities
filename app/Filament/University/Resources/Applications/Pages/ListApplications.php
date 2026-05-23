<?php

namespace App\Filament\University\Resources\Applications\Pages;

use App\Filament\University\Resources\Applications\ApplicationResource;
use App\Models\Application;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListApplications extends ListRecords
{
    protected static string $resource = ApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
 public function getTabs(): array
    {
  $countsQuery = Application::whereHas('universityMajor', fn($q) => $q->where('university_id', Auth::id()))
    ->selectRaw("COUNT(*) as all_count")
    ->selectRaw("COUNT(CASE WHEN status = 'processing' THEN 1 END) as processing")
    ->selectRaw("COUNT(CASE WHEN status = 'accepted' THEN 1 END) as accepted")
    ->selectRaw("COUNT(CASE WHEN status = 'registered' THEN 1 END) as registered")
    ->selectRaw("COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected")
    ->selectRaw("COUNT(CASE WHEN status = 'canceled' THEN 1 END) as canceled")
    ->first();

        return [
              Tab::make('All')
                    ->badge(fn() => $countsQuery->all_count)
                    ->modifyQueryUsing(fn (Builder $query) => $query),

              Tab::make('Processing')
                    ->badge(fn() => $countsQuery->processing)
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'processing')),


              Tab::make('accepted')
                    ->badge(fn() => $countsQuery->accepted)
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'accepted')),


              Tab::make('registered')
                    ->badge(fn() => $countsQuery->registered)
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'registered')),


              Tab::make('rejected')
                    ->badge(fn() => $countsQuery->rejected)
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'rejected')),


              Tab::make('canceled')
                    ->badge(fn() => $countsQuery->canceled)
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'canceled'))
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'all';
    }
}


