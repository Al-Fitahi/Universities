<?php

namespace App\Filament\Resources\Universities\Pages;

use App\Filament\Resources\Universities\UniversityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\University;
class ListUniversities extends ListRecords
{
    protected static string $resource = UniversityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }


        public function getTabs(): array
    {
                $countsQuery = University::query()
                        ->selectRaw("COUNT(*) as all_count")
                        ->selectRaw("COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending")
                        ->selectRaw("COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved")
                        ->selectRaw("COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected")
                        ->first();

          return [
              Tab::make('All')
                  ->badge(function () use ($countsQuery) { return $countsQuery->all_count; })
                  ->modifyQueryUsing(function (Builder $query) { return $query; }),

              Tab::make('pending')
                  ->badge(function () use ($countsQuery) { return $countsQuery->pending; })
                  ->modifyQueryUsing(function (Builder $query) { return $query->where('status', 'pending'); }),


              Tab::make('approved')
                  ->badge(function () use ($countsQuery) { return $countsQuery->approved; })
                  ->modifyQueryUsing(function (Builder $query) { return $query->where('status', 'approved'); }),


              Tab::make('rejected')
                  ->badge(function () use ($countsQuery) { return $countsQuery->rejected; })
                  ->modifyQueryUsing(function (Builder $query) { return $query->where('status', 'rejected'); }),
          ];
    }

        public function getDefaultActiveTab(): string|int|null
        {
          return 'all';
        }

}
