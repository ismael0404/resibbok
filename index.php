<?php
// index.php
require_once 'config/config.php';
require_once 'config/Database.php';
require_once 'core/Router.php';
require_once 'core/Controller.php';
require_once 'app/helpers/image_helper.php';

// Init Router
$router = new Router();
