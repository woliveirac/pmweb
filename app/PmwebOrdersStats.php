<?php
/**
 * Created by PhpStorm.
 * User: Willian Correa (willianoliveirac96@gmail.com)
 * Date: 8/3/2019
 * Time: 12:34 PM
 */

require('../config/config.php');
require('../database/db.php');


/**
 * Sumarizações de dados transacionais de pedidos.
 */
class PmwebOrdersStats {

    /**
     * @var string $startDate Data de início, formato `Y-m-d` (ex, 2017-08-24).
     */
    private $startDate;

    /**
     * @var string $endDate Data final da consulta, formato `Y-m-d` (ex, 2017-08-24).
     */
    private $endDate;

    /**
     * @var DB $db classe responsável pela interação com o banco de dados
     */
    private $db;

    /**
     * PmwebOrdersStats constructor.
     */
    public function __construct()
    {
        $this->db = new DB(MYSQL_HOSTNAME, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DATABASE);
    }


    /**
     * Define o período inicial da consulta.
     *
     * @param String $date Data de início, formato `Y-m-d` (ex, 2017-08-24).
     * @throws Exception Caso a data de inicio informada estiver invalida.
     * @return void
     */
    public function setStartDate($date)
    {
        if(!$this->isDateFormatValid($date)) {
            throw new Exception ('Data de Inicio informada inválida. Por favor, informe uma data no formato Y-m-d, (ex, 2017-08-24)');
        }

        if(!$this->isStartDateValid($date)) {
            throw new Exception ('Data de inicio informada nao pode ser maior que a Data Final da consulta.');
        }

        $this->startDate = mysqli_real_escape_string($this->db->mysqli, $date);
    }

    /**
     * Define o período final da consulta.
     *
     * @param String $date Data final da consulta, formato `Y-m-d` (ex, 2017-08-24).
     * @throws Exception Caso a data final da consulta informada estiver invalida.
     * @return void
     */
    public function setEndDate($date)
    {
        if(!$this->isDateFormatValid($date)) {
            throw new Exception ('Data Final da Consulta informada inválida. Por favor, informe uma data no formato Y-m-d, (ex, 2017-08-24)');
        }

        if(!$this->isEndDateValid($date)) {
            throw new Exception ('Data Final informada nao pode ser menor que a Data Inicial da consulta.');
        }

        $this->endDate = mysqli_real_escape_string($this->db->mysqli, $date);
    }

    /**
     * Retorna o total de pedidos efetuados no período.
     *
     * @throws Exception
     * @return integer Total de pedidos.
     */
    public function getOrdersCount()
    {
        // Caso os itens não forem tratados como pedidos, mas, sim, como integrantes de um pedido, o SQL seria esse:
        /*$queryTotalOrders = 'SELECT SUM(1) AS total_orders FROM ( SELECT DISTINCT order_id FROM order_items $wherePeriod$) AS orders'; */

        $queryTotalOrders = 'SELECT SUM(1) as total_orders FROM order_items $wherePeriod$';

        $queryTotalOrdersFormatted = $this->formatSqlWithPeriod($queryTotalOrders);
        
        $queryResult = $this->db->queryFirstRow($queryTotalOrdersFormatted);

        $totalOrders = ($queryResult['total_orders'] === null ? 0 : $queryResult['total_orders']);

        return $totalOrders;
    }

    /**
     * Retorna a receita total de pedidos efetuados no período.
     *
     * @throws Exception
     * @return float Receita total no período.
     */
    public function getOrdersRevenue()
    {
        $queryTotalRevenue = 'SELECT SUM(price) as total_revenue FROM order_items $wherePeriod$';

        $queryTotalRevenueFormatted = $this->formatSqlWithPeriod($queryTotalRevenue);

        $queryResult = $this->db->queryFirstRow($queryTotalRevenueFormatted);

        $totalRevenue = ($queryResult['total_revenue'] === null ? 0 : $queryResult['total_revenue']);

        return $totalRevenue;
    }

    /**
     * Retorna o total de produtos vendidos no período (soma de quantidades).
     *
     * @throws Exception
     * @return int Total de produtos vendidos.
     */
    public function getOrdersQuantity()
    {

        $queryTotalQuantity = 'SELECT SUM(quantity) as total_quantity FROM order_items $wherePeriod$';

        $queryTotalQuantityFormatted = $this->formatSqlWithPeriod($queryTotalQuantity);

        $queryResult = $this->db->queryFirstRow($queryTotalQuantityFormatted);

        $totalQuantity = ($queryResult['total_quantity'] === null ? 0 : $queryResult['total_quantity']);

        return $totalQuantity;
    }

    /**
     * Retorna o preço médio de vendas (receita / quantidade de produtos).
     *
     * @throws Exception
     * @return float Preço médio de venda.
     */
    public function getOrdersRetailPrice()
    {

        $orders_revenue = $this->getOrdersRevenue();

        $orders_quantity = $this->getOrdersQuantity();

        $retailPriceFormatted = 0;

        if($orders_revenue>0 && $orders_quantity >0) {
            $retailPrice = ($orders_revenue / $orders_quantity);
            $retailPriceFormatted = number_format($retailPrice, 2);
        }

        return $retailPriceFormatted;
    }

    /**
     * Retorna o ticket médio de venda (receita / total de pedidos).
     *
     * @throws Exception
     * @return float Ticket médio.
     */
    public function getOrdersAverageOrderValue()
    {
        $orders_revenue = $this->getOrdersRevenue();

        $total_orders = $this->getOrdersCount();

        $averageOrderValueFormatted = 0;

        if($orders_revenue>0 && $total_orders >0) {
            $averageOrderValue = ($orders_revenue / $total_orders);
            $averageOrderValueFormatted = number_format($averageOrderValue, 2);
        }

        return $averageOrderValueFormatted;
    }

    /**
     * Retorna os dados dos pedidos consolidados em JSON.
     *
     * @param string $method método a ser usado para pegar a informação específica
     * @throws Exception
     * @return false|string
     */
    public function getOrdersInfo($method)
    {
        switch ($method) {
            case 'getOrdersCount':
                $order_report['orders']['count'] = $this->getOrdersCount();
                break;
            case 'getOrdersRevenue':
                $order_report['orders']['revenue'] = $this->getOrdersRevenue();
                break;
            case 'getOrdersQuantity':
                $order_report['orders']['quantity'] = $this->getOrdersQuantity();
                break;
            case 'getOrdersRetailPrice':
                $order_report['orders']['averageRetailPrice'] = $this->getOrdersRetailPrice();
                break;
            case 'getOrdersAverageOrderValue':
                $order_report['orders']['averageOrderValue'] = $this->getOrdersAverageOrderValue();
                break;
            default:
            case 'getAll':
                $order_report = [
                    'orders' => [
                        'count' => $this->getOrdersCount(),
                        'revenue' => $this->getOrdersRevenue(),
                        'quantity' => $this->getOrdersQuantity(),
                        'averageRetailPrice' => $this->getOrdersRetailPrice(),
                        'averageOrderValue' => $this->getOrdersAverageOrderValue(),
                    ]
                ];
        }

        return json_encode($order_report);
    }

    /**
     * Confere se a data informada é uma data válida no formato esperado.
     *
     * @param string $date (ex, 2017-08-24)
     * @param string $format default é Y-m-d
     * @return bool
     */
    private function isDateFormatValid($date, $format = 'Y-m-d')
    {
        try {

            $dateTime = DateTime::createFromFormat($format, $date);
            return ($dateTime && $dateTime->format($format) === $date);

        }catch (Exception $e) {
            die('Erro ao validar data: ' . $e->getMessage());
        }
    }

    /**
     * Confere se uma data de período foi informada
     *
     * @return bool
     */
    private function hasPeriodDefined()
    {
        return ($this->startDate || $this->endDate);
    }

    /**
     * Monta o WHERE necessário para aplicar o período informado na consulta dos pedidos,
     *
     * @return string $sqlWhere retorna o comando WHERe de sql de acordo com os períodos solicitados.
     */
    private function getSqlWherePeriod()
    {
        $whereAnd = '';
        $whereEndDate = '';
        $whereStartDate = '';

        if($this->startDate) {
            $whereStartDate .= "DATE_FORMAT(order_date, '%Y-%m-%d') >= '{$this->startDate}'";
        }

        if($this->endDate) {
            $whereEndDate .= "DATE_FORMAT(order_date, '%Y-%m-%d') <= '{$this->endDate}'";
        }

        if($this->startDate && $this->endDate) {
            $whereAnd = ' AND ';
        }

        $sqlWhere = "WHERE {$whereStartDate} {$whereAnd} {$whereEndDate}";

        return $sqlWhere;
    }

    /**
     * Confere se a Data Inicial da consulta é válida, pois, ela não pode ser maior que a data final.
     *
     * @param string $date
     * @return bool
     */
    private function isStartDateValid($date)
    {

        $valid = TRUE;

        if($this->endDate) {
            $dateTimeStart = DateTime::createFromFormat('Y-m-d', $date);
            $dateTimeEnd = DateTime::createFromFormat('Y-m-d', $this->endDate);

            if($dateTimeStart > $dateTimeEnd) {
                $valid = FALSE;
            }
        }

        return $valid;
    }

    /**
     * Confere se a Data Final da consulta é válida, pois, ela não pode ser menor que a data inicial.
     *
     * @param $date
     * @return bool
     */
    private function isEndDateValid($date)
    {

        $valid = TRUE;

        if($this->startDate) {
            $dateTimeStart = DateTime::createFromFormat('Y-m-d', $this->startDate);
            $dateTimeEnd = DateTime::createFromFormat('Y-m-d', $date);

            if($dateTimeEnd < $dateTimeStart) {
                $valid = FALSE;
            }
        }

        return $valid;
    }

    /**
     * Formata o SQL de pedidos para sempre inserir o período se necessário.
     *
     * @param string $sql
     * @return string SQL formatado com o where de período se necessário;
     */
    private function formatSqlWithPeriod($sql)
    {
        $wherePeriod = '';

        if($this->hasPeriodDefined()) {
            $wherePeriod = $this->getSqlWherePeriod();
        }

        $formattedSql = str_replace('$wherePeriod$', $wherePeriod, $sql);

        return $formattedSql;
    }
}
