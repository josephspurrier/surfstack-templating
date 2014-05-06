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
 * SurfStack Template Plugin Base
 *
 * Renders content in a template.
 */
abstract class PluginBase
{
    /**
     * Array of settings from Template Engine
     * @var array
     */
    protected $arrEngineInternals = array();
    
    /**
     * Array of assigned variables from Template Engine
     * @var array
     */
    protected $arrEngineVariables = array();
    
    /**
     * Array of passed variables to the plugin
     * @var array
     */
    protected $arrPluginVariables = array();
    
    /**
     * Determines if render should be the content stored in a compiled template
     * @var bool
     */
    public $customOutput = false;
    
    /**
     * Set a custom tag name
     * @var string
     */
    public $customTagName = '';
    
    /**
     * Store a data to a variable
     * @param string $key
     * @param string $value
     */
    public function store($key, $value)
    {
        $this->$key = $value;
    }
}