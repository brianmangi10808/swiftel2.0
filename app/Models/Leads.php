<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leads extends Model
{
    
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'mobile_number',
        'sector_id',


    ];
    protected $casts = [
        'created_at'=>'datetime'
    ];
       public function sector()
    {
        return $this->belongsTo(\App\Models\Sector::class, 'sector_id');
    }

    public function service()
    {
        return $this->belongsTo(\App\Models\Service::class);
    }

    public function group()
    {
        return $this->belongsTo(\App\Models\Group::class);
    }

    public function premise()
    {
        return $this->belongsTo(\App\Models\Premise::class);
    }
}
