<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class AdminNotificationRecipient
{
    /**
     * Get all active Super Admin and Admin users.
     *
     * @param int|null $excludeUserId Optional user ID to exclude (e.g. current actor)
     * @return Collection<int, User>
     */
    public static function allActiveAdmins(?int $excludeUserId = null): Collection
    {
        return User::query()
            ->whereHas('roles', fn ($q) => $q->whereIn('name', ['Super Admin', 'Admin']))
            ->where('status', User::STATUS_ACTIVE)
            ->when($excludeUserId !== null, fn ($query) => $query->where('id', '!=', $excludeUserId))
            ->get();
    }
}
