<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Str;
use File;
use Carbon\Carbon;

class CrudGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud
    {name : Class (singular) for example User}
    {--api}';

    protected $description = 'Create CRUD operations';

    protected $namespace = '/Http/Controllers/';
    protected $is_api = false;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name');
        $is_api = $this->option('api');

        if ($is_api) {
            $this->namespace = '/Http/Controllers/Api/';
            $this->is_api = true;
        }

        $this->controller($name);
        $this->model($name);
        $this->request($name);
        $this->migrate($name);


        $plural_name = Str::plural(Str::lower($name));
        if ($is_api){
            File::append(base_path('routes/api.php'), "\n" . 'Route::post(\'' . $plural_name . "/datatable', [{$name}Controller::class, 'datatable']);");
            File::append(base_path('routes/api.php'), "\n" . 'Route::resource(\'' . $plural_name . "', {$name}Controller::class);");
        }
        else
            File::append(base_path('routes/web.php'), 'Route::resource(\'' . $plural_name . "', {$name}Controller::class);");
    }

    protected function getStub($type)
    {
        return file_get_contents(resource_path("stubs/$type.stub"));
    }

    protected function controller($name)
    {
        if ($this->is_api)
            $c_name = 'ApiController';
        else
            $c_name = 'Controller';
        $controllerTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNameSingularLowerCase}}'
            ],
            [
                $name,
                Str::lower(Str::plural($name)),
                Str::lower($name)
            ],
            $this->getStub($c_name)
        );

        file_put_contents(app_path($this->namespace . "{$name}Controller.php"), $controllerTemplate);
    }

    protected function request($name)
    {
        $requestTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNameSingularLowerCase}}'
            ],
            [
                $name,
                Str::lower($name)
            ],
            $this->getStub('Request')
        );

        if (!file_exists($path = app_path('/Http/Requests')))
            mkdir($path, 0777, true);

        file_put_contents(app_path("/Http/Requests/{$name}Request.php"), $requestTemplate);
    }

    protected function model($name)
    {
        $modelTemplate = str_replace(
            ['{{modelName}}'],
            [$name],
            $this->getStub('Model')
        );

        file_put_contents(app_path("/Models/{$name}.php"), $modelTemplate);
    }

    protected function migrate($name)
    {
        $migrationTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePlural}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNameSingularLowerCase}}'
            ],
            [
                $name,
                Str::plural($name),
                Str::lower(Str::plural($name)),
                Str::lower($name)
            ],
            $this->getStub('Migration')
        );

        file_put_contents(database_path("/migrations/" . Carbon::now()->format('Y_m_d') . "_000009_create_" . Str::plural(Str::lower($name)) . "_table.php"), $migrationTemplate);
    }
}
