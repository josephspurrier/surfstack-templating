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
 * SurfStack Template Engine Slice
 *
 * Renders content in a template.
 * Designated by an open {name} and close {/name} tag.
 */
abstract class Slice
{
    /**
     * Array of settings from Template Engine
     * @var array
     */
    public $internal = array();
    
    /**
     * Called by the template, expects a return value
     * @param array $arrData
     */
    abstract function render($arrData);
}