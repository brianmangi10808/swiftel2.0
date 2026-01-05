<?php

namespace App\Resolvers;

use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Contracts\PermissionsTeamResolver;

class CompanyTeamResolver implements PermissionsTeamResolver
{
    /**
     * Resolve current team (company) IDDS
     */
    public function getPermissionsTeamId(): int|string|null
    {
        $user = Auth::user();

        if (! $user) {
            return null;
        }

        // ✅ Option A: Super Admin = GLOBAL (no team scope)
        if ($user->is_super_admin === true) {
            return null;
        }

        // ✅ Normal users scoped to company
        return $user->company_id;
    }

    /**
     * ✅ MUST match Spatie v6 EXACT signature
     */
    public function setPermissionsTeamId($teamId): void
    {
        // Not required for your architecture
    }
}
