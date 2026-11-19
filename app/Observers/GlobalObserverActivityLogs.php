<?php

namespace App\Observers;

use App\Support\ActivityLogger;

class GlobalObserverActivityLogs
{
    protected function getFeature($model): string
    {
        try {
            if (property_exists($model, 'featureName') && ! empty($model->featureName)) {
                return $model->featureName;
            }
        } catch (\Throwable $e) {
            // Abaikan properti yang tidak dapat diakses dari luar model.
        }

        return class_basename($model);
    }

    protected function getDescription($model, string $event): string
    {
        $description = class_basename($model)." {$event}";

        if (auth()->check() && auth()->user()->name) {
            $description .= ' by '.auth()->user()->name;
        }

        return $description;
    }

    /**
     * CREATED
     */
    public function created($model): void
    {
        ActivityLogger::record(
            'created',
            $this->getDescription($model, 'created'),
            [
                'new' => ActivityLogger::redact($model->getAttributes()),
            ],
            $model,
            $this->getFeature($model)
        );
    }

    /**
     * UPDATED
     */
    public function updated($model): void
    {
        $changes = $model->getChanges();
        unset($changes['updated_at']);

        // Jangan catat log jika tidak ada perubahan berarti (mis. hanya touch()).
        if (empty($changes)) {
            return;
        }

        $old = array_intersect_key($model->getOriginal(), $changes);

        ActivityLogger::record(
            'updated',
            $this->getDescription($model, 'updated'),
            [
                'old' => ActivityLogger::redact($old),
                'new' => ActivityLogger::redact($changes),
            ],
            $model,
            $this->getFeature($model)
        );
    }

    /**
     * DELETED
     */
    public function deleted($model): void
    {
        ActivityLogger::record(
            'deleted',
            $this->getDescription($model, 'deleted'),
            [
                'old' => ActivityLogger::redact($model->getOriginal()),
            ],
            $model,
            $this->getFeature($model)
        );
    }
}
