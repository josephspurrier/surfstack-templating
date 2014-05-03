<?php

namespace SurfStack\Templating\Plugin;
use SurfStack\Templating\Core\Block;

class Passthru extends Block
{
    function render($strContent)
    {        
        return $this->arrPluginVariables['pass'];
    }
}