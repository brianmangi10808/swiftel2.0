<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Premise extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
        'available',
    ];

    protected $casts = [
        'available' => 'boolean',
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class, 'premise_id');
    }
}
