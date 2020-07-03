<?php

namespace PropertyMutator;

use Illuminate\Database\Eloquent\Model;

function getFooAttribute()
{
    return 'foo';
}

class Foo
{
    public function getFooAttribute(): string
    {
        return 'foo';
    }
}

class Bar extends Model
{
    public function getFooAttribute(): string
    {
        return 'foobar';
    }

    public function getBarAttribute($value): string
    {
        return $value . 'foobar';
    }
}