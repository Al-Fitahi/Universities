<?php

namespace App\Filament\University\Resources\SuperUniverMajors\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SuperUniverMajorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('supervisor.full_name')
                    ->label('المشرف'),
                TextEntry::make('supervisor.phone_number')
                    ->label('الهاتف'),
                TextEntry::make('universityMajor.major.college.name')
                    ->label('الكلية'),
                TextEntry::make('universityMajor.major.name')
                    ->label('التخصص'),
                TextEntry::make('created_at')
                    ->label('أضيف في')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->label('آخر تحديث')
                    ->dateTime(),
            ]);
    }
}
