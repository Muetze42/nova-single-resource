<?php

namespace NormanHuth\SingleResource\Fields;

use Illuminate\Support\Carbon;
use Laravel\Nova\Fields\Date as Field;
use NormanHuth\SingleResource\Traits\FieldTrait;

class Date extends Field
{
    use FieldTrait;

    protected function fieldResolveAttribute($attribute, $resource, $value)
    {
        $date = !$attribute ? $value : \Illuminate\Support\Facades\Date::instance(Carbon::createFromFormat('Y-m-d', $attribute)->startOfDay());
        return call_user_func($this->resolveCallback, $date, $resource, $attribute);
    }
}
