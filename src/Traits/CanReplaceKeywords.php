<?php

namespace Hesto\Core\Traits;


trait CanReplaceKeywords
{
    /**
     * Replace names with pattern.
     *
     * @param $stub
     * @return $this
     */
    public function replaceNames(&$template)
    {
        $name = $this->getNameInput();

        $plural = [
            '{{pluralCamel}}',
            '{{pluralSlug}}',
            '{{pluralSnake}}',
        ];

        $singular = [
            '{{singularCamel}}',
            '{{singularSlug}}',
            '{{singularSnake}}',
        ];

        $replace = [
            camel_case($name),
            str_slug($name),
            snake_case($name),
        ];

        $template = str_replace($plural, $replace, $template);
        $template = str_replace($singular, $replace, $template);

        return $this;
    }
}
