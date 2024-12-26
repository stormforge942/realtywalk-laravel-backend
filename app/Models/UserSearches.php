<?php

namespace App\Models;

use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserSearches extends Model
{
    public $table = 'user_searches';

    public $fillable = [
        'user_id',
        'search_name',
        'search_string_criteria'
    ];

    protected $casts = [
        'id'                        => 'integer',
        'user_id'                   => 'integer',
        'search_name'               => 'string',
        'search_string_criteria'    => 'string',
    ];

    public static $rules = [
        'user_id'               => 'required',
        'search_name'           => 'required|max:100',
        'search_string_criteria'=> 'nullable|json',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
