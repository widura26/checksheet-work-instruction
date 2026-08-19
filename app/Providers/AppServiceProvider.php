<?php

namespace App\Providers;

use App\Observers\GlobalObserverActivityLogs;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale('id');
        // scan semua file di dalam folder models dan daftarkan observer GlobalObserverActivityLogs
        $modelFiles = File::allFiles(app_path('Models'));

        foreach ($modelFiles as $file) {
            $class = 'App\\Models\\'.$file->getFilenameWithoutExtension();

            if (
                class_exists($class) &&
                is_subclass_of($class, \Illuminate\Database\Eloquent\Model::class) &&
                ! (new \ReflectionClass($class))->isAbstract()
            ) {
                $class::observe(GlobalObserverActivityLogs::class);
            }
        }
    }
}
