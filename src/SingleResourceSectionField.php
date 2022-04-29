<?php

namespace NormanHuth\SingleResource;

use Laravel\Nova\Fields\Text;

class SingleResourceSectionField extends Text
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'single-resource-section-field';

    public function icon(?string $heroicon): static
    {
        if ($heroicon) {
            $this->withMeta(['icon' => $heroicon]);
        }

        return $this;
    }

    public function faIcon(?string $fontAwesomeClass): static
    {
        if ($fontAwesomeClass) {
            $this->withMeta(['faIcon' => $fontAwesomeClass]);
        }

        return $this;
    }
}
