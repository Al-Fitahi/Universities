<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Facades\Filament;
use Illuminate\Auth\SessionGuard;
use Illuminate\Validation\ValidationException;

class LoginUniversity extends BaseLogin
{
    /** @return ?LoginResponse */
    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();

        /** @var SessionGuard $guard */
        $guard = Filament::auth();
        $provider = $guard->getProvider();

        $credentials = $this->getCredentialsFromFormData($data);
        $user = $provider->retrieveByCredentials($credentials);

        if ($user && $provider->validateCredentials($user, $credentials)) {
            if ($user->status === 'pending') {
                throw ValidationException::withMessages([
                    'data.email' => 'حسابك قيد المراجعة من الإدارة.',
                ]);
            }

            if ($user->status === 'rejected') {
                throw ValidationException::withMessages([
                    'data.email' => 'تم رفض طلبك من الإدارة.',
                ]);
            }
        }
        return parent::authenticate();
    }
}

