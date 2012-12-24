<a href="index.php">Back</a>
<br/>
<h3><?= $item['title'] ?></h3>
<iframe width="420" height="315" src="http://www.youtube.com/embed/<?= $item['url'] ?>" frameborder="0" allowfullscreen></iframe>
<div style="float:left"><?= $item['description'] ?></div>
<div style="clear:left">
<?php foreach($related as $video): ?>
<div style="margin:5px;float:left">
  <a href="<?= 'index.php?m=embedYoutube&url='.$video['url'] ?>"><img
  alt="<?= $video['title'] ?>" title="<?= $video['title'] ?>" src="<?= $video['thumb'] ?>"/></a>
</div>
<?php endforeach; ?>
</div>
