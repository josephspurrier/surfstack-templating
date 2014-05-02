<?php

namespace SurfStack\Templating\Plugin;

class TimeSlice extends Slice
{
    function render($arrData)
    {
        return date('Y');
    }
}