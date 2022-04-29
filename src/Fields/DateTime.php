<?php

namespace NormanHuth\SingleResource\Fields;

use Illuminate\Support\Facades\Date;
use Laravel\Nova\Fields\DateTime as Field;
use NormanHuth\SingleResource\Traits\FieldTrait;

class DateTime extends Field
{
    use FieldTrait;

    protected function fieldResolveAttribute($attribute, $resource, $value)
    {
        $date = !$attribute ? $value : Date::parse($attribute);
        return call_user_func($this->resolveCallback, $date, $resource, $attribute);
    }
}

