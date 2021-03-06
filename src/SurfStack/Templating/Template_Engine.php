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
 * template as a compiled template based on the timestamp of the current PHP
 * template. Renders the compiled template to the screen.
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
     */
    function __construct()
    {
        // Set the default settings
        $this->internal = array(
            'StripTags' => false,
            'StripWhitespace' => false,
            'LoadPlugins' => false,
            'CacheTemplates' => false,
            'CacheLifetime' => 3600,
            'AlwaysCheckOriginal' => false,
            'PluginCount' => 0,
            'PluginsLoaded' => array(),
            'CustomSyntax' => false,
            'CustomSyntaxArray' => array(),
        );
        
        $this->setInternal('engine', $this);
    }
    
    /**
     * If true, disables all the built-in language conversions. It will then
     * allow you to build your own template engine regular expressions.
     * @param bool $bool
     */
    function setCustomSyntax($bool)
    {
        $this->setInternal('CustomSyntax', $bool);
    }
    
    /**
     * Add a custom regex
     * @param string $search Regular Expression
     * @param string $replace String of replacement text
     */
    function addCustomRegEx($search, $replace)
    {
        $this->internal['CustomSyntaxArray'][$search] = $replace;
    }
    
    /**
     * Set the template
     * @param string $template Template path
     * @throws \ErrorException
     */
    function setTemplate($template)
    {
        if (!is_file(stream_resolve_include_path($template)))
        {
            throw new \ErrorException('The template, '.$template.', cannot be found.');
        }
        
        // Store the template full path
        $this->template = stream_resolve_include_path($template);
        
        $this->setInternal('Template', $this->template);
        
        $this->setTemplateDir(dirname($this->template));
    }
    
    /**
     * Internal check to ensure there is a template
     * @throws \ErrorException
     */
    protected function checkTemplate()
    {
        if (!$this->template)
        {
            throw new \ErrorException('You must specify a template using: setTemplate($template).');
        }
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
     * Get the value of internal variable array
     * @param string $key
     * @param string $keyvalue
     * @return mixed
     */
    protected function getInternalKey($key, $keyvalue)
    {
        $keyvalue = strtolower($keyvalue);
        
        return $this->internal[$key][$keyvalue];
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
     * Increment internal variable
     * @param string $key
     * @param int $int
     */
    protected function incrementInternal($key, $int = 1)
    {
        $this->internal[$key] += intval($int);
    }
    
    /**
     * Added to internal variable array
     * @param string $key
     * @param string $keyvalue
     * @param string $value
     */
    protected function pushInternal($key, $keyvalue, $value)
    {
        $keyvalue = strtolower($keyvalue);
        
        $this->internal[$key][$keyvalue] = $value;
    }
    
    /**
     * Set all the internal variables
     * @param array $arr
     */
    protected function setInternals(array $arr)
    {
        $this->internal = $arr;
    }
    
    /**
     * Get all the internal variables
     * @return array
     */
    protected function getInternals()
    {
        return $this->internal;
    }
    
    /**
     * Set the path of the plugin dir
     * @param string $path
     * @throws \ErrorException
     */
    function setPluginDir($path)
    {
        if (!is_dir(stream_resolve_include_path($path)))
        {
            throw new \ErrorException('The path, '.$path.', cannot be found.');
        }
        
        // Store the full path
        $this->setInternal('PluginDir', rtrim(stream_resolve_include_path($path), '/'));
    }
    
    /**
     * Set the path of the template dir
     * @param string $path
     * @throws \ErrorException
     */
    protected function setTemplateDir($path)
    {    
        $realpath = rtrim(stream_resolve_include_path($path), '/');
        
        // Store the full path
        $this->setInternal('TemplateDir', $realpath);

        if (strstr(get_include_path(), $realpath) === false)
        {
            // Set the include path to include the template directory
            set_include_path(get_include_path().PATH_SEPARATOR.$realpath);
        }
    }
    
    /**
     * Enable or disable stripping PHP tags, PHP short tags, PHP echo short tags, and ASP tags
     * @param bool $bool
     */
    function setStripTags($bool)
    {
        $this->setInternal('StripTags', $bool);
    }
    
    /**
     * Enable or disable stripping whitespace around content removed from template
     * @param bool $bool
     */
    function setStripWhitespace($bool)
    {
        $this->setInternal('StripWhitespace', $bool);
    }
    
    /**
     * Enable or disable loading the plugins
     * @param bool $bool
     */
    function setLoadPlugins($bool)
    {
        $this->setInternal('LoadPlugins', $bool);
    }
    
    /**
     * Enable or disable caching of templates
     * @param bool $bool
     */
    function setCacheTemplates($bool)
    {
        $this->setInternal('CacheTemplates', $bool);
    }
    
    /**
     * Set the cache lifetime. Default is 3600 seconds which is 1 hour.
     * 0 will expire immediately. -1 will never expire.
     * @param int $seconds
     */
    function setCacheLifetime($seconds)
    {
        $this->setInternal('CacheLifetime', $seconds);
    }

    /**
     * Check if the original file was modified on every page request
     * @param bool $bool
     */
    function setAlwaysCheckOriginal($bool)
    {
        $this->setInternal('AlwaysCheckOriginal', $bool);
    }
    
    /**
     * Get the custom MD5 of the template
     * @return string
     */
    protected function getMD5Template()
    {
        // Check for template
        $this->checkTemplate();
        
        // Use filename and file content to ensure complete uniqueness
        return md5($this->template.file_get_contents($this->template));
    }
    
    /**
     * Get the timestamp of the template
     * @return number
     */
    protected function getTimestampTemplate()
    {
        // Check for template
        $this->checkTemplate();
        
        return filemtime($this->template);
    }

    /**
     * Set the path of the cache dir
     * @param string $path
     * @throws \ErrorException
     */
    function setCacheDir($path)
    {
        if (!is_dir(stream_resolve_include_path($path)))
        {
            throw new \ErrorException('The path, '.$path.', cannot be found.');
        }
        
        $this->setInternal('CacheDir', rtrim(stream_resolve_include_path($path), '/'));
    }
    
    /**
     * Get the path of the cache dir
     * @return string
     */
    function getCacheDir()
    {
        if ($this->getInternal('CacheDir'))
        {
            return $this->getInternal('CacheDir');
        }
        else
        {
            return rtrim(sys_get_temp_dir(), '/');
        }
    }
    
    /**
     * Get the extension of the cache file (no leading dot)
     * @return string
     */
    function getCacheExtension()
    {
        return 'ca.php';
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
     * Is the cached template timestamp within it's lifetime?
     * @return boolean
     */
    function isCacheCurrent()
    {
        if ($this->isCached() && $this->getInternal('CacheLifetime') === -1)
        {
            return true;
        }
        else
        {
            return ($this->isCached() && time() < ($this->getTimestampCache() + $this->getInternal('CacheLifetime')));
        }
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
     * Update the cached template
     */
    protected function updateCache()
    {
        ob_start();
    
        extract($this->variables);
        
        require $this->getCompiledTemplate();
    
        $output = ob_get_contents();
    
        ob_end_clean();
    
        file_put_contents($this->getCachedTemplate(), $output);
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
     * Set the path of the compile dir
     * @param string $path
     * @throws \ErrorException
     */
    function setCompileDir($path)
    {
        if (!is_dir(stream_resolve_include_path($path)))
        {
            throw new \ErrorException('The path, '.$path.', cannot be found.');
        }
        
        $this->setInternal('CompileDir', rtrim(stream_resolve_include_path($path), '/'));
    }
    
    /**
     * Get the path of the compile dir
     * @return string
     */
    function getCompileDir()
    {
        if ($this->getInternal('CompileDir'))
        {
            return $this->getInternal('CompileDir');
        }
        else 
        {
            return rtrim(sys_get_temp_dir(), '/');
        }
    }
    
    /**
     * Get the extension of the compile file (no leading dot)
     * @return string
     */
    function getCompileExtension()
    {
        return 'co.php';
    }
    
    /**
     * Get the path of the compiled template
     * @return string
     */
    function getCompiledTemplate()
    {
        return $this->getCompileDir().'/'.$this->getMD5Template().'.'.$this->getCompileExtension();
    }
    
    /**
     * Get the timestamp of the compiled template
     * @return number
     */
    function getTimestampCompile()
    {
        return filemtime($this->getCompiledTemplate());
    }
    
    /**
     * Is the template compiled?
     * @return boolean
     */
    function isCompiled()
    {
        return is_file($this->getCompiledTemplate());
    }
    
    /**
     * Does the compiled template timestamp match the current template timestamp?
     * @return boolean
     */
    function isCompileCurrent()
    {
        return ($this->isCompiled() && $this->getTimestampTemplate() == $this->getTimestampCompile());
    }
    
    /**
     * Was the compiled template current before render was called?
     * @return mixed | NULL
     */
    function wasCompileCurrent()
    {
        return $this->getInternal('WasCompiled');
    }
    
    /**
     * Updated the compiled and cached template (if enabled)
     */
    function updateTemplate()
    {
        $this->checkTemplate();
        
        file_put_contents($this->getCompiledTemplate(), $this->modifyTemplateRegex(file_get_contents($this->template)));
        
        touch($this->getCompiledTemplate(), filemtime($this->template));
        
        // Update cache if enabled
        if ($this->getInternal('CacheTemplates'))
        {
            $this->updateCache();
        }
    }
    
    /**
     * Delete all compiled templates
     * Must be called after setCompileDir
     */
    function clearCompile()
    {
        foreach(glob($this->getCompileDir().'/*'.'.'.$this->getCompileExtension()) as $file)
        {
            @unlink($file);
        }
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
     * Return the assigned variables which will be passed to template
     * @return array
     */
    function getAssigned()
    {
        return $this->variables;
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
     * Strip the PHP tags from templates
     * @param string $content
     * @return string
     */
    protected function stripTags($content)
    {
        $stripSpace = $this->getStripWhitespace();
        
        $stripTags = array();
        
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
        //$stripTags[] = '/'.$stripSpace.'\{\*(.[^\}\{]*?)\*\}'.$stripSpace.'/s';
        
        // Strip section tags and all space around
        $stripTags[] = '/\s*\{(section)\s*(.*?)\}(.*?)\{\/section\s*\}\s*/si';
        
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
     * Compile the required template from require
     * @param array $matches
     * @throws \ErrorException
     * @return string
     */
    protected function embedIncludeRequire(array $matches)
    {
        // Strip quotes
        $match = str_replace(array('"',"'"), '', $matches[1]);
        
        // If the file is found
        if (is_file(stream_resolve_include_path($match)))
        {
            // Generate a new instance of this class
            $class = new self();
            $class->setTemplate(stream_resolve_include_path($match));
            
            // Copy over the settings
            $class->setInternals($this->getInternals());
            
            // If the compile is not current
            if (!$class->isCompileCurrent())
            {
                // Update the compile
                $class->updateTemplate();
            }
            
            // Return the full path to the template
            return "{require '".realpath($class->getCompiledTemplate())."'}";
            
        }
        else
        {
            throw new \ErrorException("The file, {$matches[1]}, cannot found in the template folder.");
        }
    }
    
    /**
     * Handle logic for required template
     * @param string $content
     * @return string
     */
    protected function updateEmbeddedFiles($content)
    {
        // Replace require
        foreach(array(
            'require',
        ) as $c)
        {
            $regex['<?php '.$c.' $1; ?>'] = '/\{'.$c.'\s+(.*?)\}/';
        }
        
        // Embed files into templates
        $content = preg_replace_callback(array_values($regex), array($this, 'embedIncludeRequire') , $content);
        
        // Update require to tags
        return preg_replace(array_values($regex), array_keys($regex), $content);
    }
    
    /**
     * Load the plugins and return an array of code to replace
     * @return string
     */
    protected function loadPlugins()
    {
        $return = array();
        
        if ($this->getInternal('LoadPlugins'))
        {
            if ($this->getInternal('PluginDir'))
            {
                $this->setInternal('PluginCount', 0);
                $this->setInternal('PluginsLoaded', array());
                
                foreach(glob($this->getInternal('PluginDir').'/*.php') as $file)
                {
                    require_once $file;
                    
                    $name = str_ireplace('.php', '', basename($file));
                    
                    $className = '\SurfStack\Templating\Plugin\\'.$name;
                    
                    $arr = explode('\\', get_parent_class($className));
                    
                    $parent = array_pop($arr);
                    
                    $c = new $className();
                    
                    // Set the custom tag name
                    if ($c->customTagName)
                    {
                        $name = $c->customTagName;
                    }
                    
                    switch ($parent)
                    {
                    	case 'Block':
                    	    $return[$name] = '/\{('.$name.')(\s+.*?|)\}(.*?)\{\/'.$name.'\}/si';
                    	    break;
                    	case 'Slice':
                    	    $return[$name] = '/\{('.$name.')(\s+.*?|)\}/i';
                    	    break;
                    }
                    
                    $this->incrementInternal('PluginCount');
                    $this->pushInternal('PluginsLoaded', $name, $className);
                }
            }
        }

        return $return;
    }
    
    /**
     * Get an array of loaded plugins
     * @return array
     */
    function getLoadedPlugins()
    {
        return $this->getInternal('PluginsLoaded');
    }
    
    /**
     * Get the number of loaded plugins
     * @return int
     */
    function getNumberLoadedPlugins()
    {
        return $this->getInternal('PluginCount');
    }
    
    /**
     * Build an array that can be rendered
     * @param array $arr
     * @return string
     */
    protected function buildRenderableArray(array $arr)
    {
        $arrOut = 'array(';
        
        foreach($arr as $key => $val)
        {
            if (strstr($val, '$'))
            {
                $arrOut .= "'$key'=>$val,";
            }
            else
            {
                $escapedVal = str_replace("'", "\'", $val);
            
                $arrOut .= "'$key'=>'$escapedVal',";
            }
        }
        
        $arrOut .= ')';
        
        return $arrOut;
    }
    
    /**
     * Parse a string of name='value' and convert to an array
     * @param string $strData
     * @return array
     */
    protected function parsePluginVariables($strData)
    {
        $arr = array();
        
        // Extract the variables
        if(preg_match_all('/(\w+=\'[^\']*\'|\w+=\"[^"]*"|\w+=[^\s]*)+/', $strData, $m))
        {
            foreach($m[0] as $key => $k)
            {
                $arrSplit = explode('=', $k);
        
                $arr[$arrSplit[0]] = trim(join('', array_slice($arrSplit, 1)), '\'\"');
            }
        }
        
        return $arr;
    }
    
    /**
     * Escape single quotes
     * @param string $string
     * @return string
     */
    protected function safeString($string)
    {
        return str_replace("'", '/', $string);
    }
    
    /**
     * Get the class and parent classes file paths
     * @param string $class
     */
    protected function getRequiredClasses($class)
    {
        $arrRequire = '';
        
        do
        {
            $rc = new \ReflectionClass($class);
            $arrRequire[] = "require_once '{$this->safeString($rc->getFileName())}';";
            $class = get_parent_class($class);
        
        } while ($class);

        return join(PHP_EOL, array_reverse($arrRequire));
    }
    
    /**
     * Replace the plugin tags with PHP code
     * @param array $matches
     * @return string
     */
    protected function dynamicPluginReplacement($matches)
    {
        $pluginName = $matches[1];
        $pluginData = $matches[2];
        // Block has content, Slice does not
        $pluginContent = (isset($matches[3]) ? "'".addslashes($matches[3])."'" : '');
        
        $passed = $this->parsePluginVariables($pluginData);
        
        // Get the variables as a renderable array
        $sPassed = $this->buildRenderableArray($passed);
        
        // Load the read name of the class
        $class = $this->getInternalKey('PluginsLoaded', $pluginName);
        
        // Get the requires classes as strings
        $require = $this->getRequiredClasses($class);
        
        // If customOutput is set, out the actual content
        $c = new $class();        
        if ($c->customOutput)
        {
            $c->store('arrEngineVariables', $this->variables);
            $c->store('arrEngineInternals', $this->internal);
            $c->store('arrPluginVariables', $passed);
            return $c->render($pluginContent);
        }
    
return "<?php $require
\$class = new $class();
\$class->store('arrEngineVariables', \$this->variables);
\$class->store('arrEngineInternals', \$this->internal);
\$class->store('arrPluginVariables', $sPassed);
echo \$class->render($pluginContent); ?>
";
    }
    
    /**
     * Replace plugin tags with PHP code
     * @param string $content
     * @return string
     */
    protected function parsePlugins($content)
    {        
        // Load the plugin content and replace        
        return preg_replace_callback(array_values($this->loadPlugins()), array($this, 'dynamicPluginReplacement') , $content);
    }
    
    /**
     * Return the rendered output of a string run through the plugins 
     * @param string $content
     * @return string
     */
    function getApplyPlugins($content)
    {
        $file = tempnam($this->getCompileDir(), 'tmp');
        
        file_put_contents($file, $this->parsePlugins($content));
        
        ob_start();
        require $file;
        $output = ob_get_contents();
        ob_end_clean();
        
        unlink($file);
        
        return $output;
    }
    
    /**
     * Replace custom tags with standard PHP tags
     * @param string $content
     * @return string
     */
    protected function modifyTemplateRegex($content)
    {
        if ($this->getInternal('CustomSyntaxArray'))
        {
            // Custom content first
            $content = preg_replace(array_keys($this->getInternal('CustomSyntaxArray')), array_values($this->getInternal('CustomSyntaxArray')), $content);
        }
        
        // Strip tags
        $content = $this->stripTags($content);
        
        if ($this->getInternal('CustomSyntax'))
        {            
            $content = preg_replace(array_keys($this->getInternal('CustomSyntaxArray')), array_values($this->getInternal('CustomSyntaxArray')), $content);
            
            // Replace the plugin tags
            return $this->parsePlugins($content);
        }
        else
        {
            // Update required files
            $content = $this->updateEmbeddedFiles($content);
            
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
                $regex['/\{'.$c.'\s*(.*?)\}/'] = '<?php '.$c.' $1: ?>';
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
                'dowhile',
            ) as $c)
            {
                $regex['/\{'.$c.'\s*(.*?)\}/'] = '<?php '.$c.'$1; ?>';
            }
            
            // Replace the outliers
            $custom = array(
                '/\{\*(.[^\}\{]*?)\*\}/s' => '/*$1*/',
                '/\{=e\s*(.*?)\}/' => '<?php echo htmlentities($1, ENT_QUOTES, "UTF-8"); ?>',
                '/\{=\s*(.*?)\}/' => '<?php echo $1; ?>',
                '/\{case\s*(.*?)\}/' => '<?php case $1: ?>',
                '/\{switch\s*(.[^'."\r\n".']*?)'."\r\n".'(.*?)\}/' => '<?php switch $1:'."\r\n".'$2: ?>',
                '/\{switch\s*(.[^'."\n".']*?)'."\n".'(.*?)\}/' => '<?php switch $1:'."\n".'$2: ?>',
                '/\{\$\s*(.*?)\}/' => '<?php $$1; ?>',
            );
            
            // Replace the { tags with PHP tags
            $regex = array_merge($regex, $custom);
            $content = preg_replace(array_keys($regex), array_values($regex), $content);
            
            // Replace the plugin tags
            return $this->parsePlugins($content);
        }
    }
    
    /**
     * Called by isTemplateValid() to record error
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @return boolean
     */
    protected function errorHandler($errno, $errstr, $errfile, $errline)
    {
        $this->error = false;
        
        return true;
    }
    
    /**
     * Determines if the template has any non-fatal problems
     * @return boolean
     */
    function isTemplateValid()
    {        
        $this->error = true;
        
        set_error_handler(array($this, 'errorHandler'));
        
        // If the compile is not current
        if (!$this->isCompileCurrent())
        {
            // Update the compile
            $this->updateTemplate();
        }
        
        // Extract the variables
        extract($this->variables);
        
        ob_start();
        @require $this->getCompiledTemplate();
        ob_end_clean();
        
        restore_error_handler();
        
        return $this->error;
    }
    
    /**
     * Returns an array of error information from error_get_last() for the compile template
     * or an empty array if successful
     * @return array
     */
    function getTemplateError()
    {
        // If the compile is not current
        if (!$this->isCompileCurrent())
        {
            // Update the compile
            $this->updateTemplate();
        }
        
        // Extract the variables
        extract($this->variables);
        
        ob_start();
        @require $this->getCompiledTemplate();
        ob_end_clean();
        
        $error = error_get_last();
        
        return (is_null($error) ? array() : $error);
    }
    
    /**
     * Return the render as a string
     * @return string
     */
    function getRender($string = null)
    {
        ob_start();
        $this->render($string);
        $output = ob_get_contents();
        ob_end_clean();
        
        return $output;
    }
    
    /**
     * Render a string
     * @param string $content
     */
    protected function renderString($content)
    {
        $file = tempnam($this->getCompileDir(), 'tmp');
    
        file_put_contents($file, $this->modifyTemplateRegex($content));
    
        // Extract the variables
        extract($this->variables);
        
        require $file;
    
        unlink($file);
    }
    
    /**
     * Render a template
     */
    protected function renderTemplate()
    {
        // Marked them as null for testing purposes
        $this->setInternal('WasCached', null);
        $this->setInternal('WasCompiled', null);
        
        // If caching is enabled
        if ($this->getInternal('CacheTemplates'))
        {
            // If the cache is current
            if ($this->isCacheCurrent())
            {
                // Marked the cache as current
                $this->setInternal('WasCached', true);
        
                // If set to always check and the the compile is not current
                if ($this->getInternal('AlwaysCheckOriginal'))
                {
                    // If the compile is current
                    if ($this->isCompileCurrent())
                    {
                        // Mark the compile as current
                        $this->setInternal('WasCompiled', true);
                    }
                    else
                    {
                        // Mark the compile as not current
                        $this->setInternal('WasCompiled', false);
        
                        // Mark the cache as not current
                        $this->setInternal('WasCached', false);
        
                        // Update the compile (and the cache)
                        $this->updateTemplate();
                    }
                }
            }
            // Else the cache does not exist or is expired
            else
            {
                // Marked the cache as not current
                $this->setInternal('WasCached', false);
        
                // Mark the compile as not current
                $this->setInternal('WasCompiled', false);
        
                // Update the compile (and the cache)
                $this->updateTemplate();
            }
        
            // Render the cache
            require $this->getCachedTemplate();
        }
        // Else caching is not enabled
        else
        {
            // If the compile is current
            if ($this->isCompileCurrent())
            {
                // Mark the compile as current
                $this->setInternal('WasCompiled', true);
            }
            else
            {
                // Mark the compile as not current
                $this->setInternal('WasCompiled', false);
        
                // Update the compile
                $this->updateTemplate();
            }
        
            // Extract the variables
            extract($this->variables);
        
            // Render the compile
            require $this->getCompiledTemplate();
        }
    }
    
    /**
     * Render the template or string
     */
    function render($string = null)
    {
        if ($string)
        {
            $this->renderString($string);
        }
        else
        {
            $this->renderTemplate();
        }
    }
}