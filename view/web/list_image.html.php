<a href="<?= "index.php?m=list&q=$query&s=$source" ?>">Back</a>
<br/>
<p>Query Phrase: <b><?= $query ?></b> (page: <?= $page ?>)</p>

<h3>Image Result</h3>
<?php foreach($items as $item): ?>
<div class="image_result" style="width:50%;">
  <h4>[<span class="show"><?= (int)$item['show'] ?>
  </span> : <span class="click"><?= (int)$item['click'] ?></span>
  => <span class="ctr"><?= ViewHelper::ctr($item['click'], $item['show']) ?></span>%]</h4>
     <div><a href="<?= $item['url'] ?>" data-url="<?= $item['url'] ?>"><img src="<?= $item['img'] ?>" /></a></div>
</div>
<?php endforeach; ?>
<br/ >
