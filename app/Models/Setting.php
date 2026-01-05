<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $fillable = [
        'pppoe_expiry_reminder_times',
    ];

    protected $casts = [
        'pppoe_expiry_reminder_times' => 'array',
    ];

    /**
     * Return Carbon date windows for each selected reminder time.
     */
    public function reminderWindows(): array
    {
        $now = Carbon::now();
        $windows = [];

        foreach ($this->pppoe_expiry_reminder_times ?? [] as $key) {
            $windows[$key] = match ($key) {
                '7_days_before'   => [$now->copy()->addDays(7)->startOfDay(),   $now->copy()->addDays(7)->endOfDay()],
                '5_days_before'   => [$now->copy()->addDays(5)->startOfDay(),   $now->copy()->addDays(5)->endOfDay()],
                '4_days_before'   => [$now->copy()->addDays(4)->startOfDay(),   $now->copy()->addDays(4)->endOfDay()],
                '3_days_before'   => [$now->copy()->addDays(3)->startOfDay(),   $now->copy()->addDays(3)->endOfDay()],
                '2_days_before'   => [$now->copy()->addDays(2)->startOfDay(),   $now->copy()->addDays(2)->endOfDay()],
                '1_day_before'    => [$now->copy()->addDay()->startOfDay(),     $now->copy()->addDay()->endOfDay()],

                '12_hours_before' => [
                    $now->copy()->addHours(12)->subMinutes(30),
                    $now->copy()->addHours(12)->addMinutes(30),
                ],

                '6_hours_before' => [
                    $now->copy()->addHours(6)->subMinutes(30),
                    $now->copy()->addHours(6)->addMinutes(30),
                ],

                '4_hours_before' => [
                    $now->copy()->addHours(4)->subMinutes(30),
                    $now->copy()->addHours(4)->addMinutes(30),
                ],

                default => null
            };
        }

        return $windows;
    }
}

