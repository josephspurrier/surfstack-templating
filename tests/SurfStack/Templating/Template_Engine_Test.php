<?php

/**
 * This file is part of the SurfStack package.
 *
 * @package SurfStack
 * @copyright Copyright (C) Joseph Spurrier. All rights reserved.
 * @author Joseph Spurrier (http://josephspurrier.com)
 * @license http://www.apache.org/licenses/LICENSE-2.0.html
 */

/**
 * Template Engine Test
 * 
 * Ensures the the templates render properly
 *
 */
class Template_Engine_Test extends PHPUnit_Framework_TestCase
{
    protected $view;
    
    protected $output;
    
    protected function setUp()
    {
        //$dir = __DIR__.'/../../../public';
        
        //require_once $dir.'/Template_Engine.php';
        $this->view = new SurfStack\Templating\Template_Engine(__DIR__.'/template/template.tpl');
        
        $this->view->setCacheDir(__DIR__.'/template_c');
        $this->view->setPluginDir(__DIR__.'/plugin');
        
        $this->view->setLoadPlugins(true);
        
        $this->view->clearCache();
        
        $items = array(
        'item1' => 'i1z',
        'item2' => 'i2',
        );
        
        $this->view->assign('items', $items);
    }
    
    protected function tearDown()
    {
        $this->view->clearCache();
    }
    
    private function render()
    {
        ob_start();
        $this->view->render();
        $this->output = ob_get_contents();
        ob_end_clean();
    }
    
    public function testCaching()
    {
        $this->view->clearCache();

        $this->render();
        
        $this->assertFalse($this->view->wasCacheCurrent());
        
        $this->render();
        
        $this->assertTrue($this->view->wasCacheCurrent());
    }
    
    public function testClear()
    {
        $items = array(
            'item1' => 'i1z',
            'item2' => 'i2',
        );
        
        $this->view->assign('items', $items);
        
        $this->view->clear();
        
        $this->assertSame($this->view->getAssigned(), array());
    }
    
    public function testUnassign()
    {
        $this->view->clear();
        
        $this->view->assign('item1', 'test1');
        
        $this->view->assign('item2', 'test2');
    
        $this->view->unassign('item1');
    
        $this->assertSame($this->view->getAssigned(), array(
            'item2' => 'test2',
        ));
    }
    
    /**
     * @expectedException ErrorException
     */
    public function testTemplateNotExist()
    {
        $this->view = new SurfStack\Templating\Template_Engine(__DIR__.'/template/templateNotExist.tpl');
    }

    public function testOutputText()
    {
        $this->expectOutputString('Hello world!');
        
        $this->view->setStripTags(false);
        
        $this->view->setStripWhitespace(false);
        
        $this->view->setTemplate(__DIR__.'/template/text.tpl');
        
        $this->view->render();
    }
    
    public function testOutputTextStripTags()
    {
        $this->expectOutputString('Hello world!'.PHP_EOL);
        
        $this->view->setStripTags(true);
        
        $this->view->setStripWhitespace(false);
    
        $this->view->setTemplate(__DIR__.'/template/textTag.tpl');
    
        $this->view->render();
    }
    
    public function testOutputTextStripTagsWhitespace()
    {
        $this->expectOutputString('Hello world!');
    
        $this->view->setStripTags(true);
    
        $this->view->setStripWhitespace(true);
    
        $this->view->setTemplate(__DIR__.'/template/textTagWhitespace.tpl');
    
        $this->view->render();
    }
    
    public function testOutputRequire()
    {
        $this->expectOutputString('Hello world!');
    
        $this->view->setStripTags(false);
    
        $this->view->setStripWhitespace(false);
    
        $this->view->setTemplate(__DIR__.'/template/requirer.tpl');
    
        $this->view->render();
    }
    
    /**
     * @expectedException ErrorException
     */
    public function testOutputRequireMissing()
    {    
        $this->view->setTemplate(__DIR__.'/template/requireMissing.tpl');
    
        $this->view->render();
    }

    public function testOutputStripFullPHPTags()
    {
        $this->expectOutputString('Hello world!');
    
        $this->view->setStripTags(true);
    
        $this->view->setStripWhitespace(true);
    
        $this->view->setTemplate(__DIR__.'/template/phpFullTags.tpl');
    
        $this->view->render();
    }
    
    public function testOutputStripShortPHPTags()
    {
        $this->expectOutputString('Hello world!');
    
        $this->view->setStripTags(true);
    
        $this->view->setStripWhitespace(true);
    
        $this->view->setTemplate(__DIR__.'/template/phpShortTags.tpl');
    
        $this->view->render();
    }
    
    public function testOutputStripASPPHPTags()
    {
        $this->expectOutputString('Hello world!');
    
        $this->view->setStripTags(true);
    
        $this->view->setStripWhitespace(true);
    
        $this->view->setTemplate(__DIR__.'/template/phpASPTags.tpl');
    
        $this->view->render();
    }

    public function testConversion()
    {
        $before = __DIR__.'/template/tagBefore.tpl';
        $after = __DIR__.'/template/tagAfter.tpl';
        
        $this->view->setTemplate($before);
        
        $this->view->setStripTags(false);
        
        $this->view->setStripWhitespace(false);
        
        $this->render();
        
        $this->assertSame(file_get_contents($this->view->getCachedTemplate()), file_get_contents($after));
    }
    
    public function testBlock()
    {
        $this->expectOutputString('<strong>Hello</strong> world!');
        
        $this->view->setTemplate(__DIR__.'/template/block.tpl');
    
        $this->view->setStripTags(false);
    
        $this->view->setStripWhitespace(false);
    
        $this->view->render();
    }
    
    public function testSlice()
    {
        $this->expectOutputString(date('Y'));
    
        $this->view->setTemplate(__DIR__.'/template/slice.tpl');
    
        $this->view->setStripTags(false);
    
        $this->view->setStripWhitespace(false);
    
        $this->view->render();
    }
}