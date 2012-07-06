<?php

include "assetic_env_helper.php";

$loader = new Assetic_Env_Helper( $scripts, array(

  'concat_url' => "whatever.php",

  'env' => 'development',

  'file_system_path' => "/path/to/js"

) );


echo $loader->get_output();
