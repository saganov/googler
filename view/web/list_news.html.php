<a href="<?= "index.php?m=list&q=$query&s=$source" ?>">Back</a>
<br/>
<p>Query Phrase: <b><?= $query ?></b> (page: <?= $page ?>)</p>

<h3>News Result</h3>
<?php foreach($items as $item): ?>
<div class="news_result" style="width:50%">
  <h4><?= $item['title'] ?> [<span class="show"><?= (int)$item['show']
  ?> </span> : <span class="click"><?= (int)$item['click'] ?></span>
  => <span class="ctr"><?= ViewHelper::ctr($item['click'], $item['show']) ?></span>%]</h4>
     <div><a href="<?= $item['url'] ?>"><?= ViewHelper::crop($item['url'], 40) ?></a></div>
  <div><?= $item['description'] ?></div>
</div>
<?php endforeach; ?>
<br/ >
<div clas="pager"><?php echo ViewHelper::pager($total, $page, "index.php?m=listNews&q=$query&s="); ?></div>
