<?php
/**
 * Created by iTook.
 * User: Mirhosseini
 * Date: 15/07/2018
 * Time: 10:58 AM
 */

namespace App\Libraries;


class TemplateEngine
{
    protected $content;
    protected $values = array();

    public function __construct($content)
    {
        $this->content = $content;
    }

    public static function getTemplate($content)
    {
        return new TemplateEngine($content);
    }

    public function setValues($values)
    {
        $this->values = $values;

        return $this;
    }

    public function render()
    {
        $output = $this->content;

        foreach ($this->values as $key => $value) {
            $tagToReplace = "%$key%";
            $output = str_replace($tagToReplace, $value, $output);
        }

        return $output;
    }
}
