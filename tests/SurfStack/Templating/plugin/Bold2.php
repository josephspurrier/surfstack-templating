<?php

namespace SurfStack\Templating\Plugin;
use SurfStack\Templating\Core\Block;

class Bold2 extends Block
{
    function __construct()
    {
        $this->customTagName = 'b';
    }
    
    function render($strContent)
    {
        return '<strong><i>'.$strContent.'</i></strong> '.$this->arrPluginVariables['name'];
    }
}