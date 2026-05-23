<?php

namespace App\Filament\University\Resources\Applications\Pages;

use App\Filament\University\Resources\Applications\ApplicationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewApplication extends ViewRecord
{
    protected static string $resource = ApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('accept')
                ->label('قبول')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('تأكيد قبول الطلب')
                ->modalDescription('هل أنت متأكد من قبول هذا الطلب؟')
                ->modalSubmitActionLabel('نعم، قبول')
                ->action(function (\App\Models\Application $record) {
                    try {
                        if ($record->changeStatus('accepted')) {
                            \Filament\Notifications\Notification::make()->success()->title('تم قبول الطلب')->send();

                            // Refresh the record on the page so infolist and buttons update without full page reload
                            $record->refresh();

                            // Safely trigger a component refresh if available
                            if (method_exists($this, 'emitSelf')) {
                                $this->emitSelf('refresh');
                            } elseif (method_exists($this, 'emit')) {
                                $this->emit('refresh');
                            } elseif (method_exists($this, 'refresh')) {
                                $this->refresh();
                            }

                        } else {
                            \Filament\Notifications\Notification::make()->danger()->title('تعذر تحديث الحالة')->send();
                        }
                    } catch (\Illuminate\Validation\ValidationException $e) {
                        $message = collect($e->errors())->flatten()->first() ?? 'خطأ في التحقق';
                        \Filament\Notifications\Notification::make()->danger()->title($message)->send();
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()->danger()->title($e->getMessage())->send();
                    }
                })
                ->visible(fn (\App\Models\Application $record) => $record->is_active && $record->status === 'processing'),

            \Filament\Actions\Action::make('register')
                ->label('تسجيل')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('تأكيد تسجيل الطالب')
                ->modalDescription('هل تريد تسجيل هذا الطالب؟')
                ->modalSubmitActionLabel('نعم، تسجيل')
                ->action(function (\App\Models\Application $record) {
                    try {
                        if ($record->changeStatus('registered')) {
                            \Filament\Notifications\Notification::make()->success()->title('تم تسجيل الطالب')->send();

                            $record->refresh();

                            // Safely trigger a component refresh if available
                            if (method_exists($this, 'emitSelf')) {
                                $this->emitSelf('refresh');
                            } elseif (method_exists($this, 'emit')) {
                                $this->emit('refresh');
                            } elseif (method_exists($this, 'refresh')) {
                                $this->refresh();
                            }

                        } else {
                            \Filament\Notifications\Notification::make()->danger()->title('تعذر تحديث الحالة')->send();
                        }
                    } catch (\Illuminate\Validation\ValidationException $e) {
                        $message = collect($e->errors())->flatten()->first() ?? 'خطأ في التحقق';
                        \Filament\Notifications\Notification::make()->danger()->title($message)->send();
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()->danger()->title($e->getMessage())->send();
                    }
                })
                ->visible(fn (\App\Models\Application $record) => $record->is_active && $record->status === 'accepted'),

            \Filament\Actions\Action::make('reject')
                ->label('رفض')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('تأكيد رفض الطلب')
                ->modalDescription('هل أنت متأكد من رفض هذا الطلب؟ لا يمكن التراجع عن هذا الإجراء بسهولة.')
                ->modalSubmitActionLabel('نعم، رفض')
                ->action(function (\App\Models\Application $record) {
                    try {
                        if ($record->changeStatus('rejected')) {
                            \Filament\Notifications\Notification::make()->success()->title('تم رفض الطلب')->send();

                            $record->refresh();

                            // Safely trigger a component refresh if available
                            if (method_exists($this, 'emitSelf')) {
                                $this->emitSelf('refresh');
                            } elseif (method_exists($this, 'emit')) {
                                $this->emit('refresh');
                            } elseif (method_exists($this, 'refresh')) {
                                $this->refresh();
                            }

                        } else {
                            \Filament\Notifications\Notification::make()->danger()->title('تعذر تحديث الحالة')->send();
                        }
                    } catch (\Illuminate\Validation\ValidationException $e) {
                        $message = collect($e->errors())->flatten()->first() ?? 'خطأ في التحقق';
                        \Filament\Notifications\Notification::make()->danger()->title($message)->send();
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()->danger()->title($e->getMessage())->send();
                    }
                })
                ->visible(fn (\App\Models\Application $record) => $record->is_active && $record->status === 'processing'),

            // EditAction::make(),
        ];
    }
}
