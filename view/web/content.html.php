<p>Query Phrase: <b><?= $query ?></b> (page: <?= $page ?>)</p>

<?php foreach($items as $item): ?>

<h4><?= $item['title'] ?> [<?= $item['show'] ?>]</h4>
<div><a href="<?= $item['url'] ?>"><?= $item['url'] ?></a></div>
<div><?= $item['description'] ?></div>

<?php endforeach; ?>
<br/ >
