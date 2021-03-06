<?php
  if( is_numeric( $_GET['sub'] )) {

    $f = Face::load( $_GET['sub'] );

    if( $f ) {

      $tmp = explode( '.', $f->file );
      $type = $tmp[1];

      switch( $type ) {

        case 'png':
          header('Content-Type: image/png');
          break;

        case 'gif':
          header('Content-Type: image/gif');
          break;

        case 'jpg':
        case 'jpeg':
          header('Content-Type: image/jpeg');
          break;

        default: die('no image');
      }

      readfile('content/thumbs/thumb_120_'.$f->file);
    }
    else header('location: '.$_CONFIG['baseurl'].'/error/404');

  }
  else header('location: '.$_CONFIG['baseurl'].'/error/404');

  exit;
?>
