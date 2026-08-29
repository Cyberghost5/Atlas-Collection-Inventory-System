<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Role Helper Methods
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }

    public function isStaff(): bool
    {
        return in_array($this->role, ['super_admin', 'admin', 'staff']);
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getLtvAttribute(): float
    {
        return (float) $this->orders()->where('status', '!=', 'cancelled')->sum('total_amount');
    }

    public function getAverageOrderValueAttribute(): float
    {
        $count = $this->orders()->where('status', '!=', 'cancelled')->count();
        return $count > 0 ? ($this->ltv / $count) : 0;
    }

    public function getVipBadgeAttribute(): array
    {
        $spent = $this->ltv;
        $orderCount = $this->orders()->where('status', '!=', 'cancelled')->count();

        if ($spent >= 500000 || $orderCount >= 10) {
            return [
                'tier' => 'VIP Platinum',
                'badge' => '👑 VIP Platinum',
                'class' => 'bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300 border border-purple-300 dark:border-purple-800 font-extrabold'
            ];
        }

        if ($spent >= 200000 || $orderCount >= 5) {
            return [
                'tier' => 'Gold Loyal',
                'badge' => '🥇 Gold VIP',
                'class' => 'bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-800 font-extrabold'
            ];
        }

        if ($spent >= 50000 || $orderCount >= 2) {
            return [
                'tier' => 'Silver VIP',
                'badge' => '🥈 Silver VIP',
                'class' => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700 font-bold'
            ];
        }

        return [
            'tier' => 'Regular Buyer',
            'badge' => '🛍️ Regular Buyer',
            'class' => 'bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-800 font-medium'
        ];
    }
}
