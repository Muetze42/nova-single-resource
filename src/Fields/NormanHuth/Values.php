<?php

namespace NormanHuth\SingleResource\Fields\NormanHuth;

use NormanHuth\SingleResource\Traits\FieldTrait;
use NormanHuth\Values\Values as Field;

class Values extends Field
{
    use FieldTrait;

    protected string $cast = 'array';
}
