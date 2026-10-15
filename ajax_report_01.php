<?php

header('Content-Type: application/json; charset=utf-8');

$site_db = 'eshop';
@include_once '/website/include/pub_function.php';
@include_once('/website/class/' . $site_db . '_info_class.php');
require_once __DIR__ . '/contractitem_report_data.php';

$mDB = new MywebDB();
$rows = contractitem_report_rows(
    $mDB,
    $_GET['contract_id'] ?? '',
    $_GET['annual_month'] ?? ''
);
$mDB->remove();

echo json_encode(['data' => $rows]);
