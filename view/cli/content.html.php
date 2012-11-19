Query Phrase: <?= $query ?> (page: <?= $page ?>)

<?php foreach($items as $item): ?>

<?= $item['title'] ?> [<?= $item['show'] ?>]
<?= $item['url'] ?>
<?= $item['description'] ?>

<?php endforeach; ?>


