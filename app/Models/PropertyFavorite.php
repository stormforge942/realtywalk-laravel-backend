<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class PropertyFavorite extends Model
{
    public static function currentConnection(): string
    {
        return 'realty';
    }

    public static function isShared(): bool
    {
        return config('app.shared_favorite', false);
    }

    public static function getDBTable(): string
    {
        return self::isShared()
            ? config('database.favorites_table', 'property_favorites')
            : 'property_favorites';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public static function add(int $user_id, int $property_id, string $connection = 'realty'): void
    {
        if (!self::rowExists($user_id, $property_id)) {
            DB::transaction(function () use ($user_id, $property_id, $connection) {
                DB::table(self::getDBTable())->insert([
                    'connection' => $connection,
                    'user_id' => $user_id,
                    'property_id' => $property_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            });
        }
    }

    public static function remove(int $user_id, int $property_id, string $connection = 'realty'): void
    {
        if (self::rowExists($user_id, $property_id)) {
            DB::transaction(function () use ($user_id, $property_id, $connection) {
                DB::table(self::getDBTable())
                    ->where('connection', $connection)
                    ->where('user_id', $user_id)
                    ->where('property_id', $property_id)
                    ->delete();
            });
        }
    }

    public static function rowExists(int $user_id, int $property_id): bool
    {
        return DB::table(self::getDBTable())
            ->where('connection', self::currentConnection())
            ->where('user_id', $user_id)
            ->where('property_id', $property_id)
            ->exists();
    }
}
