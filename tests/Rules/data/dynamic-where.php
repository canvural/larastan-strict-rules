<?php
declare(strict_types=1);

namespace DynamicWhere;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

class ModelWithPivotWhereClauses extends Model
{
    /** @return BelongsToMany<Foo, $this> */
    public function fooWherePivot(): BelongsToMany
    {
        return $this->belongsToMany(Foo::class)->wherePivot('pivot_field', 1);
    }

    /** @return BelongsToMany<Foo, $this> */
    public function fooWherePivotBetween(): BelongsToMany
    {
        return $this->belongsToMany(Foo::class)->wherePivotBetween('pivot_field', [1, 2]);
    }

    /** @return BelongsToMany<Foo, $this> */
    public function fooWherePivotNotBetween(): BelongsToMany
    {
        return $this->belongsToMany(Foo::class)->wherePivotNotBetween('pivot_field', [1, 2]);
    }

    /** @return BelongsToMany<Foo, $this> */
    public function fooWherePivotIn(): BelongsToMany
    {
        return $this->belongsToMany(Foo::class)->wherePivotIn('pivot_field', [1, 2]);
    }

    /** @return BelongsToMany<Foo, $this> */
    public function fooWithPivotValue(): BelongsToMany
    {
        return $this->belongsToMany(Foo::class)->withPivotValue('pivot_field', 1);
    }

    /** @return BelongsToMany<Foo, $this> */
    public function fooWherePivotNotIn(): BelongsToMany
    {
        return $this->belongsToMany(Foo::class)->wherePivotNotIn('pivot_field', [1, 2]);
    }

    /** @return BelongsToMany<Foo, $this> */
    public function fooWherePivotNull(): BelongsToMany
    {
        return $this->belongsToMany(Foo::class)->wherePivotNull('pivot_field');
    }

    /** @return BelongsToMany<Foo, $this> */
    public function fooWherePivotNotNull(): BelongsToMany
    {
        return $this->belongsToMany(Foo::class)->wherePivotNotNull('pivot_field');
    }
}

class ModelWithScope extends Model
{
    public function testScope()
    {
        return $this->whereFooBar();
    }

    /**
     * @param Builder<Foo> $query
     * @phpstan-return Builder<Foo>
     */
    public function scopeWhereFooBar($query): Builder
    {
        return $query->where('foo', 'bar');
    }
}

class Account extends Model
{
    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<AccountAction, $this> */
    public function actions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AccountAction::class);
    }

    public function hasActiveActions(): bool
    {
        if (rand(0, 100) > 40) {
            return $this->actions()->whereIsActive()->exists();
        }

        return $this->actions()->whereActive()->exists();
    }
}

class AccountAction extends Model
{
    /**
     * @param Builder<AccountAction> $query
     *
     * @return Builder<AccountAction>
     */
    public function scopeWhereActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}

/** @extends Builder<AccountAction> */
class SomeBuilder extends Builder
{
    public function doFoo(): void
    {
        $this->whereActive();
    }
}

function calledOnBaseModel(Model $model)
{
    $model->whereFoo();
}

class ModelWithScopeAttribute extends Model
{
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function whereActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    // Laravel does not treat private methods as scopes
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    private function wherePrivate(Builder $query): void
    {
        $query->where('is_private', true);
    }

    protected function whereWithoutAttribute(Builder $query): void
    {
        $query->where('foo', 'bar');
    }
}

class ModelRelatedToScopeAttribute extends Model
{
    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<ModelWithScopeAttribute, $this> */
    public function related(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ModelWithScopeAttribute::class);
    }

    public function doFoo(): void
    {
        ModelWithScopeAttribute::query()->whereActive();
        $this->related()->whereActive();
    }
}

/** @extends Builder<ModelWithScopeAttribute> */
class ScopeAttributeBuilder extends Builder
{
    public function doFoo(): void
    {
        $this->whereActive();
        $this->wherePrivate();
        $this->whereWithoutAttribute();
    }
}

trait HasScopeAttribute
{
    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function whereFromTrait(Builder $query): void
    {
        $query->where('foo', 'bar');
    }
}

class ModelWithScopeAttributeFromTrait extends Model
{
    use HasScopeAttribute;
}

/** @extends Builder<ModelWithScopeAttributeFromTrait> */
class ScopeAttributeFromTraitBuilder extends Builder
{
    public function doFoo(): void
    {
        $this->whereFromTrait();
    }
}
