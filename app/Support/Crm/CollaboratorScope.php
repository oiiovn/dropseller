<?php

namespace App\Support\Crm;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class CollaboratorScope
{
    public const PACKAGING_ORDER_STATUSES = [
        'waiting_delivery',
        'packaged',
        'delivering',
        'delivered',
    ];

    public static function affiliateId(User $user): ?int
    {
        return $user->affiliate_id;
    }

    public static function isCollaborator(User $user): bool
    {
        return $user->hasCrmRole('collaborator');
    }

    public static function isPackaging(User $user): bool
    {
        return $user->hasCrmRole('packaging');
    }

    public static function applyAffiliateScope(Builder $query, User $user, string $column = 'affiliate_id'): Builder
    {
        if (self::isCollaborator($user) && self::affiliateId($user)) {
            $query->where($column, self::affiliateId($user));
        }

        if (self::isPackaging($user)) {
            $query->whereIn('order_status', self::PACKAGING_ORDER_STATUSES);
        }

        return $query;
    }

    public static function applyOrderScope(Builder $query, User $user, string $relation = 'order'): Builder
    {
        if (self::isCollaborator($user) && self::affiliateId($user)) {
            $query->whereHas($relation, function (Builder $builder) use ($user) {
                $builder->where('affiliate_id', self::affiliateId($user));
            });
        }

        return $query;
    }

    public static function enforceAffiliateId(User $user, ?int $affiliateId): int
    {
        if (self::isCollaborator($user)) {
            if (! self::affiliateId($user)) {
                abort(422, 'Tài khoản CTV chưa được gán affiliate_id.');
            }

            return (int) self::affiliateId($user);
        }

        if (! $affiliateId) {
            abort(422, 'affiliate_id là bắt buộc.');
        }

        return (int) $affiliateId;
    }
}
