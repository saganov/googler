<a href="/">Back</a>
<br/>
<p>Query Phrase: <b><?= $query ?></b> (page: <?= $page ?>)</p>

<?php foreach($items as $item): ?>
<div class="search_result" style="width:50%">
  <h4><?= $item['title'] ?> [<span class="show"><?= (int)$item['show']
  ?> </span> : <span class="click"><?= (int)$item['click'] ?></span>
  => <span class="ctr"><?= ViewHelper::ctr($item['click'], $item['show']) ?></span>%]</h4>
     <div><a href="<?= $item['url'] ?>"><?= ViewHelper::crop($item['url'], 40) ?></a></div>
  <div><?= $item['description'] ?></div>
</div>
<?php endforeach; ?>
<br/ >
<div clas="pager"><?php echo ViewHelper::pager($total, $page, "/?m=list&q=$query&s=$source"); ?></div>
