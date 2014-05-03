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
 * SurfStack Template Engine Block
 *
 * Renders content in a template.
 * Designated by a single {name} tag.
 */
abstract class Block extends PluginBase
{    
    /**
     * Called by the template, expects a return value
     * @param string $strContent Content written between the tags
     */
    abstract function render($strContent);
}