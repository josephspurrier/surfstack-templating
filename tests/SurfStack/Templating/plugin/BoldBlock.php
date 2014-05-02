<?php

namespace SurfStack\Templating\Plugin;

class BoldBlock extends Block
{
    function render($strContent, $arrData)
    {
        return '<strong>'.$strContent.'</strong> '.$arrData['name'];
    }
}