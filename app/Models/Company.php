<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
protected $connection = 'radius';

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
    
}
