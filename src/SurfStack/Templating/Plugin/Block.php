<?php

/**
 * This file is part of the SurfStack package.
 *
 * @package SurfStack
 * @copyright Copyright (C) Joseph Spurrier. All rights reserved.
 * @author Joseph Spurrier (http://josephspurrier.com)
 * @license http://www.apache.org/licenses/LICENSE-2.0.html
 */

namespace SurfStack\Templating\Plugin;

/**
 * SurfStack Template Engine Block
 *
* Renders content in a template.
* Designated by a single {name} tag.
 */
abstract class Block
{
    /**
     * Called by the template, expects a return value
     * @param string $strContent
     * @param array $arrData
     */
    abstract function render($strContent, $arrData);
}