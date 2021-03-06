<ul>
  <li class="col">
    <h3>newest</h3>
    <ol>
      <?php while ($data = array_shift( $newest )): ?>
        <li>
          <a href="<?=$_CONFIG['baseurl'].'/'.$data->id?>" target="_new">
            <div class="thumb" style="background-image:url(<?=a('thumbs/'.$data->thumbnail)?>)">
            </div>
          </a>
          <div class="info">
            <b>#<?=$data->id?></b><br/>
            added: <?=date('d.m.Y, H:i:s', $data->added)?><br/>
            views total: <?=number_format( $data->views, 0, ',', '.')?>
          </div>
        </li>
      <?php endwhile; ?>
    </ol>
  </li>
  <li class="col">
    <h3>most popular</h3>
    <ol>
      <?php while ($data = array_shift($best)): ?>
        <li>
          <a href="<?=$_CONFIG['baseurl'].'/'.$data->id?>"target="_new">
            <div class="thumb" style="background-image:url(<?=a('thumbs/'.$data->thumbnail)?>)">
            </div>
          </a>
          <div class="info">
            <b>#<?=$data->id?></b><br/>
            views total: <?=number_format( $data->views, 0, ',', '.')?><br/>
            views per day: <?=number_format( $data->popularity, 2, ',', '.')?>
          </div>
        </li>
      <?php endwhile; ?>
    </ol>
  </li>
  <li class="col">
    <h3>most unpopular</h3>
    <ol>
      <?php while ($data = array_shift($worst)): ?>
        <li>
          <a href="<?=$_CONFIG['baseurl'].'/'.$data->id?>"target="_new">
            <div class="thumb" style="background-image:url(<?=a('thumbs/'.$data->thumbnail)?>)">
            </div>
          </a>
          <div class="info">
            <b>#<?=$data->id?></b><br/>
            views total: <?=number_format( $data->views, 0, ',', '.')?><br/>
            views per day: <?=number_format( $data->popularity, 2, ',', '.')?>
          </div>
        </li>
      <?php endwhile; ?>
    </ol>
  </li>
</ul>