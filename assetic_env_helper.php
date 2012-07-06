<?php

/*

Helper for loading JavaScript assets with Assetic, which does not seem to cover
the basic use case of loading files with individual SCRIPT tags in development
and concatenating (etc.) them in production.

Copyright © 2012 Jesse McCarthy <http://jessemccarthy.net/>

This software may be used under the MIT (aka X11) license or
Simplified BSD (aka FreeBSD) license.  See LICENSE.

*/

$auto_loader = function ( $class_name ) {

  $class_name = str_replace( "\\", "/", $class_name );

  include __DIR__ . "/assetic/{$class_name}.php";

};


spl_autoload_register( $auto_loader );


class Assetic_Env_Helper {

  public $scripts = array();

  public $cfg = array();


  public function __construct( $scripts, $cfg = array() ) {

    $cfg = array_merge( $this->get_default_cfg(), $cfg );

    $this->scripts = $scripts;

    $this->cfg = $cfg;

  }
  // __construct


  public function get_default_cfg() {

    $default_cfg = array(

      'env' => 'development',

      'query_param' => 'output_type'

    );


    return $default_cfg;

  }


  public function get_output_type() {

    return $_GET[ $this->cfg[ 'query_param' ] ] ?: 'html';

  }


  public function get_output() {

    $method = $this->get_output_type();

    $method = "get_{$method}";

    return $this->$method();

  }
  // get_output


  public function get_html() {

    $scripts = $this->scripts;


    if ( $this->cfg[ 'env' ] === 'production' ) {

      $scripts = array( "{$this->cfg[ 'concat_url' ]}?{$this->cfg[ 'query_param' ]}=js" );

    }
    // if


    foreach ( $scripts as &$script ) {

      $script = htmlspecialchars( $script );

      $script = <<<DOCHERE
<script src="{$script}"></script>
DOCHERE;

    }
    // foreach


    echo join( "\n\n\n", $scripts );

  }
  // get_html


  public function get_js() {

    header( "Content-Type: text/javascript" );

    $factory = new Assetic\Factory\AssetFactory( $this->cfg[ 'file_system_path' ] );

    $js = $factory->createAsset( $this->scripts );

    return $js->dump();

  }
  // get_js

}
// Assetic_Env_Helper
