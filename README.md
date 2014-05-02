SurfStack Templating for PHP [![Build Status](https://travis-ci.org/josephspurrier/surfstack-templating.svg)](https://travis-ci.org/josephspurrier/surfstack-templating)
=================================

The SurfStack Template Engine is a barebones system designed to
be lean and quick. It uses a syntax very similar to Smarty and outputs cached
templates in pure PHP. The engine uses regular expressions to strip out open
and closed PHP tags, PHP echo short tags, PHP short tags, and ASP tags to keep
your templates clean and readable.

PHP itself is a template system, BUT it's not the easiest to read at glance.
Instead of using tags like <?php, the SurfStack Template Engine uses
curly braces so you can type less and build more.

If you are looking for a lightweight template engine to use with PHP, please
fork the project and add your own customizations. I'd love you see what you
come up with.

# Setup the Template Engine

It's a snap. Just pass your template file to the constructor, set the Cache
and Plugin directories, assign a few variables, and then render the template.
The engine will create a cached version of the template in pure PHP and store
it with a unique file name in the Cache directory.

```php
require 'Template_Engine.php';

$view = new SurfStack\Templating\Template_Engine('template/template.tpl');

$view->setPluginDir('plugin');

$view->setCacheDir('template_c');

$view->assign('items', $items);

$view->render();
```

# Settings and Available Methods

You also have access to these public methods to make it easy to troubleshoot
and manage your cache and templates.

```php
// Strip PHP tags from template (default is true)
$view->setStripTags(false);

// Strip whitespace from template (default is true)
$view->setStripWhitespace(false);

// Load plugins (default is true)
$view->setLoadPlugins(false);

// Delete all the cached templates
$view->clearCache();

// Get the path of the cached template
$view->getCachedTemplate();

// Is the cache current?
$view->isCacheCurrent();

// Is the template cached?
$view->isCached();

// Force update the cached template
$view->updateCache();

// Was the template cached before render() was run?
$view->wasCacheCurrent();
```
# Plugins

The SurfStack Template Engine supports plugins which are custom code you can
create yourself and then tie them to a class. The engine supports blocks and
slices. The blocks do work over multiple lines.

Here is an example of a block and slice you could place in your template.

```
{BoldBlock name='World'}Hello{/BoldBlock} it is {TimeSlice}
```

And here is the class you could write. Name it BoldBlock.php and place it in your
plugin folder.

```php
namespace SurfStack\Templating\Plugin;

class BoldBlock extends Block
{
    function render($strContent, $arrData)
    {
        return '<strong>'.$strContent.'</strong> '.$arrData['name'];
    }
}
```

Here is the code for the slice. Name it TimeSlice.php and place it in your plugin
folder.

```php
namespace SurfStack\Templating\Plugin;

class TimeSlice extends Slice
{
    function render($arrData)
    {
        return date('l jS \of F Y h:i:s A');
    }
}
```

The template will then output: **Hello** World it is Thursday 17th of April 2014 04:47:56 AM

# Comparison of Syntax

You can compare the SurfStack Template Engine syntax to the PHP alternative
control structures below to see how different the code looks on the screen.

## SurfStack Template Engine Syntax

Below is the syntax along with all the control structures supported. It
requires the least amount of typing, supports all the same PHP
alternative control structures, and makes it easy to escape output
using =e. It's also supported on all PHP servers because it doesn't
make use of any PHP short tags.

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

Below the standard PHP syntax using the echo short tags and short tags. Echo
short tags are always enabled as of PHP v5.4, but the other short tags are
not recommended for distribution because not all servers have them
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

Below the standard PHP syntax using the recommended full PHP tags. These are
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
  No item has been found..
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