{require 'header.tpl'}

/* This is a comment */

<?php if ($items): ?>
  <?php foreach ($items as $item): ?>
    * <?php echo $item ?>
  <?php endforeach; ?>
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

<br />

/* This is a comment */

<? if ($items): ?>
  <? foreach ($items as $item): ?>
    * <?= $item ?>
  <? endforeach; ?>
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

<br />

{BoldBlock name='Joe' items=$items} Hello: {/BoldBlock} it is {TimeSlice}

{* This is a comment *}

{if (is_array($items))}
  {foreach ($items as $item)}
    * {=$item}
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