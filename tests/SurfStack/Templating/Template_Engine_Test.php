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
    
    protected $templateDir;
    
    protected function setUp()
    {
        $this->templateDir = __DIR__.'/template/';
        
        $this->view = new SurfStack\Templating\Template_Engine();
        
        $this->view->setTemplate($this->templateDir.'template.tpl');
        
        $this->view->setCompileDir(__DIR__.'/template_compile');
        $this->view->setCacheDir(__DIR__.'/template_cache');
        $this->view->setPluginDir(__DIR__.'/plugin');
        
        $this->view->clearCompile();
        $this->view->clearCache();
        
        $items = array(
        'item1' => 'hello',
        'item2' => 'world',
        );
        
        $this->view->assign('items', $items);
        
        $deep = array(
            'item1' => 'hello',
            'item2' => array(
                'obj' => new stdClass(),
            ),
        );
        
        $this->view->assign('deep', $deep);
        
        $obj = new stdClass();
        $obj->item1 = 'hello';
        $obj->item2 = 'world';
        
        $this->view->assign('obj', $obj);
    }
    
    protected function tearDown()
    {
        $this->view->clearCompile();
        $this->view->clearCache();
    }
    
    private function render()
    {
        ob_start();
        $this->view->render();
        $this->output = ob_get_contents();
        ob_end_clean();
    }
    
    public function testCompiling()
    {
        $this->render();
        
        $this->assertFalse($this->view->wasCompileCurrent());
        
        $this->render();
        
        $this->assertTrue($this->view->wasCompileCurrent());
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
        $this->view->setTemplate($this->templateDir.'templateNotExist.tpl');
    }
    
    /**
     * @expectedException ErrorException
     */
    public function testTemplateNotExistInternal()
    {
        $view = new SurfStack\Templating\Template_Engine();
        $view->setCompileDir(__DIR__.'/template_compile');
        $view->updateTemplate();
        $view->render();
    }
    
    /**
     * @expectedException ErrorException
     */
    public function testPluginDirMissing()
    {
        $this->view->setPluginDir('nowhere');
    }
    
    /**
     * @expectedException ErrorException
     */
    public function testCacheDirMissing()
    {
        $this->view->setCacheDir('nowhere');
    }
    
    public function testCacheDirTemp()
    {
        $view = new SurfStack\Templating\Template_Engine();
        $this->assertSame($view->getCacheDir(), rtrim(sys_get_temp_dir(), '/'));
    }
    
    /**
     * @expectedException ErrorException
     */
    public function testCompileDirMissing()
    {
        $this->view->setCompileDir('nowhere');
    }
    
    public function testCompileDirTemp()
    {
        $view = new SurfStack\Templating\Template_Engine();
        $this->assertSame($view->getCompileDir(), rtrim(sys_get_temp_dir(), '/'));
    }

    public function testOutputText()
    {
        $this->expectOutputString('Hello world!');
        
        $this->view->setStripTags(false);
        
        $this->view->setStripWhitespace(false);
        
        $this->view->setTemplate($this->templateDir.'text.tpl');
        
        $this->view->render();
    }
    
    public function testOutputTextStripTags()
    {
        $this->expectOutputString('Hello world!'."\n");
        
        $this->view->setStripTags(true);
        
        $this->view->setStripWhitespace(false);
    
        $this->view->setTemplate($this->templateDir.'textTag.tpl');
        
        $this->render();
        
        echo str_replace("\r\n","\n",$this->output);
    }
    
    public function testOutputTextStripTagsWhitespace()
    {
        $this->expectOutputString('Hello world!');
    
        $this->view->setStripTags(true);
    
        $this->view->setStripWhitespace(true);
    
        $this->view->setTemplate($this->templateDir.'textTagWhitespace.tpl');
    
        $this->view->render();
    }
    
    public function testOutputRequire()
    {
        $this->expectOutputString('Hello world!');
    
        $this->view->setStripTags(false);
    
        $this->view->setStripWhitespace(false);
    
        $this->view->setTemplate($this->templateDir.'requirer.tpl');
    
        $this->view->render();
    }
    
    /**
     * @expectedException ErrorException
     */
    public function testOutputRequireMissing()
    {    
        $this->view->setTemplate($this->templateDir.'requireMissing.tpl');
    
        $this->view->render();
    }

    public function testOutputStripFullPHPTags()
    {
        $this->expectOutputString('Hello world!');
    
        $this->view->setStripTags(true);
    
        $this->view->setStripWhitespace(true);
    
        $this->view->setTemplate($this->templateDir.'phpFullTags.tpl');
    
        $this->view->render();
    }
    
    public function testOutputStripShortPHPTags()
    {
        $this->expectOutputString('Hello world!');
    
        $this->view->setStripTags(true);
    
        $this->view->setStripWhitespace(true);
    
        $this->view->setTemplate($this->templateDir.'phpShortTags.tpl');
    
        $this->view->render();
    }
    
    public function testOutputStripASPPHPTags()
    {
        $this->expectOutputString('Hello world!');
    
        $this->view->setStripTags(true);
    
        $this->view->setStripWhitespace(true);
    
        $this->view->setTemplate($this->templateDir.'phpASPTags.tpl');
    
        $this->view->render();
    }

    public function testConversion()
    {
        $before = 'tagBefore.tpl';
        $after = __DIR__.'/template/tagAfter.tpl';
        
        $this->view->setTemplate($this->templateDir.$before);
        
        $this->view->setStripTags(false);
        
        $this->view->setStripWhitespace(false);
        
        $this->render();
        
        $this->assertSame(file_get_contents($this->view->getCompiledTemplate()), file_get_contents($after));
    }
    
    public function testCustomSyntaxNone()
    {
        $this->expectOutputString('Hello &lt;i&gt;world&lt;/i&gt;. Goodbye.');
    
        $this->view->assign('name', '<i>world</i>');
    
        $this->view->render('Hello {=e $name}. {if (true)}Goodbye.{endif}');
    }
    
    public function testCustomSyntaxOff()
    {        
        $this->expectOutputString('Hello galaxy. Goodbye.');
        
        $this->view->assign('name', '<i>world</i>');
        
        $this->view->addCustomRegEx('/\{=e\s*(.*?)\}/','galaxy');
        
        $this->view->render('Hello {=e $name}. {if (true)}Goodbye.{endif}');
    }
    
    public function testCustomSyntaxOn()
    {
        $this->expectOutputString('Hello galaxy. {if (true)}Goodbye.{endif}');
    
        $this->view->setCustomSyntax(true);
    
        $this->view->assign('name', '<i>world</i>');
    
        $this->view->addCustomRegEx('/\{=e\s*(.*?)\}/','galaxy');
    
        $this->view->render('Hello {=e $name}. {if (true)}Goodbye.{endif}');
    }
    
    public function testNoLoadPlugins()
    {        
        $this->expectOutputString("{Bold name='world' class=\$obj array=\$items}Hello{/Bold}!");
        
        $this->view->setTemplate($this->templateDir.'pluginBold.tpl');
        
        $this->view->setLoadPlugins(false);
        
        $this->view->setStripTags(false);
        
        $this->view->setStripWhitespace(false);
        
        $this->view->render();
    }
    
    public function testBlock()
    {        
        $this->expectOutputString('<strong>Hello</strong> world!');
        
        $this->view->setTemplate($this->templateDir.'pluginBold.tpl');
        
        $this->view->setLoadPlugins(true);
    
        $this->view->setStripTags(false);
    
        $this->view->setStripWhitespace(false);
    
        $this->view->render();
    }
    
    public function testBlockCustomName()
    {
        $this->expectOutputString('<strong><i>Hello</i></strong> world!');
    
        $this->view->setTemplate($this->templateDir.'pluginBold2.tpl');
    
        $this->view->setLoadPlugins(true);
    
        $this->view->setStripTags(false);
    
        $this->view->setStripWhitespace(false);
    
        $this->view->render();
    }
    
    public function testLoadPluginsArray()
    {        
        $this->view->setLoadPlugins(true);
    
        $this->render();
        
        $this->assertSame($this->view->getLoadedPlugins(), array(
            'blank' => '\SurfStack\Templating\Plugin\Blank',
            'bold' => '\SurfStack\Templating\Plugin\Bold',
            'b' => '\SurfStack\Templating\Plugin\Bold2',
            'extend' => '\SurfStack\Templating\Plugin\Extend',
            'overwrite' => '\SurfStack\Templating\Plugin\Overwrite',
            'passthru' => '\SurfStack\Templating\Plugin\Passthru',
            'time' => '\SurfStack\Templating\Plugin\Time',
            'time2' => '\SurfStack\Templating\Plugin\Time2',
        ));
    }
    
    public function testLoadPluginsCount()
    {
        $this->view->setLoadPlugins(true);
    
        $this->render();
    
        $this->assertSame($this->view->getNumberLoadedPlugins(), 8);
    }
    
    public function testVariableBlock()
    {
        $this->expectOutputString('Hello world.');
        
        $this->view->clear();
        
        $this->view->assign('test', 'Hello world');
    
        $this->view->setTemplate($this->templateDir.'pluginBoldVariable.tpl');
    
        $this->view->setLoadPlugins(true);
    
        $this->view->setStripTags(false);
    
        $this->view->setStripWhitespace(false);
    
        $this->view->render();
    }
    
    public function testSlice()
    {
        $this->expectOutputString(date('Y'));
    
        $this->view->setTemplate($this->templateDir.'pluginYear.tpl');
    
        $this->view->setLoadPlugins(true);
    
        $this->view->render();
    }
    
    public function testSliceSimilarName()
    {
        $this->expectOutputString(date('YY'));
    
        $this->view->setTemplate($this->templateDir.'pluginYear2.tpl');
    
        $this->view->setLoadPlugins(true);
    
        $this->view->render();
    }
    
    public function testOverwrite()
    {
        $this->expectOutputString('Overwrite worked.');
    
        $this->view->setTemplate($this->templateDir.'pluginOverwrite.tpl');
    
        $this->view->setLoadPlugins(true);
    
        $this->view->render();
    }

    public function testVariableSliceBad()
    {
        $this->view->setLoadPlugins(true);
    
        $this->view->setTemplate($this->templateDir.'pluginBlankVariable.tpl');
    
        $this->assertFalse($this->view->isTemplateValid());
    }
    
    public function testVariableSliceBadMessage()
    {
        $this->view->setLoadPlugins(true);
    
        $this->view->setTemplate($this->templateDir.'pluginBlankVariable.tpl');
    
        $arr = $this->view->getTemplateError();
        
        $this->assertSame($arr['message'], 'Undefined variable: missing');
    }
    
    public function testVariableSliceGood()
    {
        $this->view->setLoadPlugins(true);
    
        $this->view->assign('missing', 'notmissing');
    
        $this->view->setTemplate($this->templateDir.'pluginBlankVariable.tpl');
    
        $this->assertTrue($this->view->isTemplateValid());
    }
    
    public function testExtend()
    {        
        $this->view->setTemplate($this->templateDir.'pluginExtendChild.tpl');
        
        $this->view->setLoadPlugins(true);
        
        $this->render();
        
        $content = str_replace("\r\n","\n", $this->output);
        
        $this->assertSame($content, "<p>Hello world!</p>\n\n<div>\nTest from grandparent\nTest from parent\n</div>\n\n<div>\nSafe from child ".date('Y')."\nSafe from grandparent\n</div>");
    }
    
    public function testRenderString()
    {        
        $this->view->setLoadPlugins(true);
        
        $this->assertSame($this->view->getRender('{Time}'), date('Y'));
    }
    
    public function testNoCacheTemplates()
    {
        $this->view->setCacheTemplates(false);
        
        $this->view->setCacheLifetime(-1);
        
        $this->render();
        
        $this->assertFalse($this->view->isCacheCurrent());
        
        $this->assertNull($this->view->wasCacheCurrent());
    }
    
    public function testCacheNeverExpire()
    {
        $this->view->setCacheTemplates(true);
        
        $this->view->setCacheLifetime(-1);
        
        $this->render();
        
        $this->assertTrue($this->view->isCacheCurrent());
    }
    
    public function testCacheAlwaysExpire()
    {
        $this->view->setCacheTemplates(true);
    
        $this->view->setCacheLifetime(0);
        
        $this->render();
    
        $this->assertFalse($this->view->isCacheCurrent());
    }
    
    public function testCacheNotExpire()
    {
        $this->view->setCacheTemplates(true);
    
        $this->view->setCacheLifetime(2);
    
        $this->render();
        
        $this->assertTrue($this->view->isCacheCurrent());
    }
    
    public function testCacheExpire()
    {
        $this->view->clearCache();
    
        $this->view->setCacheLifetime(1);
    
        $this->render();
    
        sleep(1);

        $this->assertFalse($this->view->isCacheCurrent());
    }
    
    public function testNoCheckOriginalMissingCompiled()
    {
        $this->view->setCacheTemplates(true);
    
        $this->view->setAlwaysCheckOriginal(false);
    
        $this->view->setCacheLifetime(5);
    
        $this->render();
    
        $this->assertTrue($this->view->isCacheCurrent());
    
        $this->view->clearCompile();
    
        $this->render();
    
        $this->assertTrue($this->view->wasCacheCurrent());
    
        $this->assertNull($this->view->wasCompileCurrent());
    }
    
    public function testAlwaysCheckOriginalMissingCompiled()
    {
        $this->view->setCacheTemplates(true);
        
        $this->view->setAlwaysCheckOriginal(true);
        
        $this->view->setCacheLifetime(5);
        
        $this->render();
        
        $this->assertTrue($this->view->isCacheCurrent());
        
        $this->view->clearCompile();
        
        $this->render();
        
        $this->assertFalse($this->view->wasCacheCurrent());
        
        $this->assertFalse($this->view->wasCompileCurrent());
    }
    
    public function testAlwaysCheckOriginalNotMissing()
    {
        $this->view->setCacheTemplates(true);
    
        $this->view->setAlwaysCheckOriginal(true);
    
        $this->view->setCacheLifetime(5);
    
        $this->render();
    
        $this->assertTrue($this->view->isCacheCurrent());
    
        $this->render();
    
        $this->assertTrue($this->view->wasCacheCurrent());
    
        $this->assertTrue($this->view->wasCompileCurrent());
    }
    
}