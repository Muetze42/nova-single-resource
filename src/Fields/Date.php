<?php

namespace NormanHuth\SingleResource\Fields;

use Illuminate\Support\Carbon;
use Laravel\Nova\Fields\Date as Field;
use NormanHuth\SingleResource\Traits\FieldTrait;

class Date extends Field
{
    use FieldTrait;

    protected string $cast = 'date';
}
