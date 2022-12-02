<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'description',
        'photo_url',
        'price',
    ];

    public function order_items()
    {
        return $this->hasMany(Order_Item::class, 'product_id');
    }

    /*public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'task_user');
    }*/
}
