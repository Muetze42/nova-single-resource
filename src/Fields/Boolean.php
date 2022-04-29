<?php

namespace NormanHuth\SingleResource\Fields;

use Laravel\Nova\Fields\Boolean as Field;
use NormanHuth\SingleResource\Traits\FieldTrait;

class Boolean extends Field
{
    use FieldTrait;

    protected string $cast = 'bool';
}
