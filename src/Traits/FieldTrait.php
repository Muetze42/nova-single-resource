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
    protected ?string $castColumn = null;

    /**
     * Resolve the field's value.
     *
     * @param mixed $resource
     * @param string|null $attribute
     * @return void
     */
    public function resolve($resource, $attribute = null): void
    {
        $this->castColumn = $attribute;
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
                    $this->value = $this->fieldResolveAttribute($modelAttribute, $value, $resource, $modelAttribute);
                } else {
                    $this->value = call_user_func($this->resolveCallback, $value, $resource, $modelAttribute);
                }
            });
        }
    }

    /**
     * Get the current connection name for the model.
     *
     * @return string|null
     */
    public function getConnection(): ?string
    {
        return 'mysql';
    }

    /**
     * Cast values
     *
     * @param $value
     * @return bool|BaseCollection|int|mixed|string|null|void|array
     */
    protected function castValue($value)
    {
        if (empty($this->cast)) {
            return $value;
        }

        return $this->castAttribute($this->cast, $value);
    }

    /**
     * Get the casts array.
     *
     * @return array
     */
    public function getCasts(): array
    {
        return [$this->castColumn => $this->cast];
    }

    /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return bool
     */
    public function getIncrementing(): bool
    {
        return false;
    }

    /**
     * Get the format for database stored dates.
     *
     * @return string
     */
    public function getDateFormat(): string
    {
        return empty($this->dateFormat) ? 'Y-m-d H:i:s' : $this->dateFormat;
    }

    /**
     * Get the type of cast for a model attribute.
     *
     * @param string $key
     * @return string
     */
    protected function getCastType($key): string
    {
        if ($this->isCustomDateTimeCast($this->cast)) {
            return 'custom_datetime';
        }

        if ($this->isImmutableCustomDateTimeCast($this->cast)) {
            return 'immutable_custom_datetime';
        }

        if ($this->isDecimalCast($this->cast)) {
            return 'decimal';
        }

        return trim(strtolower($this->cast));
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
         * if (!empty($resource->{$this->valueColumn})) {
         * return $resource->{$this->valueColumn};
         * }
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
            $value = $this->isNullValue($value) ? null : $value;

            if (!empty($this->cast) && !is_null($value) && $this->isEncryptedCastable($this->cast)) {
                $value = $this->castAttributeAsEncryptedString($this->cast, $value);
            }

            $this->currentModel::updateOrCreate(
                [$this->keyColumn => $requestAttribute],
                [$this->valueColumn => $value],
            );
        }
    }

    /**
     * Determine whether a value is an encrypted castable for inbound manipulation.
     *
     * @param  string  $key
     * @return bool
     */
    protected function isEncryptedCastable($key): bool
    {
        return in_array($key, ['encrypted', 'encrypted:array', 'encrypted:collection', 'encrypted:json', 'encrypted:object']);
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param NovaRequest $request
     * @param string $requestAttribute
     * @param object $model
     * @param string $attribute
     */
    protected function fillAttribute(NovaRequest $request, $requestAttribute, $model, $attribute): void
    {
        if (method_exists($this, 'fieldFillAttribute')) {
            $this->fieldFillAttribute($request, $requestAttribute, $model, $attribute);
            return;
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

    /**
     * Cast this field
     *
     * @param string $cast
     * @return Field
     */
    public function cast(string $cast): Field
    {
        $this->cast = $cast;

        return $this;
    }
}
