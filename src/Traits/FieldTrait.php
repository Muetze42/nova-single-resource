<?php

namespace NormanHuth\SingleResource\Traits;

use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Illuminate\Support\Collection as BaseCollection;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

trait FieldTrait
{
    use HasAttributes;

    protected string $valueColumn;
    protected string $keyColumn;
    protected mixed $currentModel;

    /**
     * Resolve the field's value.
     *
     * @param mixed $resource
     * @param string|null $attribute
     * @return void
     */
    public function resolve($resource, $attribute = null): void
    {
        $this->resource = $resource;

        $attribute = $attribute ?? $this->attribute;

        if ($attribute === 'ComputedField') {
            $this->value = call_user_func($this->computedCallback, $resource);
            return;
        }

        $this->currentModel = $resource;
        $this->keyColumn = !empty($resource::$keyColumn) ? $resource::$keyColumn : 'key';
        $this->valueColumn = !empty($resource::$valueColumn) ? $resource::$valueColumn : 'value';
        $model = $resource::where($this->keyColumn, $attribute)->first();
        $modelAttribute = $model ? $model->{$this->valueColumn} : null;

        if (!$this->resolveCallback) {
            $this->value = $this->castValue($modelAttribute);
        } elseif (is_callable($this->resolveCallback)) {
            tap($this->resolveAttribute($resource, $attribute), function ($value) use ($resource, $modelAttribute) {
                if (method_exists($this, 'fieldResolveAttribute')) {
                    $this->value = $this->fieldResolveAttribute($modelAttribute, $resource, $value);
                } else {
                    $this->value = call_user_func($this->resolveCallback, $value, $resource, $modelAttribute);
                }
            });
        }
    }

    /**
     * Cast values
     *
     * @param $attribute
     * @return bool|float|BaseCollection|int|mixed|void
     */
    protected function castValue($attribute)
    {
        if (empty($this->cast)) {
            $this->cast = null;
        }
        return match ($this->cast) {
            'bool', 'boolean' => (bool)$attribute,
            'int', 'integer' => (int)$attribute,
            'float', 'double' => $this->fromFloat($attribute),
            'object' => $this->fromJson($attribute, true),
            'array', 'json' => $this->fromJson($attribute),
            'collection' => new BaseCollection($this->fromJson($attribute)),
            default => $attribute,
        };
    }

    /**
     * Resolve the given attribute from the given resource.
     *
     * @param mixed $resource
     * @param string $attribute
     * @return mixed
     */
    protected function resolveAttribute($resource, $attribute): mixed
    {
        if (empty($this->valueColumn)) {
            $this->valueColumn = !empty($resource::$valueColumn) ? $resource::$valueColumn : 'value';
        }
        if (empty($this->keyColumn)) {
            $this->keyColumn = !empty($resource::$keyColumn) ? $resource::$keyColumn : 'key';
        }

        /**
         * Failed sometimes
        if (!empty($resource->{$this->valueColumn})) {
            return $resource->{$this->valueColumn};
        }
         **/

        $model = $resource::where($this->keyColumn, $attribute)->first();
        if ($model) {
            $modelAttribute = $model->{$this->valueColumn};
            return $this->castValue($modelAttribute);
        }

        return data_get($resource, str_replace('->', '.', $attribute));
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param NovaRequest $request
     * @param string $requestAttribute
     * @param object $model
     * @param string $attribute
     * @return void
     */
    protected function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute): void
    {
        if ($request->exists($requestAttribute)) {
            $value = $request[$requestAttribute];

            $this->currentModel::updateOrCreate(
                [$this->keyColumn => $requestAttribute],
                [$this->valueColumn => $this->isNullValue($value) ? null : $value],
            );
        }
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param NovaRequest $request
     * @param string $requestAttribute
     * @param object $model
     * @param string $attribute
     * @return mixed|void
     */
    protected function fillAttribute(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        if (isset($this->fieldResolveAttribute)) {
            return call_user_func($this->fieldResolveAttribute, $request, $model, $attribute, $requestAttribute);
        }

        $this->fillAttributeFromRequest($request, $requestAttribute, $model, $attribute);
    }

    /**
     * Specify that this field should be sortable.
     *
     * @param bool $value
     * @return Field
     */
    public function sortable($value = false): Field
    {
        /**
         * No sortable for single resources!
         */
        $this->sortable = false;

        return $this;
    }
}
