<?php

namespace NextDeveloper\Communication;

use Illuminate\Console\Scheduling\Schedule;
use NextDeveloper\Commons\AbstractServiceProvider;
use NextDeveloper\Communication\Jobs\DeliverAllEmails;

/**
 * Class CommunicationServiceProvider
 *
 * @package NextDeveloper\Communication
 */
class CommunicationServiceProvider extends AbstractServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = false;

    /**
     * @throws \Exception
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes(
            [
            __DIR__.'/../config/communication.php' => config_path('communication.php'),
            ], 'config'
        );

        $this->loadViewsFrom($this->dir.'/../resources/views', 'Communication');

        //        $this->bootErrorHandler();
        $this->bootChannelRoutes();
        $this->bootModelBindings();
        $this->bootLogger();
    }

    /**
     * @return void
     */
    public function register()
    {
        $this->registerHelpers();
        $this->registerMiddlewares('generator');
        $this->registerRoutes();
        $this->registerCommands();

        $this->mergeConfigFrom(__DIR__.'/../config/communication.php', 'communication');
        $this->customMergeConfigFrom(__DIR__.'/../config/relation.php', 'relation');
    }

    /**
     * @return void
     */
    public function bootLogger()
    {
        //        $monolog = Log::getMonolog();
        //        $monolog->pushProcessor(new \Monolog\Processor\WebProcessor());
        //        $monolog->pushProcessor(new \Monolog\Processor\MemoryUsageProcessor());
        //        $monolog->pushProcessor(new \Monolog\Processor\MemoryPeakUsageProcessor());
    }

    /**
     * @return array
     */
    public function provides()
    {
        return ['generator'];
    }

    //    public function bootErrorHandler() {
    //        $this->app->singleton(
    //            ExceptionHandler::class,
    //            Handler::class
    //        );
    //    }

    /**
     * @return void
     */
    private function bootChannelRoutes()
    {
        if (file_exists(($file = $this->dir.'/../config/channel.routes.php'))) {
            include_once $file;
        }
    }

    /**
     * Register module routes
     *
     * @return void
     */
    protected function registerRoutes()
    {
        if ( ! $this->app->routesAreCached() && config('leo.allowed_routes.communication', true) ) {
            $this->app['router']
                ->namespace('NextDeveloper\Communication\Http\Controllers')
                ->group(__DIR__.DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'api.routes.php');
        }
    }

    /**
     * Registers module based commands
     *
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands(
                [

                ]
            );
        }
    }

    /**
     * This is here, in case of shit happens!
     *
     * @return void
     */
    private function checkDatabaseConnection()
    {
        $isSuccessfull = false;

        try {
            \DB::connection()->getPdo();

            $isSuccessfull = true;
        } catch (\Exception $e) {
            die('Could not connect to the database. Please check your configuration. error:'.$e);
        }

        return $isSuccessfull;
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

    private function bootSchedule()
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);

            $schedule->call(function () {})->monthly();

            //	Daily jobs
            $schedule->call(function () {})->daily();

            //  Güne başlarken taskları
            $schedule->call(function () {})->weekdays()->dailyAt('09:00');

            $schedule->call(function () {})->weekdays()->dailyAt('12:00');

            //	Hourly Jobs
            $schedule->call(function () {})->hourly();

            $schedule->call(function () {})->everyFifteenMinutes();

            $schedule->call(function () {
                logger()->info('[Communication] Every minute jobs start');
                dispatch( new DeliverAllEmails() );
            })->everyMinute();
        });
    }
}
