<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;
class Dashboard extends BaseDashboard
{
    public function getHeading(): string
    {
        $hour = now()->hour;
        $greeting = match(true) {
            $hour >= 0 && $hour < 12 => 'Good Morning',
            $hour >= 12 && $hour < 17 => 'Good Afternoon',
            $hour >= 17 && $hour < 21 => 'Good Evening',
            default => 'Good Night',
        };

        $userName = Auth::user()->name ?? 'User';
        
        return "{$greeting}, {$userName}";
    }
    
    public function getSubheading(): ?string
    {
        return 'Welcome to Swiftel • ' . now()->format('l, F j, Y');
    }

    // Hide dashboard from navigation if user can't see any widgets
    public static function shouldRegisterNavigation(): bool
    {
        $user =  Auth::user();
        
        // Super admin always sees dashboard
        if ($user->is_super_admin) {
            return true;
        }

        // Check if user can view any widgets
        $canViewAnyWidget = $user->can('read customers')
            || $user->can('read payments')
            || $user->can('read services');

        return $canViewAnyWidget;
    }

    // Also hide the page itself if user shouldn't access it
    public static function canAccess(): bool
    {
        return static::shouldRegisterNavigation();
    }
}