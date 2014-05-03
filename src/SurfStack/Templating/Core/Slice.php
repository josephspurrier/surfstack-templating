<?php

/**
 * This file is part of the SurfStack package.
 *
 * @package SurfStack
 * @copyright Copyright (C) Joseph Spurrier. All rights reserved.
 * @author Joseph Spurrier (http://josephspurrier.com)
 * @license http://www.apache.org/licenses/LICENSE-2.0.html
 */

namespace SurfStack\Templating\Core;

/**
 * SurfStack Template Engine Slice
 *
 * Renders content in a template.
 * Designated by an open {name} and close {/name} tag.
 */
abstract class Slice extends PluginBase
{    
    /**
     * Called by the template, expects a return value
     */
    abstract function render();
}