<?php

namespace NormanHuth\SingleResource\Fields;

use Illuminate\Support\Facades\Date;
use Laravel\Nova\Fields\DateTime as Field;
use NormanHuth\SingleResource\Traits\FieldTrait;

class DateTime extends Field
{
    use FieldTrait;

    protected string $cast = 'datetime';
}

