<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PppoeTraffic extends Model
{
    public $timestamps = false;

    protected $table = 'pppoe_traffic'; // ← REQUIRED

    protected $fillable = [
        'interface',
        'upload_mbps',
        'download_mbps',
        'logged_at',
    ];
}
