<?php

namespace NormanHuth\SingleResource\Fields;

use Laravel\Nova\Fields\KeyValue as Field;
use NormanHuth\SingleResource\Traits\FieldTrait;

class KeyValue extends Field
{
    use FieldTrait;

    protected string $cast = 'array';
}
