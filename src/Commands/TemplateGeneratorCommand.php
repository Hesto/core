<?php

namespace Hesto\Core\Commands;

use Hesto\Core\Traits\CanReplaceKeywords;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

abstract class TemplateGeneratorCommand extends InstallCommand
{
    use CanReplaceKeywords;

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
     * The type of class being generated.
     *
     * @var string
     */
    protected $type;

    /**
     * Get the destination path.
     *
     * @return string
     */
    abstract function getPath();

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function fire()
    {
        $path = $this->getPath();

        if($this->files->isDirectory($this->getTemplate())) {
            $this->installFiles($path, $this->files->allFiles($this->getTemplate()));

            return true;
        }

        $template = new \SplFileInfo($this->getTemplate());

        if($this->putFile(base_path() . $path, $template)) {
            $this->info($this->type . ' template created successfully!');
        }

        return true;
    }

    /**
     * Get the desired template.
     *
     * @param $template
     * @return string
     */
    public function getTemplate() {
        $templateDir = __DIR__ . '/../stubs/';

        if($this->option('custom')) {
            $templateDir = $this->option('path');
        }

        $templatesPath = $templateDir . $this->parseTypeName() .'/';

        return $templatesPath . $this->getTemplateInput() . '.stub';
    }

    /**
     * Parse and format the type's name.
     *
     * @return mixed
     */
    public function parseTypeName()
    {
        return str_plural(strtolower($this->type));
    }

    /**
     * Get the desired template name from the input.
     *
     * @return array|string
     */
    public function getTemplateInput()
    {
        return $this->option('template');
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        return trim($this->argument('name'));
    }

    /**
     * Compile the template.
     *
     * @param $template
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function compile($template)
    {
        $this->replaceNames($template);

        return $template;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the class'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['template', 't', InputOption::VALUE_OPTIONAL, 'The template to generate', 'default'],
            ['custom', 'c', InputOption::VALUE_OPTIONAL, 'Use custom templates instead of given ones', false],
            ['path', 'p', InputOption::VALUE_OPTIONAL, 'Local path for template stubs', '/resources/templates/'],
            ['force', 'f', InputOption::VALUE_NONE, 'Force override existing files'],
        ];
    }


}
