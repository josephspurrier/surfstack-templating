<?php

namespace SurfStack\Templating\Plugin;

class Bold extends Block
{
    function render($strContent, $arrData)
    {
        return '<strong>'.$strContent.'</strong> '.$arrData['name'];
    }
}