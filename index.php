<?php

use controller\BaseController;

require dirname(__FILE__) . '/controller/BaseController.php';

date_default_timezone_set('PRC');

$mainController = BaseController::checkGETToGetController($_GET['project']);

$mainController->fromGETToRoute($_GET['status'], $_GET);

?>
