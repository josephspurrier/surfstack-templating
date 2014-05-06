SurfStack Templating for PHP
=================================
[![Build Status](https://travis-ci.org/josephspurrier/surfstack-templating.svg)](https://travis-ci.org/josephspurrier/surfstack-templating) [![Coverage Status](https://coveralls.io/repos/josephspurrier/surfstack-templating/badge.png)](https://coveralls.io/r/josephspurrier/surfstack-templating)

The SurfStack Template Engine is a barebones system designed to
be lean and quick. It uses a syntax very similar to the alternative control structures
in PHP and outputs compiled and optional cached templates. The engine also has optional regular
expressions to strip out open and closed PHP tags, PHP echo short tags, PHP short tags,
and ASP tags to keep your templates clean and readable.

PHP itself is a template system, BUT it's not the easiest to read at glance.
Instead of using tags like <?php, the SurfStack Template Engine uses
curly braces so you can type less and build more. The tag names are case-insensitive,
but the tag data is case sensitive.

There is a full set of unit tests for the Template Engine class using PHPUnit.
The Template Engine has 100% code coverage.

If you are looking for a lightweight template engine to use with PHP, please
fork the project and add your own customizations. I'd love you see what you
come up with.

# Support Control Structures Syntax

This is the syntax for the control structures.

```
{if},{elseif},{else},{end}
{for},{endfor}
{foreach},{endforeach}
{while},{endwhile}


```



# Render a string

Create an instance of the class, assign a few variables, and then render the string.

```php
// Create an instance of the class
$view = new SurfStack\Templating\Template_Engine();

// Assign variables
$view->assign('name', 'world');

// Render the string directly to the screen
$view->render('Hello {= $name}!');
```

# Render a template

Create an instance of the class, set your template, 
assign a few variables, and then echo the template to the screen.
The engine will create a compiled version of the template in PHP and store
it with a unique file name in the compile directory.

```php
// Create an instance of the class
$view = new SurfStack\Templating\Template_Engine();

// Set the template
$view->setTemplate(__DIR__.'/template/template.tpl');

// Assign variables
$view->assign('items', array('hello', 'world));

// Render the template to the screen using echo
echo $view->getRender();
```

# Compiling and Caching

In order to render a template, the SurfStack markup must be converted to valid PHP.
The compiled (converted) file contains valid PHP and is stored in the compile directory.
You can also enable caching which will pre-render the compiled file and store cached
file in the cache directory so it can be quickly displayed without interpreting any PHP
code. The compiled file will update whenever the original template changes while the cached
file will only update at the end of it's lifetime. You can manually set the lifetime
to control how often the cache is refreshed. You can also force the template engine to
check the original file for changes on every page request. The page may load a bit slower
but will always detect changes in the templates.

```php
// Set the cache directory
$view->setCacheDir(__DIR__.'/template_cache');

// Enable caching
$view->setCacheTemplates(true);

// Set the lifetime of a file to 60 seconds
$view->setCacheLifetime(60);

// Check the original file on every page request
$view->setAlwaysCheckOriginal(true);

```

# Template Inheritance

The Template Engine supports inheritance. You can define a HTML layout in the parent
template using the section blocks and then create a child template that extends the parent
and contains the same section blocks. Any content within the child section blocks
will replace the content within the parent section blocks. You can use the {parent} tag inside
a child section block when you want the parent's content to also display. As long as you have
a parent template, you can create child templates and grandchild templates, etc, but you can
only use the {extend} tag once per file. You can use blocks and slices inside the sections.

The parent template (parent.tpl) could look like this:

```php
<div>
{section name='test'}
World!
{/section}
</div>
```

The child template (child.tpl) could look like this:

```php
{extend file='parent.tpl'}

{section name='test'}
Hello {parent}
{/section}
```

When you render child.tpl, you will get this output: Hello World!

# Settings and Available Methods

You also have access to these public methods to make it easy to troubleshoot
and manage your cache and templates.

```php
// Set the compile directory
$view->setCompileDir(__DIR__.'/template_compile');

// Strip PHP tags from template
$view->setStripTags(true);

// Strip whitespace from template
$view->setStripWhitespace(true);

// Set the plugin directory
$view->setPluginDir(__DIR__.'/plugin');

// Load plugins
$view->setLoadPlugins(true);

// Delete the files
$view->clearCache();
$view->clearCompile();

// Get the path of the files
$view->getCachedTemplate();
$view->getCompiledTemplate();

// Are the files current?
$view->isCacheCurrent();
$view->isCompileCurrent();

// Are the files cached?
$view->isCached();
$view->isCompiled();

// Force update the compiled and cached templates
$view->updateTemplate();

// Were the files current before render() was run?
$view->wasCacheCurrent();
$view->wasCompileCurrent();

// Is the template error free?
$view->isTemplateValid();

// Get the error information for the template
$view->getTemplateError();

// Get an array of loaded plugins
$view->getLoadedPlugins();

// Get the number of loaded plugins
$view->getNumberLoadedPlugins();
```

# Plugins

The SurfStack Template Engine supports plugins which are custom code you can
create yourself and then tie them to a class. The engine supports blocks and
slices. The blocks do work over multiple lines.

Here is an example of a block and slice you could place in your template.

```
{Bold name='World'}Hello{/Bold} it is {Time}
```

And here is the class you could write. Name it Bold.php and place it in your
plugin folder.

```php
namespace SurfStack\Templating\Plugin;

class Bold extends Block
{
    function render($strContent, $arrData)
    {
        return '<strong>'.$strContent.'</strong> '.$arrData['name'];
    }
}
```

Here is the code for the slice. Name it Time.php and place it in your plugin
folder.

```php
namespace SurfStack\Templating\Plugin;

class Time extends Slice
{
    function render($arrData)
    {
        return date('l jS \of F Y h:i:s A');
    }
}
```

The template will then output: **Hello** World it is Thursday 17th of April 2014 04:47:56 AM

## Plugin Settings

You can set the tag name different from the class name by setting the $this->customTagName from
the constructor of the plugin.
You can set the render() method to return custom code instead of the prewritten PHP code by setting
$this->customOutput to true from the constructor of the plugin.

# Custom Regular Expressions

You have the ability to define your own regular expressions. Any custom regular expressions will 
be applied first before any other of the built-in modifications. You can even disable
internal regular expressions by using setCustomSyntax(true).

```PHP
// Disable all built-in regular expressions
$view->setCustomSyntax(true);

// Assigned a variable
$view->assign('name', '<i>world</i>');

// Add a custom regular expression to overwrite escaping.
$view->addCustomRegEx('/\{=e\s*(.*?)\}/','galaxy');

// This will output: Hello galaxy. {if (true)}Goodbye.{endif}
$view->render('Hello {=e $name}. {if (true)}Goodbye.{endif}');
```

# Comparison of Syntax

You can compare the SurfStack Template Engine syntax to the PHP alternative
control structures below to see how different the code looks on the screen.

## SurfStack Template Engine Syntax

Below is the syntax along with all the control structures supported. It
requires the least amount of typing, supports all the PHP
alternative control structures, and makes it easy to escape output
using =e.

```
{* This is a single line comment *}

{*
This is a multi line comment
*}

{require 'header.tpl'}

{if (is_array($items))}
  {foreach ($items as $item)}
    * {= $item}
  {endforeach}
{elseif (is_string($items))}
  Items is a string, should be an array.
{else}
  No item has been found.
{endif}

{for ($i = 0; $i < 10; $i++)}
  * {= $i}
{endfor}

{$i = 0}
{while ($i <= 10)}
  {= $i}
  {$i++}
{endwhile}

{$i = 0}

{$i++}

{=e $i}

{switch ($i)
  case 0}
    {= "i equals 0"}
    {break}
  {case 1}
    {= "i equals 1"}
    {break}
  {case 2}
    {= "i equals 2"}
    {break}
{endswitch}

{declare(ticks=1)}
{enddeclare}
```

## PHP Syntax with PHP with Echo Short Tags and Short Tags

Below is the standard PHP syntax using the echo short tags and short tags. Echo
short tags are always enabled as of PHP v5.4, but the other short tags are
not recommended when distributing code because not all servers have them
enabled.

```php
// This is a single line comment

/*
This is a multi line comment
*/

<? require 'header.tpl'; ?>

<? if (is_array($items)): ?>
  <? foreach ($items as $item): ?>
    * <?= $item ?>
  <? endforeach; ?>
<? elseif (is_string($items)): ?>
  Items is a string, should be an array.
<? else: ?>
  No item has been found.
<? endif; ?>

<? for ($i = 0; $i < 10; $i++): ?>
  * <?= $i ?>
<? endfor; ?>

<? $i = 0; ?>
<? while ($i <= 10): ?>
  <?= $i; ?>
  <? $i++; ?>
<? endwhile; ?>

<? $i = 0; ?>

<? $i++; ?>

<?= htmlentities($i, ENT_QUOTES, "UTF-8"); ?>

<?php switch ($i):
  case 0: ?>
    <?= "i equals 0"; ?>
    <? break; ?>
  <? case 1: ?>
    <?= "i equals 1"; ?>
    <? break; ?>
  <? case 2: ?>
    <?= "i equals 2"; ?>
    <? break; ?>
<? endswitch; ?>

<? declare(ticks=1): ?>
<? enddeclare; ?>
```

## PHP Syntax with PHP without Short Tags

Below are the standard PHP syntax using the recommended full PHP tags. These are
supported on all PHP systems, but they require the most amount of typing
and provides no escaping so you must manually use the htmlentities()
function to ensure your output is safe.

```php
// This is a single line comment

/*
This is a multi line comment
*/

<?php require 'header.tpl'; ?>

<?php if (is_array($items)): ?>
  <?php foreach ($items as $item): ?>
    * <?php echo $item ?>
  <?php endforeach; ?>
<?php elseif (is_string($items)): ?>
  Items is a string, should be an array.
<?php else: ?>
  No item has been found.
<?php endif; ?>

<?php for ($i = 0; $i < 10; $i++): ?>
  * <?php echo $i ?>
<?php endfor; ?>

<?php $i = 0; ?>
<?php while ($i <= 10): ?>
  <?php echo $i; ?>
  <?php $i++; ?>
<?php endwhile; ?>

<?php $i = 0; ?>

<?php $i++; ?>

<?php echo htmlentities($i, ENT_QUOTES, "UTF-8"); ?>

<?php switch ($i):
  case 0: ?>
    <?php echo "i equals 0"; ?>
    <?php break; ?>
  <?php case 1: ?>
    <?php echo "i equals 1"; ?>
    <?php break; ?>
  <?php case 2: ?>
    <?php echo "i equals 2"; ?>
    <?php break; ?>
<?php endswitch; ?>

<?php declare(ticks=1): ?>
<?php enddeclare; ?>
```

To install using composer, use the code from the Wiki page [Composer Wiki page](../../wiki/Composer).