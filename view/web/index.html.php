<form action="index.php">
  <ul style="list-style-type: none;">
    <li>
      <label for="query_phrase">Query Phrase:</label>
      <input id="query_phrase" type="text" name="q" />
    </li>
    <li>
      <label for="source_domain">Source Domain:</label>
      <select id="source_domain" name="s">
        <option value=""></option>
        <?php foreach ($sources as $domain): ?>
        <option value="<?= $domain ?>"><?= $domain ?></option>
        <?php endforeach; ?>
      </select>
    </li>
    <li><input type="hidden" name="m" value="list"><input type="submit"></li>
</form>

