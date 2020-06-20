<?php

namespace LocalQueryScopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Foo extends Model
{
    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeFoo($query)
    {
        return $query->where('foo', 1);
    }

    public function scopeBar(Builder $query) : Builder
    {
        return $query->where('bar', 1);
    }

    public function scopeWithoutParameter() : string
    {
        return 'foo';
    }
}