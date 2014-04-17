<?php

/**
 * This file is part of the SurfStack package.
 *
 * @package SurfStack
 * @copyright Copyright (C) Joseph Spurrier. All rights reserved.
 * @author Joseph Spurrier (http://josephspurrier.com)
 * @license http://www.apache.org/licenses/LICENSE-2.0.html
 */

namespace SurfStack\Templating;

/**
 * SurfStack Template Engine
 *
 * Strips PHP tags, converts custom tags to PHP tags, and saves the modified
 * template as a cached template based on the timestamp of the current PHP
 * template. Renders the cached template to the screen.
 */
class Template_Engine
{    
    /**
     * Path of the template
     * @var string
     */
    protected $template;
    
    /**
     * Array of variables passed to template
     * @var array
     */
    protected $variables = array();
    
    /**
     * Array of variables used by this class
     * @var array
     */
    protected $internal = array();
    
    /**
     * Create class instance
     * @param string $template Template path
     * @throws \ErrorException Throws error if template is not found
     */
    function __construct($template)
    {
        if (!is_file($template))
        {
            throw new \ErrorException('The template, '.$template.', cannot be found.');
        }
    
        $this->template = $template;
        
        $this->internal = array(
            'StripTags' => true,
            'StripWhitespace' => true,
        );
    }
    
    /**
     * Get the value of internal variable
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function getInternal($key, $default = NULL)
    {
        if (isset($this->internal[$key]))
        {
            return $this->internal[$key];
        }
        else
        {
            return $default;
        }
    }
    
    /**
     * Set the value of internal variable
     * @param string $key
     * @param mixed $default
     */
    protected function setInternal($key, $value)
    {
        $this->internal[$key] = $value;
    }
        
    /**
     * Set the path of the cache dir
     * @param string $path
     */
    function setCacheDir($path)
    {
        $this->setInternal('CompiledDir', trim($path, '/'));
    }
    
    /**
     * Strip PHP tags, PHP short tags, PHP echo short tags, and ASP tags
     * @param bool $bool
     */
    function setStripTags($bool)
    {
        $this->setInternal('StripTags', $bool);
    }
    
    /**
     * Strip whitespace around content removed from template
     * @param bool $bool
     */
    function setStripWhitespace($bool)
    {
        $this->setInternal('StripWhitespace', $bool);
    }
    
    /**
     * Get the path of the cache dir
     * @return string
     */
    function getCacheDir()
    {
        return ($this->getInternal('CompiledDir') ? $this->getInternal('CompiledDir') : dirname(realpath($this->template)));
    }
    
    /**
     * Get the extension of the cache file (no leading dot)
     * @return string
     */
    function getCacheExtension()
    {
        return 'c.php';
    }
    
    /**
     * Get the path of the cached template
     * @return string
     */
    function getCachedTemplate()
    {        
        return $this->getCacheDir().'/'.$this->getMD5Template().'.'.$this->getCacheExtension();
    }
    
    /**
     * Get the MD5 of the template
     * @return string
     */
    function getMD5Template()
    {
        // Use filename and file content to ensure complete uniqueness
        return md5($this->template.file_get_contents($this->template));
    }
    
    /**
     * Get the timestamp of the template
     * @return number
     */
    function getTimestampTemplate()
    {
        return filemtime($this->template);
    }
    
    /**
     * Get the timestamp of the cached template
     * @return number
     */
    function getTimestampCache()
    {
        return filemtime($this->getCachedTemplate());
    }
    
    /**
     * Is the template cached?
     * @return boolean
     */
    function isCached()
    {
        return is_file($this->getCachedTemplate());
    }
    
    /**
     * Does the cached template timestamp match the current template timestamp?
     * @return boolean
     */
    function isCacheCurrent()
    {        
        return ($this->isCached() && $this->getTimestampTemplate() == $this->getTimestampCache());
    }
    
    /**
     * Was the cached template current before render was called?
     * @return mixed | NULL
     */
    function wasCacheCurrent()
    {
        return $this->getInternal('WasCached');
    }
    
    /**
     * Updated the cached template
     */
    function updateCache()
    {
        file_put_contents($this->getCachedTemplate(), $this->modifyTemplateRegex(file_get_contents($this->template)));
        touch($this->getCachedTemplate(), filemtime($this->template));
    } 
    
    /**
     * Assign a variable to be passed to template
     * @param string $key
     * @param mixed $value
     */
    function assign($key, $value)
    {
        $this->variables[$key] = $value;
    }
    
    /**
     * Unassign a variable to be passed to template
     * @param string $key
     */
    function unassign($key)
    {
        unset($this->variables[$key]);
    }
    
    /**
     * Clear all assigned variables
     */
    function clear()
    {
        $this->variables = array();
    }
    
    /**
     * Delete all cached templates
     * Must be called after setCacheDir
     */
    function clearCache()
    {
        foreach(glob($this->getCacheDir().'/*'.'.'.$this->getCacheExtension()) as $file)
        {
            @unlink($file);
        }
    }
    
    /**
     * Strip the PHP tags from templates
     * @param string $content
     * @return string
     */
    protected function stripTags($content)
    {
        $stripSpace = $this->getStripWhitespace();
        
        if ($this->getInternal('StripTags'))
        {            
            $stripTags = array(
                // Strip closed PHP, echo, or short tags
                '/'.$stripSpace.'(<\?php|\<\?=|\<\?)(.*?)\?\>'.$stripSpace.'/si',
                // Strip ASP tags
                '/'.$stripSpace.'\<%(.*?)%\>'.$stripSpace.'/s',
                // Strip any open tags
                '/'.$stripSpace.'(<\?php|\<\?=|\<\?|\<\%)(.*)'.$stripSpace.'/si',
            );
        }
        
        // Strip multi-line comments
        $stripTags[] = '/'.$stripSpace.'\{\*(.[^\}\{]*?)\*\}'.$stripSpace.'/s';
        
        return preg_replace($stripTags, '', $content);
    }
    
    /**
     * Get the whitespace regex
     * @return string
     */
    protected function getStripWhitespace()
    {
        if ($this->getInternal('StripWhitespace'))
        {
            return '\s*';
        }
        else
        {
            return '';
        }
    }

    /**
     * Replace custom tags with standard PHP tags
     * @param string $content
     * @return string
     */
    protected function modifyTemplateRegex($content)
    {
        // Strip tags
        $content = $this->stripTags($content);
        
        // Replace tops and mids (colons)
        foreach(array(
            'elseif',
            'else',
            'if',
            'foreach',
            'for',
            'while',
            'declare',
        ) as $c)
        {
            $regex['<?php '.$c.' $1: ?>'] = '/\{\s*'.$c.'\s*(.*?)\}/';
        }
        
        // Replace bottoms (semicolons)
        foreach(array(
            'endif',
            'endforeach',
            'endfor',
            'endwhile',
            'enddeclare',
            'endswitch',
            'break',
            'continue',
        ) as $c)
        {
            $regex['<?php '.$c.'$1; ?>'] = '/\{\s*'.$c.'\s*(.*?)\}/';
        }
        
        // Replace others (semicolons)
        foreach(array(
            //'include',
        ) as $c)
        {
            $regex['<?php '.$c.' $1; ?>'] = '/\{\s*'.$c.'\s+(.*?)\}/';
        }

        // Replace the outliers
        $custom = array(
            '<?php echo htmlentities($1, ENT_QUOTES, "UTF-8") ?>' => '/\{=e\s*(.*)\}/',
            '<?php echo $1 ?>' => '/\{=\s*(.*?)\}/',
            '<?php case $1: ?>' => '/\{\s*case\s*(.*?)\}/',
            '<?php switch $1: $2 : ?>' => '/\{\s*switch\s*(.*?)\n(.*?)\}/s',
            '<?php $$1; ?>' => '/\{\s*\$\s*(.*?)\}/',
        );
        
        $regex = array_merge($regex, $custom);
        
        return preg_replace(array_values($regex), array_keys($regex), $content);
    }

    /**
     * Render the cached template (cache first if not current)
     */
    function render()
    {
        if (!$this->isCacheCurrent())
        {
            $this->setInternal('WasCached', false);
            $this->updateCache();
        }
        else
        {
            $this->setInternal('WasCached', true);
        }
    
        extract($this->variables);
    
        require $this->getCachedTemplate();
    }
}