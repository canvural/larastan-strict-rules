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

class WithScopeAttribute extends Model
{
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function active(Builder $query): void
    {
        $query->where('active', 1);
    }

    // Laravel does not treat private methods as scopes
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    private function privateMethod(Builder $query): void
    {
        $query->where('active', 1);
    }

    protected function withoutAttribute(Builder $query): void
    {
        $query->where('active', 1);
    }
}
