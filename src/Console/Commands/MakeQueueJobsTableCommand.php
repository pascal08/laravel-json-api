<?php

namespace CloudCreativity\LaravelJsonApi\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;

class MakeQueueJobsTableCommand extends Command
{

    /**
     * @var string
     */
    protected $name = 'queue-jobs:table';

    /**
     * @var string
     */
    protected $description = 'Create a migration for the JSON API resource `queue-jobs` database table';

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var Composer
     */
    protected $composer;

    /**
     * MakeQueueJobsTableCommand constructor.
     *
     * @param Filesystem $files
     * @param Composer   $composer
     */
    public function __construct(Filesystem $files, Composer $composer)
    {
        parent::__construct();

        $this->files = $files;
        $this->composer = $composer;
    }

    /**
     * @return void
     */
    public function fire()
    {
        $table = 'queue-jobs';

        $this->replaceMigration(
            $this->createBaseMigration($table), $table, Str::studly($table)
        );

        $this->info('Migration created succesfully!');

        $this->composer->dumpAutoloads();
    }

    /**
     * @param string $table
     * @return mixed
     */
    protected function createBaseMigration($table = 'queue-jobs')
    {
        return $this->laravel['migration.creator']->create(
            'create_' . $table . '_table', $this->laravel->databasePath() . '/migrations'
        );
    }

    /**
     * @param $path
     * @param $table
     * @param $tableClassName
     */
    protected function replaceMigration($path, $table, $tableClassName)
    {
        $stub = str_replace(
            ['{{table}}', '{{tableClassName}}'],
            [$table, $tableClassName],
            $this->files->get(__DIR__.'/stubs/queue-jobs.stub')
        );

        $this->files->put($path, $stub);
    }
}