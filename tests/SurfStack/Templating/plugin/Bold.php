<?php

namespace SurfStack\Templating\Plugin;
use SurfStack\Templating\Core\Block;

class Bold extends Block
{
    function render($strContent)
    {
        return '<strong>'.$strContent.'</strong> '.$this->arrPluginVariables['name'];
    }
}