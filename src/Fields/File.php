<?php

namespace NormanHuth\SingleResource\Fields;

use Laravel\Nova\Fields\File as Field;
use NormanHuth\SingleResource\Traits\FieldTrait;
use NormanHuth\SingleResource\Traits\FileFieldTrait;

class File extends Field
{
    use FieldTrait, FileFieldTrait;
}
