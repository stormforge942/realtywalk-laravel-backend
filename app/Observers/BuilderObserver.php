<?php

namespace App\Observers;

use App\Models\Builder;
use App\Models\Property;

class BuilderObserver
{
    public function updated(Builder $builder): void
    {
        // unset the houses related to alias of builder id
        if ($builder->getOriginal('alias_of_builder_id') && is_null($builder->alias_of_builder_id)) {
            Property::query()
                ->where('builder_id', $builder->getOriginal('alias_of_builder_id'))
                ->update([
                    'builder_id' => null
                ]);
        }
    }

    public function saved(Builder $builder): void
    {
        $builder->generateSEOURL();
    }

    public function deleting(Builder $builder): void
    {
        $builder->seourl()->delete();
    }
}
