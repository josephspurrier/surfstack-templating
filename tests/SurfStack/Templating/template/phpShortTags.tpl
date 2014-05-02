<? require 'header.tpl'; ?>

<? if ($items): ?>
  <? foreach ($items as $item): ?>
      <?= $item ?>
  <? endforeach; ?>
<? else: ?>
  <? echo 'none found' ?>
<? endif; ?>

<? for ($i = 0; $i < 10; $i++): ?>
    <?= $i ?>
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

Hello world!