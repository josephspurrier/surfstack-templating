<?php

namespace SurfStack\Templating\Plugin;

class Time extends Slice
{
    function render($arrData)
    {
        return date('Y');
    }
}