<h2>views total: <?=$stats['total_views']?></h2>
<ul>
  <li class="col">
    <h3>newest</h3>
    <ol>
      <?php
        $newest = $faces;
        usort( $newest, 'sortAdded');
        $newest = array_slice( $newest, 0, $limit );

        while ($data = array_shift( $newest )) {
          echo '
          <li>
            <a href="'.$_CONFIG['app']['baseurl'].'/'.$data->id.'" target="_new">
              <div class="thumb" style="background-image:url(../sites/'.$_CONFIG['app']['face'].'/thumbs/'.$data->thumbnail.')">
              </div>
            </a>
            <div class="info">
              <b>#'.$data->id.'</b><br/>
              added: '.date('d.m.Y, H:i:s', $data->added).'<br/>
              views total: '.number_format( $data->views, 0, ',', '.').'
            </div>
          </li>';
        }
        mysql_free_result($rs);
      ?>
    </ol>
  </li>
  <li class="col">
    <h3>most popular</h3>
    <ol>
      <?php

        $best = array_slice( $faces, 0, $limit );
        while ($data = array_shift($best)) {
          echo '
          <li>
            <a href="'.$_CONFIG['app']['baseurl'].'/'.$data->id.'"target="_new">
              <div class="thumb" style="background-image:url(../sites/'.$_CONFIG['app']['face'].'/thumbs/'.$data->thumbnail.')">
              </div>
            </a>
            <div class="info">
              <b>#'.$data->id.'</b><br/>
              views total: '.number_format( $data->views, 0, ',', '.').'<br/>
              views per day: '.number_format( $data->popularity, 2, ',', '.').'
            </div>
          </li>';
        }
      ?>
    </ol>
  </li>
  <li class="col">
    <h3>most unpopular</h3>
    <ol>
      <?php
        $worst = array_slice( array_reverse($faces), 0, $limit );
        while ($data = array_shift($worst)) {
          echo '
          <li>
            <a href="'.$_CONFIG['app']['baseurl'].'/'.$data->id.'"target="_new">
              <div class="thumb" style="background-image:url(../sites/'.$_CONFIG['app']['face'].'/thumbs/'.$data->thumbnail.')">
              </div>
            </a>
            <div class="info">
              <b>#'.$data->id.'</b><br/>
              views total: '.number_format( $data->views, 0, ',', '.').'<br/>
              views per day: '.number_format( $data->popularity, 2, ',', '.').'
            </div>
          </li>';
        }
      ?>
    </ol>
  </li>
</ul>