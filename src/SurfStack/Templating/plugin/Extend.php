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
use SurfStack\Templating\Core\Slice;
use SurfStack\Templating\Template_Engine;

/**
 * SurfStack Template Extend Block
 *
 * Allows for template inheritance.
 * Designated by a single {Extend file='template.tpl'} tag.
 */
class Extend extends Slice
{
    /**
     * Array of sections from each template
     * @var array
     */
    protected $sections = array();
    
    /**
     * Number of skipped sections
     * @var int
     */
    protected $skipped = 0;
    
    /**
     * Array of template parents
     * @var array
     */
    protected $parents = array();
    
    /**
     * Path of the final template to render
     * @var string
     */
    protected $finalTemplate = '';
    
    /**
     * RegEx for {section}{/section}
     * @var string
     */
    protected $sectionRegex = '/\{\s*section\s*(.*?)\}(.*?)\{\/\s*section\s*\}/si';
    
    /**
     * RegEx for {extend}
     * @var unknown
     */
    protected $extendRegex = '/\{\s*extend\s*(.*?)\}/i';
    
    /**
     * (non-PHPdoc)
     * @see \SurfStack\Templating\Core\Slice::render()
     */
    function render()
    {        
        if (isset($this->arrPluginVariables['file']))
        {
            $template = $this->arrPluginVariables['file'];
            
            if (!is_file(stream_resolve_include_path($template)))
            {
                throw new \ErrorException('The template, '.$template.', cannot be found.');
            }
            
            $this->parents[] = $this->arrEngineInternals['Template'];
            
            do
            {
                $parentName = array_shift($this->parents);
                $currentTemplate = stream_resolve_include_path($parentName);
                //$currentTemplate2 = $this->arrEngineInternals['TemplateDir'].'/'.$parentName;
                
                if (is_file($currentTemplate))
                {
                    $this->parseTemplate(file_get_contents($currentTemplate));
                    
                    $this->finalTemplate = $currentTemplate;
                }
                /*else if (is_file($currentTemplate2))
                {
                    $this->parseTemplate(file_get_contents($currentTemplate2));
                    
                    $this->finalTemplate = $currentTemplate2;
                }*/
                else
                {
                    throw new \ErrorException("Template parent, $parentName, cannot be found.");
                }
            }
            while ($this->parents);

            return $this->arrEngineInternals['engine']->getRenderPlugins($this->overlayTemplate());
        }
        else
        {
            throw new \InvalidArgumentException('You must specify the "file" argument when using the Extend plugin.');
        }        
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
    * Extract section and extend tags
    * @param string $content
    * @return string
    */
    protected function parseTemplate($content)
    {        
        // Extract the sections
        if(preg_match_all($this->sectionRegex, $content, $m))
        {            
            for($i = 0, $total = count($m[0]); $i < $total; $i++)
            {
                $arrData = array();
                
                $vars = $this->parsePluginVariables($m[1][$i]);
                
                if (isset($vars['name']))
                {
                    $this->storeSection($vars['name'], array(
                        'content' => trim($m[2][$i]),
                        'data' => $vars,
                    ));
                }
            }
        }
        
        // Extract the extend
        if(preg_match($this->extendRegex, $content, $m))
        {
            $vars = $this->parsePluginVariables($m[1]);
            
            if (isset($vars['file']))
            {
                $this->parents[] = $vars['file'];
            }
        }
    }
    
    /**
     * Store only the first section
     * @param string $key
     * @param array $value
     */
    protected function storeSection($key, array $value)
    {
        if (!isset($this->sections[$key]))
        {
            $this->sections[$key] = $value;
        }
        else
        {
            $this->skipped += 1;
        }
    }

    /**
     * Returns the replacement for the section in the final template
     * @param array $matches
     * @return string
     */
    protected function dynamicSectionReplacement($matches)
    {
        $vars = $this->parsePluginVariables($matches[1]);
        
        if (isset($vars['name']))
        {
            $name = $vars['name'];
            
            if (isset($this->sections[$name]))
            {
                $content = $this->sections[$name]['content'];
                
                if (strpos($content, '{parent}') !== -1)
                {
                    return str_replace('{parent}', trim($matches[2]), $content);
                }
                else
                {
                    return $content;
                }
            }
            else
            {
                return trim($matches[2]);
            }
        }
    }
    
    /**
     * Returns the final template output
     * @return string
     */
    protected function overlayTemplate()
    {
        return preg_replace_callback($this->sectionRegex, array($this, 'dynamicSectionReplacement') , file_get_contents($this->finalTemplate));
    }
}