<?php

namespace App\Support\Assets;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class AssetAccess
{
    private const IT_BLOCKED_CODES = ['MTR', 'MBL'];

    private const PPI_BLOCKED_CODES = ['NBK', 'HPN'];

    /**
     * Kode kategori yang dilarang berdasarkan role pengguna.
     */
    public static function blockedCategoryCodes(User $user): array
    {
        if ($user->hasRole('admin')) {
            return [];
        }

        $blocked = [];

        if ($user->hasRole('it')) {
            $blocked = array_merge(
                $blocked,
                self::IT_BLOCKED_CODES
            );
        }

        if ($user->hasRole('ppi')) {
            $blocked = array_merge(
                $blocked,
                self::PPI_BLOCKED_CODES
            );
        }

        return array_values(array_unique($blocked));
    }

    /**
     * Membatasi query aset berdasarkan role pengguna.
     *
     * Contoh:
     * AssetAccess::assetsQuery(auth()->user())
     */
    public static function assetsQuery(
        User $user,
        ?Builder $query = null
    ): Builder {
        $query ??= Asset::query();

        if ($user->hasRole('admin')) {
            return $query;
        }

        $hasRecognizedRole =
            $user->hasRole('it')
            || $user->hasRole('ppi')
            || $user->hasRole('viewer');

        // User tanpa role yang dikenali tidak mendapat data aset.
        if (! $hasRecognizedRole) {
            return $query->whereRaw('1 = 0');
        }

        $blockedCodes = self::blockedCategoryCodes($user);

        if (empty($blockedCodes)) {
            return $query;
        }

        return $query->whereDoesntHave(
            'category',
            function (Builder $categoryQuery) use ($blockedCodes) {
                $categoryQuery->whereIn('code', $blockedCodes);
            }
        );
    }

    /**
     * Memeriksa apakah pengguna boleh melihat suatu aset.
     */
    public static function canView(
        User $user,
        Asset $asset
    ): bool {
        if ($user->hasRole('admin')) {
            return true;
        }

        $hasRecognizedRole =
            $user->hasRole('it')
            || $user->hasRole('ppi')
            || $user->hasRole('viewer');

        if (! $hasRecognizedRole) {
            return false;
        }

        $asset->loadMissing('category');

        $categoryCode = $asset->category?->code;

        if (! $categoryCode) {
            return false;
        }

        return ! in_array(
            $categoryCode,
            self::blockedCategoryCodes($user),
            true
        );
    }

    /**
     * Memeriksa apakah role boleh mengelola kategori aset.
     *
     * Hak membuat, mengubah, dan menghapus tetap harus
     * diperiksa melalui permission yang sesuai.
     */
    public static function canManageCategory(
        User $user,
        ?string $categoryCode
    ): bool {
        if ($user->hasRole('admin')) {
            return true;
        }

        // Viewer hanya boleh melihat.
        if (
            ! $user->hasRole('it')
            && ! $user->hasRole('ppi')
        ) {
            return false;
        }

        if (! $categoryCode) {
            return false;
        }

        return ! in_array(
            $categoryCode,
            self::blockedCategoryCodes($user),
            true
        );
    }

    /**
     * Mengambil daftar kategori yang sesuai cakupan role.
     */
    public static function categoriesQueryFor(
        User $user
    ): Builder {
        $query = AssetCategory::query();

        if ($user->hasRole('admin')) {
            return $query;
        }

        $hasRecognizedRole =
            $user->hasRole('it')
            || $user->hasRole('ppi')
            || $user->hasRole('viewer');

        if (! $hasRecognizedRole) {
            return $query->whereRaw('1 = 0');
        }

        $blockedCodes = self::blockedCategoryCodes($user);

        if (! empty($blockedCodes)) {
            $query->whereNotIn('code', $blockedCodes);
        }

        return $query;
    }
}
