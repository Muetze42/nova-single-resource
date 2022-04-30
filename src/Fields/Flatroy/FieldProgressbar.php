<?php

namespace NormanHuth\SingleResource\Fields\Flatroy;

use NormanHuth\SingleResource\Traits\FieldTrait;
use Flatroy\FieldProgressbar\FieldProgressbar as Field;

class FieldProgressbar extends Field
{
    use FieldTrait;

    /**
     * Indicates if the element should be shown on the update view.
     *
     * @var (callable(\Laravel\Nova\Http\Requests\NovaRequest, mixed):bool)|bool
     */
    public $showOnUpdate = false;

    /**
     * Indicates if the element should be shown on the creation view.
     *
     * @var (callable(\Laravel\Nova\Http\Requests\NovaRequest):bool)|bool
     */
    public $showOnCreation = true;
}
