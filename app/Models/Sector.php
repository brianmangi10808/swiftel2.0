<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
   public function tickets()
    {
        return $this->hasMany(Tickets::class);
    }
       public function leads()
    {
        return $this->hasMany(Leads::class);
    }
}
