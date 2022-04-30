<?php

namespace NormanHuth\SingleResource\Traits;

use Laravel\Nova\Http\Requests\NovaRequest;

trait FileFieldTrait
{
    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param NovaRequest $request
     * @param string $requestAttribute
     * @param object $model
     * @param string $attribute
     * @return Closure|void|mixed
     */
    protected function fieldFillAttribute(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        if (is_null($file = $request->file($requestAttribute)) || !$file->isValid()) {
            return;
        }

        $hasExistingFile = !is_null($this->getStoragePath());

        $result = call_user_func(
            $this->storageCallback,
            $request,
            $model,
            $attribute,
            $requestAttribute,
            $this->getStorageDisk(),
            $this->getStorageDir()
        );

        if ($result === true) {
            return;
        }

        if ($result instanceof Closure) {
            return $result;
        }

        if (!is_array($result)) {
            return $this->currentModel::updateOrCreate(
                [$this->keyColumn => $requestAttribute],
                [$this->valueColumn => $result]
            );
        }

        foreach ($result as $key => $value) {
            $this->currentModel::updateOrCreate(
                [$this->keyColumn => $requestAttribute],
                [$this->valueColumn => $value]
            );
        }

        if ($this->isPrunable() && $hasExistingFile) {
            return function () use ($model, $request) {
                call_user_func(
                    $this->deleteCallback,
                    $request,
                    $model,
                    $this->getStorageDisk(),
                    $this->getStoragePath()
                );
            };
        }
    }
}
