<?php

$autenti_commerce_controller = new Autentify_Autenti_Commerce_Controller();

add_action(
  'wp_ajax_autentify_autenti_commerce_initiate_analysis',
  [ $autenti_commerce_controller, 'autentify_autenti_commerce_initiate_analysis' ]
);

add_action(
  'wp_ajax_autentify_autenti_commerce_update_analysis',
  [ $autenti_commerce_controller, 'autentify_autenti_commerce_update_analysis' ]
);
