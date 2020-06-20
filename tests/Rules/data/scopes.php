<?php declare(strict_types=1);

namespace Scopes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Foo extends Model
{
    /**
     * @param Builder $query
     */
    public function scopeFoo($query)
    {
        $query->where('foo', 1);
    }

    public function scopeBar(Builder $query) : void
    {
        $query->where('bar', 1);
    }

    /**
     * @param Builder $query
     * @return void
     */
    public function scopeBaz($query)
    {
        $query->where('baz', 1);
    }


    /**
     * @param Builder $query
     */
    public function scopeBazz($query) : Foo
    {
        $query->where('baz', 1);
        return $this;
    }

    public function scopeRealMethod(string $foo) : bool
    {
        return true;
    }
}