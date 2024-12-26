<?php

namespace App;

use App\Models\Builder;
use App\Models\Property;
use App\Models\PropertyFavorite;
use App\Models\UserSearches;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'picture',
        'email',
        'password',
        'last_login_at',
        'login_token',
        'login_token_created_at',
        'builder_id',
        'activation_token',
        'email_verified_at',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'picture_path',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'activation_token',
        'login_token',
        'login_token_created_at',
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'last_login_at'          => 'datetime',
        'email_verified_at'      => 'datetime',
        'login_token_created_at' => 'datetime',
        'email_verified_at'      => 'datetime',
    ];

    /**
     * Set the password hash from user input
     *
     * @param  string $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Append new attribute the picture path
     *
     * @return string
     */
    public function getPicturePathAttribute()
    {
        return $this->picture
            ? "https://" . env("WAS_BUCKET") . "." .env("WAS_URL") . "/uploads/" . $this->picture
            : null;
    }

    public function favorites()
    {
        if (PropertyFavorite::isShared()) {
            return $this->belongsToMany(Property::class, 'property_favorites', 'user_id', 'property_id')->withTimeStamps();
        }

        return $this->belongsToMany(Property::class, 'property_favorites', 'user_id', 'property_id')
            ->where('connection', PropertyFavorite::currentConnection())
            ->withTimestamps();
    }

    public function localFavorites()
    {
        return $this->belongsToMany(Property::class, 'property_favorites', 'user_id', 'property_id')
            ->where('connection', PropertyFavorite::currentConnection())
            ->withTimestamps();
    }

    public function getFavoritedProperties(): Collection
    {
        $is_shared = config('app.shared_favorite');
        $table = PropertyFavorite::getDBTable();
        $currentConnection = PropertyFavorite::currentConnection();

        if (!$is_shared) {
            $results = DB::select("
                SELECT
                    p.property_id as id,
                    p.list_data
                FROM $table pf
                JOIN property_results p ON p.property_id = pf.property_id AND pf.connection = '$currentConnection'
                WHERE pf.user_id = ?
                ORDER BY pf.created_at DESC;
            ", [$this->id]);

            return collect($results)->map(function ($row) {
                return collect([
                    'id' => $row->id,
                    'conn' => PropertyFavorite::currentConnection()
                ])->merge(json_decode($row->list_data ?: '', 1))->toArray();
            });
        }

        $rw_db_name = config('database.connections.mysql.database');
        $bps_db_name = config('database.connections.mysql_bps.database');
        $rw_property_table =  "$rw_db_name.property_results";
        $bps_property_table =  "$bps_db_name.property_results";

        $results = DB::select("
            SELECT
                pf.connection as conn,
                pf.property_id AS id,
                CASE
                    WHEN pf.connection = 'bps' THEN bp.list_data
                    WHEN pf.connection = '$currentConnection' THEN rp.list_data
                END AS list_data
            FROM $table pf
            LEFT JOIN $bps_property_table bp ON pf.property_id = bp.property_id AND pf.connection = 'bps'
            LEFT JOIN $rw_property_table rp ON pf.property_id = rp.property_id AND pf.connection = '$currentConnection'
            WHERE pf.user_id = ?
                AND (
                    (pf.connection = 'bps' AND bp.list_data IS NOT NULL)
                    OR
                    (pf.connection = '$currentConnection' AND rp.list_data IS NOT NULL)
                )
            ORDER BY pf.created_at DESC;
        ", [$this->id]);

        return collect($results)->map(function ($row) {
            return collect([
                'id' => $row->id,
                'conn' => $row->conn
            ])->merge(json_decode($row->list_data ?: '', 1))->map(function ($row) {
                // Update URLs
                $prefix = 'https://builderpostingservice.com';

                if ($row['conn'] !== PropertyFavorite::currentConnection()) {
                    // property url
                    $row['pu'] = complete_the_url($prefix, $row['pu'] ?? null);
                    // polygon url
                    $row['pp'] = complete_the_url($prefix, $row['pp'] ?? null);
                    // builder url
                    $row['bp'] = complete_the_url($prefix, $row['bp'] ?? null);
                }

                return $row;
            })->toArray();
        });
    }

    public function getSimpleFavoritedProperties(): array
    {
        return $this->getFavoritedProperties()->map(fn($row) => [
            'id' => $row['id'],
            'conn' => $row['conn'] ?? PropertyFavorite::currentConnection()
        ])->toArray();
    }

    public function builder()
    {
        return $this->belongsTo(Builder::class);
    }

    public function usersearches()
    {
        return $this->hasMany(UserSearches::class);
    }

    public function getPropertyFavoritesAttribute()
    {
        return $this->favorites->pluck('id')->all();
    }
}
