<?php

namespace Shift\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionException;
use ReflectionMethod;

class CheckRoutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shift:check-routes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find routes which do not have a corresponding controller action';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        collect(\Illuminate\Support\Facades\Route::getRoutes())
            ->filter(function (Route $route) {
                return is_string($route->getAction('uses'));
            })
            ->map(function (Route $route) {
                try {
                    $controller = $route->getController();
                    $method = Str::parseCallback($route->getAction('uses'), '__invoke')[1];

                    if (is_null($method)) {
                        return [
                            'type' => 'undefined',
                            'action' => $route->getActionName()
                        ];
                    }

                    $reflectedMethod = new ReflectionMethod($controller, $method);
                    if ($reflectedMethod->isPublic()) {
                        return null;
                    }

                    return [
                        'type' => 'visibility',
                        'action' => $route->getActionName()
                    ];
                } catch (BindingResolutionException $exception) {
                    return [
                        'type' => 'controller',
                        'action' => $route->getActionName()
                    ];
                } catch (ReflectionException $exception) {
                    return [
                        'type' => 'method',
                        'action' => $route->getActionName()
                    ];
                }
            })
            ->filter()
            ->mapToGroups(function ($error) {
                return [$error['type'] => $error['action']];
            })
            ->sortBy('type')
            ->each(function ($actions, $type) {
                $this->displayHeader($type);
                $this->displayIssues($actions);
            });
    }

    private function displayHeader($type)
    {
        if ($type === 'controller') {
            $this->output->error('Undefined Controllers');
            $this->output->text([
                'The following actions referenced controllers which do not exist. To resolve, quickly create these controllers using `artisan make:controller` or remove these routes.'
            ]);
        } elseif ($type === 'method') {
            $this->output->warning('Undefined Methods');
            $this->output->text([
                'The following actions referenced a method in the controller which did not exist. To resolve, you may add the method to the controller or remove these routes.'
            ]);
        } else {
            $this->output->note('Incorrect Visibility');
            $this->output->text([
                'The following actions referenced methods which existed, but have the incorrect visibility. To resolve, you may update the method visibility to `public` or remove these routes.'
            ]);
        }

        $this->output->newLine();
    }

    private function displayIssues(Collection $actions)
    {
        $this->output->listing($actions->toArray());
        $this->output->newLine();
    }
}
