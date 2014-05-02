<?php require 'header.tpl'; ?>

<?php if ($items): ?>
  <?php foreach ($items as $item): ?>
      <?php echo $item ?>
  <?php endforeach; ?>
<?php else: ?>
  <?php echo 'none found' ?>
<?php endif; ?>

<?php for ($i = 0; $i < 10; $i++): ?>
    <?php echo $i ?>
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

Hello world!