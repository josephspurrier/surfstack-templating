<?php

namespace SurfStack\Templating\Plugin;
use SurfStack\Templating\Core\Slice;

class Time2 extends Slice
{
    function render()
    {
        return date('YY');
    }
}