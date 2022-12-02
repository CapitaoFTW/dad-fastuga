<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'phone',
        'points',
        'nif',
        'default_payment_type',
        'default_payment_reference',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    /*public function assigedTasks()
    {
        return $this->belongsToMany(Task::class, 'task_user');
    }

    // Relations that return only a subset of the tasks

    // Owner and Completed
    public function tasksCompleted()
    {
        return $this->hasMany(Task::class, 'owner_id')->where('completed', 1);
    }

    // Owner and NOT Completed
    public function tasksNotCompleted()
    {
        return $this->hasMany(Task::class, 'owner_id')->where('completed', 0);
    }

    // Assigned and Completed
    public function assigedTasksCompleted()
    {
        return $this->belongsToMany(Task::class, 'task_user')->where('completed', 1);
    }

    // Assigned and Not Completed
    public function assigedTasksNotCompleted()
    {
        return $this->belongsToMany(Task::class, 'task_user')->where('completed', 0);
    }*/
}
