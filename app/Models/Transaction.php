<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_number',
        'order_id',
        'staff_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'amount',
        'payment_method',
        'payment_status',
        'payment_proof',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function getRouteKeyName()
    {
        return 'transaction_number';
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function getPaymentMethodBadgeAttribute(): string
    {
        $badges = [
            'cash'          => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300',
            'bank_transfer' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300',
            'pos'           => 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300',
            'other'         => 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300',
        ];

        return $badges[$this->payment_method] ?? 'bg-slate-100 text-slate-800';
    }

    public function getPaymentStatusBadgeAttribute(): string
    {
        $badges = [
            'paid'     => 'bg-emerald-500 text-slate-950 font-bold',
            'unpaid'   => 'bg-amber-400 text-slate-950 font-bold',
            'refunded' => 'bg-rose-500 text-white font-bold',
        ];

        return $badges[$this->payment_status] ?? 'bg-slate-500 text-white';
    }
}
