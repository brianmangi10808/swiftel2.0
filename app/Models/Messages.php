<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Messages extends Model
{
    use HasFactory;
    
    protected $table = 'messages';
    
    protected $fillable = [
         'company_id',
        'recipient',
        'message_body',
        'channel',
        'status',
        'scheduled_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    /**
     * Get the customer that owns the message
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'recipient', 'username');
    }
    public function company()
{
    return $this->belongsTo(\App\Models\Company::class);
}

}