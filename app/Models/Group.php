<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $table = 'group'; // note: singular table name
    protected $fillable = [
        'name',
        'description',
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class, 'group_id');
    }
}
