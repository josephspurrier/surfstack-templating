<?php

namespace SurfStack\Templating\Plugin;
use SurfStack\Templating\Core\Slice;

class Overwrite extends Slice
{    
    function __construct()
    {
        $this->customOutput = true;
    }
    
    function render()
    {
        return 'Overwrite worked.';
    }
}