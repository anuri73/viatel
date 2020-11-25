<?php
/**
 * Plugin Name: Viatel payment plugin
 * Description: Viatel payment plugin
 * Version: 1.0
 * Author: Urmat Zhenaliev
 * Author URI: http://anuri73.github.io
 * Author Email: urmat.zhenaliev@gmail.com
 */

include 'functions.php';
include 'Action.php';
include 'Asset.php';
include 'Config.php';
include 'Viatel.php';
include 'View.php';
include 'Profile.php';
include 'ShortCode.php';
include 'Viatel_Widget.php';
include 'Package.php';

$viatel = new Viatel();

$viatel->init();
