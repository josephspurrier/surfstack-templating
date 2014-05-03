<?php

namespace SurfStack\Templating\Plugin;
use SurfStack\Templating\Core\Slice;

class Time extends Slice
{
    function render()
    {
        return date('Y');
    }
}