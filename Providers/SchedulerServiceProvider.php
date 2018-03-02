<?php
/**
 * *
 *  * This file is part of Schedule Addon for InvoiceNinja.
 *  * (c) Cytech <cytech@cytech-eng.com>
 *  *
 *  * For the full copyright and license information, please view the LICENSE
 *  * file that was distributed with this source code.
 *  
 *
 */

namespace Modules\Scheduler\Providers;

use App\Providers\AuthServiceProvider;
use Config;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Modules\Scheduler\Models\Setting;
use Schema;
use Form;
use Utils;

class SchedulerServiceProvider extends AuthServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        \Modules\Scheduler\Models\Schedule::class => \Modules\Scheduler\Policies\SchedulePolicy::class,
    ];

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();

	    if (Schema::hasTable('schedule_settings')) {
		    foreach (Setting::all() as $setting) {
			    Config::set('schedule_settings.'.$setting->setting_key, $setting->setting_value);
		    }
	    }

	    //cytech copied and modified from ninja appserviceprovider
	    //was not passing child routes to breadcrumb
	    //also encountered issue with using a dash "-" in route was being replaced on call to mtrans
	    //with underscore
	    Form::macro('breadcrumbs', function ($status = false) {
		    $str = '<ol class="breadcrumb">';

		    // Get the breadcrumbs by exploding the current path.
		    $basePath = Utils::basePath();
		    $parts = explode('?', isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');
		    $path = $parts[0];

		    if ($basePath != '/') {
			    $path = str_replace($basePath, '', $path);
		    }
		    $crumbs = explode('/', $path);

		    foreach ($crumbs as $key => $val) {
			    if (is_numeric($val)) {
				    unset($crumbs[$key]);
			    }
		    }

		    $crumbs = array_values($crumbs);
		    $modcrumb = null;
		    for ($i = 0; $i < count($crumbs); $i++) {
			    $crumb = trim($crumbs[$i]);
			    if (! $crumb) {
				    continue;
			    }
			    if ($crumb == 'company') {
				    return '';
			    }

			    if (! Utils::isNinjaProd() && $module = \Module::find($crumb)) {
				    $modcrumb = $crumb;
				    $name     = mtrans( $crumb );
			    } elseif ($modcrumb){
				    $name     = mtrans( $modcrumb,$crumb );
			    } else {
				    $name = trans("texts.$crumb");
			    }

			    if ($i == count($crumbs) - 1) {
				    $str .= "<li class='active'>$name</li>";
			    } elseif ($i <= count($crumbs) - 2 && $i >= 2) {
				    $str .= '<li>'.link_to($modcrumb.'/'.$crumb, $name).'</li>';
			    }else {
				    $str .= '<li>'.link_to($crumb, $name).'</li>';
			    }
		    }

		    if ($status) {
			    $str .= $status;
		    }

		    return $str . '</ol>';
	    });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('scheduler.php'),
        ]);
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'Scheduler'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = base_path('resources/views/modules/scheduler');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ]);

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/scheduler';
        }, \Config::get('view.paths')), [$sourcePath]), 'Scheduler');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        //$langPath = base_path('resources/lang/modules/schedule');
	    $langPath = base_path('Modules/Scheduler/Resources/lang');//cytech changed

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'Scheduler');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang/en', 'Scheduler');
        }

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
