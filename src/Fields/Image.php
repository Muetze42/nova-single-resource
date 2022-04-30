<?php

namespace NormanHuth\SingleResource\Fields;

use Laravel\Nova\Fields\Image as Field;
use NormanHuth\SingleResource\Traits\FieldTrait;
use NormanHuth\SingleResource\Traits\FileFieldTrait;

class Image extends Field
{
    use FieldTrait, FileFieldTrait;
}

