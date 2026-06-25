<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class, 'created_by');
    }

    public function cashierShifts(): HasMany
    {
        return $this->hasMany(CashierShift::class, 'cashier_id');
    }

    public function adminChatbotLogs(): HasMany
    {
        return $this->hasMany(AdminChatbotLog::class);
    }

    public function buyerCart(): HasOne
    {
        return $this->hasOne(BuyerCart::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function confirmedOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'confirmed_by');
    }

    public function completedOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'completed_by');
    }

    public function cancelledOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'cancelled_by');
    }
}
