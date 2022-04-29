<?php

namespace NormanHuth\SingleResource\Fields;

use Laravel\Nova\Fields\Currency as Field;
use NormanHuth\SingleResource\Traits\FieldTrait;

class Currency extends Field
{
    use FieldTrait;

    protected string $cast = 'float';
}
