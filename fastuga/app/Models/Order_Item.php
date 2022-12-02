<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order_Item extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'order_local_number',
        'product_id',
        'status',
        'price',
        'preparation_by',
        'notes',
    ];

    public function getStatusNameAttribute()
    {
        switch ($this->status) {
            case 'W':
                return 'Waiting';
            case 'P':
                return 'Preparing';
            case 'R':
                return 'Ready';
        }
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function chef()
    {
        return $this->belongsTo(User::class, 'preparation_by');
    }
}
