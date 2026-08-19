<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class GlobalObserverActivityLogs
{
    protected function getFeature($model)
    {
        return property_exists($model, 'featureName')
            ? $model->featureName
            : class_basename($model);
    }

    protected function getCauser()
    {
        return Auth::check() ? Auth::user() : null;
    }

    protected function buildProperties($model, array $extra = [])
    {
        return array_merge([
            'ip' => Request::ip(),
            'url' => Request::fullUrl(),
            'user_agent' => Request::header('User-Agent'),
        ], $extra);
    }

    protected function log($model, string $event, array $properties = [])
    {
        $causer = $this->getCauser();
        $feature = $this->getFeature($model);
        $modelName = class_basename($model);

        // Format description yang jelas
        $desc = "{$modelName} {$event}";
        if ($causer) {
            $desc .= " by {$causer->name}";
        }

        activity()
            ->useLog($feature)
            ->causedBy($causer)
            ->performedOn($model)
            ->tap(function ($activity) use ($feature) {
                $activity->feature = $feature;
            })
            ->withProperties(
                $this->buildProperties($model, $properties)
            )
            ->event($event)
            ->log($desc);
    }

    /**
     * CREATED
     */
    public function created($model)
    {
        $this->log($model, 'created', [
            'new' => $model->getAttributes(),
        ]);
    }

    /**
     * UPDATED
     */
    public function updated($model)
    {
        $this->log($model, 'updated', [
            'old' => $model->getOriginal(),
            'new' => $model->getAttributes(),
        ]);
    }

    /**
     * DELETED
     */
    public function deleted($model)
    {
        $this->log($model, 'deleted', [
            'old' => $model->getOriginal(),
        ]);
    }
}
