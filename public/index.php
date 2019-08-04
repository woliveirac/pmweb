<?php
/**
 * Created by PhpStorm.
 * User: Willian Correa (willianoliveirac96@gmail.com)
 * Date: 8/3/2019
 * Time: 12:08 PM
 */

die('
    <h1>Welcome!</h1>
    <h3>Para acessar a API de relatório de pedidos e ter uma visão geral de todos os pedidos <a href="orders_report.php" target="_blank">clique aqui.</a></h3>
    <p>Lembrando que os parâmetros GET aceitos são: <strong>startDate</strong>, <strong>endDate</strong> e <strong>method</strong></p>
    
    <p>O parâmetro <strong>startDate</strong> recebe uma <strong>data</strong> válida no formato <strong>Y-m-d</strong></p>
    <p>O parâmetro <strong>endDate</strong> recebe uma <strong>data</strong> válida no formato <strong>Y-m-d</strong></p>
    
    <p>O parâmetro <strong>method</strong> tem as seguintes opções:</p>
    <ul>
        <li><a href="orders_report.php?method=getOrdersCount" target="_blank">getOrdersCount</a></li>
        <li><a href="orders_report.php?method=getOrdersRevenue" target="_blank">getOrdersRevenue</a></li>
        <li><a href="orders_report.php?method=getOrdersQuantity" target="_blank">getOrdersQuantity</a></li>
        <li><a href="orders_report.php?method=getOrdersRetailPrice" target="_blank">getOrdersRetailPrice</a></li>
        <li><a href="orders_report.php?method=getOrdersAverageOrderValue" target="_blank">getOrdersAverageOrderValue</a></li>
    </ul>
');