<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PermissionHelper
{
    /**
     * Check if the current user has permission for a specific action on a resource.
     * Pattern: {action}-{resource}
     */
    public static function can(string $action, string $resource): bool
    {
        /** @var User $user */
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        // Admin override: Super Admin and Admin bypass permission checks
        if (static::hasSuperAdminAccess($user)) {
            return true;
        }

        $permissionName = "{$action}-{$resource}";
        
        // Check if the user's role has the designated permission
        if (!$user->relationLoaded('role')) {
            $user->load('role.permissions');
        }

        return $user->role?->permissions->contains('name', $permissionName) ?? false;
    }

    /**
     * Check if a user has administrative access.
     */
    public static function hasSuperAdminAccess($user): bool
    {
        if (!$user) {
            return false;
        }

        // Fallback: Check for default admin email if roles are not yet configured
        if ($user->email === 'admin@admin.com') {
            return true;
        }

        if (!$user->role) {
            return false;
        }

        return in_array($user->role->name, ['Super Admin', 'Admin']);
    }

    /**
     * Check if a user is specifically a Super Admin.
     */
    public static function isSuperAdmin($user): bool
    {
        if (!$user || !$user->role) {
            return false;
        }

        return $user->role->name === 'Super Admin';
    }
}
