<?php

namespace Hesto\Core\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;


abstract class InstallCommand extends Command
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description;

    /**
     * Create a new controller creator command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    abstract function fire();

    /**
     * Copy files method.
     *
     * @param $path
     * @param $files
     */
    protected function copyFiles($path, $files)
    {
        foreach($files as $file)
        {
            $name = $file->getFileName();

            if(empty($name)) {
                $name = $file->getExtension();
            }

            $filepath = base_path(). $path . $name;

            if($this->putFile($filepath, $file)) {
                $this->info('Copied: ' . $filepath);
            }
        }
    }

    /**
     * Install files method.
     *
     * @param $path
     * @param $files
     */
    protected function installFiles($path, $files)
    {
        foreach($files as $file)
        {
            $filepath = base_path(). $path . $file->getRelativePath() . $this->parseFilename($file);

            if($this->putFile($filepath, $file)) {
                $this->info('Copied: ' . $filepath);
            }
        }
    }

    protected function parseFilename($file)
    {
        return '/' . $file->getBasename($file->getExtension()) . $this->getExtension($file);
    }

    /**
     * Put given file in path
     *
     * @param $path
     * @param $file
     * @return bool
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function putFile($path, $file)
    {
        if($this->alreadyExists($path) && !$this->option('force')) {
            $this->error($path . ' already exists!');

            return false;
        }

        $this->makeDirectory($path);

        $this->files->put($path, $this->compile($this->files->get($file->getPathname())));

        return true;
    }

    /**
     * Determine if the class already exists.
     *
     * @param $path
     * @return bool
     */
    protected function alreadyExists($path)
    {
        return $this->files->exists($path);
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string  $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (! $this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
    }

    /**
     * Get file extension.
     *
     * @param $file
     * @return bool
     */
    protected function getExtension($file)
    {
        if($this->replaceExtensions()) {
            return $this->replaceExtensions();
        }

        return $file->getExtension();
    }

    /**
     * Replace extension.
     *
     * @return bool
     */
    public function replaceExtensions()
    {
        return false;
    }

    /**
     * Compile content.
     *
     * @param $content
     * @return mixed
     */
    protected function compile($content)
    {
        return $content;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Force override existing files'],
        ];
    }
}
