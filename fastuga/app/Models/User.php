<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// Remove Sanctum Tokens
//use Laravel\Sanctum\HasApiTokens;
// Add Laravel Tokens
use Laravel\Passport\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'photo_url',
        'type',
        'blocked',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isManager() {
        return $this->type == 'EM';
    }

    public function isChef() {
        return $this->type == 'EC';
    }

    public function isEmployee() {
        return $this->type == 'EC' || $this->type == 'ED' || $this->type == 'EM';
    }

    public function isCustomer() {
        return $this->type == 'C';
    }

    public function isBlocked() {
        return $this->blocked == 1;
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'delivered_by');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'preparation_by');
    }
}
