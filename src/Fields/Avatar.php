<?php

namespace NormanHuth\SingleResource\Fields;

use Laravel\Nova\Fields\Avatar as Field;
use NormanHuth\SingleResource\Traits\FieldTrait;
use NormanHuth\SingleResource\Traits\FileFieldTrait;

class Avatar extends Field
{
    use FieldTrait, FileFieldTrait;
}

