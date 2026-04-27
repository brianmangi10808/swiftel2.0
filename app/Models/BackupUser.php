<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupUser extends Model
{
    protected $connection = 'mysql';   
    protected $table = 'users';         

 protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'is_super_admin',
        'email_verified_at',
        'otp',
        'otp_expires_at',
    ];


}


