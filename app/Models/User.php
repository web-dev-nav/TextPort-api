<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'email',
    'password',
    'sync_enabled',
    'is_admin',
    'activation_code',
    'activated_at',
    'device_id',
    'device_name',
    'device_model',
    'device_brand',
    'device_manufacturer',
    'android_version',
    'sdk_int',
    'device_hardware',
    'device_board',
    'device_product',
    'last_seen_at',
])]
#[Hidden(['password'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'sync_enabled' => 'boolean',
            'is_admin' => 'boolean',
            'activated_at' => 'datetime',
            'last_seen_at' => 'datetime',
        ];
    }
}
