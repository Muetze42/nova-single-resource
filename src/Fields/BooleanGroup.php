<?php

namespace NormanHuth\SingleResource\Fields;

use Laravel\Nova\Fields\BooleanGroup as Field;
use NormanHuth\SingleResource\Traits\FieldTrait;

class BooleanGroup extends Field
{
    use FieldTrait;

    protected string $cast = 'array';
}
