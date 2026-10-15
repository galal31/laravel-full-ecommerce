<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';
    protected $fillable = [
        'order_id',
        'transaction_id',
        'invoice_id',
        'payment_method',
        'invoice_expires_at',
    ];

    protected function casts(): array
    {
        return ['invoice_expires_at' => 'datetime'];
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
