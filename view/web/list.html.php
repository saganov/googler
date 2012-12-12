<a href="index.php">Back</a>
<br/>
<p>Query Phrase: <b><?= $query ?></b>

<div style="width:50%;float:left;">
  <h3>Search Result</h3>
  <?php foreach($items['search'] as $item): ?>
  <div class="search_result">
    <h4>
      <?= $item['title'] ?> <br/>[<span class="show"><?= (int)$item['show']
      ?> </span> : <span class="click"><?= (int)$item['click'] ?></span>
                              => <span class="ctr"><?= ViewHelper::ctr($item['click'],
                              $item['show']) ?></span>%]
    </h4>
    <div><a href="<?= $item['url'] ?>"><?= ViewHelper::crop($item['url'], 40) ?></a></div>
    <div><?= $item['description'] ?></div>
  </div>
  <?php endforeach; ?>
  <div style="margin: 30px 0;">
    <a href="<?= "index.php?m=listSearch&q=$query&s=$source" ?>">Show
    all results</a>
  </div>
</div>

<div style="width:50%;float:left;">
  <h3>News Result</h3>
  <?php foreach($items['news'] as $item): ?>
  <div class="news_result">
    <h4><?= $item['title'] ?> <br/>[<span class="show"><?= (int)$item['show']
    ?> </span> : <span class="click"><?= (int)$item['click'] ?></span>
    => <span class="ctr"><?= ViewHelper::ctr($item['click'], $item['show']) ?></span>%]</h4>
    <div><a href="<?= $item['url'] ?>"><?= ViewHelper::crop($item['url'], 40) ?></a></div>
    <div><?= $item['description'] ?></div>
  </div>
  <?php endforeach; ?>
  <br/ >
  <div style="margin-bottom: 30px;"><a href="<?= "index.php?m=listNews&q=$query&s=" ?>">Show
  all results</a></div>
</div>

<div style="clear:left; width:100%">
  <h3>Youtube Result</h3>
  <?php foreach($items['youtube'] as $item): ?>
  <div class="youtube_result" style="float:left; width:10%">
    <h4><?= $item['title'] ?> <br/>[<span class="show"><?= (int)$item['show']
    ?> </span> : <span class="click"><?= (int)$item['click'] ?></span>
    => <span class="ctr"><?= ViewHelper::ctr($item['click'], $item['show']) ?></span>%]</h4>
    <div><a href="<?= $item['url'] ?>"><?= ViewHelper::crop($item['url'], 40) ?></a></div>
    <div><?= $item['description'] ?></div>
  </div>
  <?php endforeach; ?>
  <br/ >
  <div style="margin-bottom: 30px;"><a href="<?= "index.php?m=lisYoutube&q=$query&s=" ?>">Show
  all results</a></div>
</div>
