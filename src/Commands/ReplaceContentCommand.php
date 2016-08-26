<?php

namespace Hesto\Core\Commands;

use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;


abstract class ReplaceContentCommand extends InstallCommand
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
     * Get the destination path.
     *
     * @return string
     */
    abstract function getPath();

    abstract function searchFor();

    abstract function replaceWith();

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function fire()
    {
        $path = $this->getPath();
        $fullPath = base_path() . $path;

        if($this->files->isDirectory($path)) {
            $this->installFiles($path, $this->files->allFiles($fullPath));

            return true;
        }

        $file = new \SplFileInfo($fullPath);

        if($this->putFile($fullPath, $file)) {
            $this->getInfoMessage($fullPath);
        }

        return true;
    }

    /**
     * Compile content.
     *
     * @param $content
     * @return mixed
     */
    protected function compile($content)
    {
        $content = str_replace($this->searchFor(), $this->replaceWith(), $content);

        return $content;
    }

    protected function getInfoMessage($filePath)
    {
        return $this->info('Content changed in: ' . $filePath);
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
