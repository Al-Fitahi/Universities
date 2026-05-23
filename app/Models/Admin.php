<?php
namespace App\Models;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Filament\Auth\MultiFactor\Email\Contracts\HasEmailAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
// use Illuminate\Database\Eloquent\Concerns\HasUlids;
use App\Models\Traits\HasPublicId;





class Admin extends Authenticatable implements FilamentUser, HasEmailAuthentication, HasAppAuthentication
{
    use HasFactory,Notifiable;


    protected $fillable = [
        'name',
        'email',
                'password',

        'has_email_authentication'
    ];



    protected $hidden = [
        'password',
        'remember_token',
        'app_authentication_secret',
        'id'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'has_email_authentication' =>'boolean',
        'app_authentication_secret' =>'encrypted'
    ];

    public function HasEmailAuthentication():bool
    {
      return $this->has_email_authentication;
    }

    public function toggleEmailAuthentication(bool $condetion):void
    {
      $this->has_email_authentication=$condetion;
      $this->save();
    }

    public function canAccessPanel(Panel $panel): bool
    {
      return $this->email=='Jihad@gmail.com';
    }


    public function getAppAuthenticationSecret(): ?string
    {
          return $this->app_authentication_secret;
    }

    public function saveAppAuthenticationSecret(?string $secret): void
    {
        $this->app_authentication_secret=$secret;
        $this->save();
    }

    public function getAppAuthenticationHolderName(): string
    {
                return $this->email;
    }
}

