<?php

namespace Modules\Attribute\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Attribute\Blade\AttributesDirective;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Attributes\Radio;
use Modules\Attribute\Attributes\Select;
use Modules\Attribute\Attributes\Checkbox;
use Modules\Attribute\Attributes\Textarea;
use Modules\Attribute\Attributes\InputText;
use Modules\Attribute\Normalisers\AttributeOptionsNormaliser;
use Modules\Attribute\Repositories\AttributesManager;
use Modules\Attribute\Repositories\AttributeRepository;
use Modules\Attribute\Repositories\AttributablesManager;
use Modules\Attribute\Repositories\AttributesManagerRepository;
use Modules\Attribute\Repositories\AttributablesManagerRepository;
use Modules\Attribute\Repositories\Cache\CacheAttributeDecorator;
use Modules\Attribute\Repositories\Eloquent\EloquentAttributeRepository;
use Modules\Core\Events\LoadingBackendTranslations;
use Modules\Core\Traits\CanPublishConfiguration;

class AttributeServiceProvider extends ServiceProvider
{
    use CanPublishConfiguration;
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBindings();

        $this->app[AttributesManager::class]->registerEntity(new InputText());
        $this->app[AttributesManager::class]->registerEntity(new Checkbox());
        $this->app[AttributesManager::class]->registerEntity(new Radio());
        $this->app[AttributesManager::class]->registerEntity(new Select());
        $this->app[AttributesManager::class]->registerEntity(new Textarea());

        $this->app->singleton('options.normaliser', function () {
            return new AttributeOptionsNormaliser();
        });

        $this->app->bind('attribute.attributes.directive', function ($app) {
            return new AttributesDirective($app[AttributeRepository::class], $app[AttributablesManager::class]);
        });

        $this->app['events']->listen(LoadingBackendTranslations::class, function (LoadingBackendTranslations $event) {
            $event->load('attributes', array_dot(trans('attribute::attributes')));
            // append translations

        });
    }

    public function boot()
    {
        $this->publishConfig('attribute', 'permissions');
        $this->registerBladeTags();
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
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

    private function registerBindings()
    {
        $this->app->singleton(AttributesManager::class, function () {
            return new AttributesManagerRepository();
        });
        $this->app->singleton(AttributablesManager::class, function () {
            return new AttributablesManagerRepository();
        });
        $this->app->bind(AttributeRepository::class, function () {
            $repository = new EloquentAttributeRepository(new Attribute());

            if (! config('app.cache')) {
                return $repository;
            }

            return new CacheAttributeDecorator($repository);
        });

    }

    private function registerBladeTags()
    {
        if (app()->environment() === 'testing') {
            return;
        }
        $this->app['blade.compiler']->directive('attributes', function ($value) {
            return "<?php echo AttributesDirective::show([$value]); ?>";
        });
    }
}
