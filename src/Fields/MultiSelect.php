<?php

namespace NormanHuth\SingleResource\Fields;

use Laravel\Nova\Fields\MultiSelect as Field;
use NormanHuth\SingleResource\Traits\FieldTrait;

class MultiSelect extends Field
{
    use FieldTrait;

    protected string $cast = 'array';
}
