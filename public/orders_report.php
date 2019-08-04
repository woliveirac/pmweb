<?php
/**
 * Created by PhpStorm.
 * User: Willian Correa (willianoliveirac96@gmail.com)
 * Date: 8/3/2019
 * Time: 5:13 PM
 */

require('../app/PmwebOrdersStats.php');

try {

    $PmwebOrdersStats = new PmwebOrdersStats();

    if(isset($_GET['startDate'])) {
        $PmwebOrdersStats->setStartDate($_GET['startDate']);
    }

    if(isset($_GET['endDate'])) {
        $PmwebOrdersStats->setEndDate($_GET['endDate']);
    }

    $method = '';
    if(isset($_GET['method'])) {
        $method = $_GET['method'];
    }

    $response = $PmwebOrdersStats->getOrdersInfo($method);

    header('Content-Type: application/json');
    echo $response;

}catch (Exception $e) {
    die($e->getMessage());
}

