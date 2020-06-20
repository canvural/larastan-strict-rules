<?php
declare(strict_types=1);

namespace DynamicWhere;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Foo extends Model
{
    public function usingExistingWhereMethods()
    {
        $this->whereDate('created_at', '2018-01-02');

        return $this->whereBaz();
    }

    public function bar(): Foo
    {
        return $this->whereBar('baz');
    }

    public function whereBaz(): Foo
    {
        return $this;
    }
}

function outOfClassScope(Foo $foo)
{
    $foo->whereBar();
    $query = Foo::query();
    $query->whereDate('created_at', '2020-05-15')->whereBar();
}

class ModelWithCustomQueryBuilder extends Model
{
    /**
     * @param \Illuminate\Database\Query\Builder $query
     *
     * @return CustomEloquentBuilder<ModelWithCustomQueryBuilder>
     */
    public function newEloquentBuilder($query): CustomEloquentBuilder
    {
        return new CustomEloquentBuilder($query);
    }

    public function foo()
    {
        $this->whereBar('foo')->whereBaz();
    }
}

/**
 * @template TModelClass of ModelWithCustomQueryBuilder
 * @extends Builder<ModelWithCustomQueryBuilder>
 */
class CustomEloquentBuilder extends Builder
{
    /**
     * @param string $bar
     *
     * @return CustomEloquentBuilder
     * @phpstan-return CustomEloquentBuilder<ModelWithCustomQueryBuilder>
     */
    public function whereBar(string $bar): CustomEloquentBuilder
    {
        return $this->where('bar', $bar);
    }
}
