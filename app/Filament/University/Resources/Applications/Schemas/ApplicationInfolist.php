<?php

namespace App\Filament\University\Resources\Applications\Schemas;

use Dotenv\Parser\Entry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ApplicationInfolist
{
  public static function configure(Schema $schema): Schema
  {
    return $schema
      ->components([
        Section::make('About Student')
          ->schema([
            Flex::make([
              Grid::make(2)
                ->schema([
                  Group::make([
                    TextEntry::make('student.full_name'),
                    TextEntry::make('student.graduation_date'),
                    TextEntry::make('created_at')
                      ->label('created_at')
                      ->badge()
                      ->date()
                      ->color('success'),
                    TextEntry::make('application_code'),
                    TextEntry::make('status')
                      ->badge()
                      ->color(fn ($state) => match ($state) {
                          'processing' => 'warning',
                          'accepted' => 'success',
                          'registered' => 'primary',
                          'rejected' => 'danger',
                          'canceled' => 'secondary',
                          default => 'secondary',
                      }),
                  ]),
                  Group::make([
                    TextEntry::make('student.phone_number'),
                    TextEntry::make('student.graduation_grade'),
                  ]),
                ]),
            ])->from('lg'),
          ]),
        Section::make('About Application')
          // ->description('Prevent abuse by d')
          ->icon(Heroicon::ShoppingBag)
          ->schema([
            Group::make([
              TextEntry::make('universityMajor.major.college.name')
                ->label('College')
                ->color('primary'),
              TextEntry::make('universityMajor.major.name')
                ->label('Major')
                ->color('success'),
              TextEntry::make('universityMajor.admission_rate')
                ->label('Rate')
                ->color('success'),
              TextEntry::make('universityMajor.study_years')
                ->label('study_years'),
              TextEntry::make('universityMajor.tuition_fee')
                ->label('tuition_fee'),
              TextEntry::make('universityMajor.number_of_seats')
                ->label('number_of_seats')
            ]),
          ]),

        Section::make()
          ->schema([
            ImageEntry::make('student.certificate_image')
              ->disk('public'),
          ]),
      ]);
  }
}
