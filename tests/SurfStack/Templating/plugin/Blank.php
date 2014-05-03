<?php

namespace SurfStack\Templating\Plugin;
use SurfStack\Templating\Core\Slice;

class Blank extends Slice
{
    function render()
    {
        return 'blank';
    }
}