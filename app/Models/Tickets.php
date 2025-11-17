<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tickets extends Model
{
    use HasFactory;

    protected $table ='tickets';
    protected $fillable = [
        'customer_id',
        'description',

        'status',
   
        'severity',
        'comment',
        'created_at',
        'resolution_notes',
        'resolved_at'
    ];

    public function sector()
    {
        return $this->belongsTo(\App\Models\Sector::class, 'sector_id');
    }

        public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
    public function customer()
{
    return $this->belongsTo(Customer::class);
}

}
