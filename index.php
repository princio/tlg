<?php

require_once './dumper/autoload.php';
require_once './utils.php';
require_once './Board.php';
require_once './BoardMS.php';

preg_match('~/(\w+)/(\w+)?~', $_SERVER['REQUEST_URI'], $ms);

$page = $ms[1] ?? '';
$id = $ms[2] ?? '';

if($page === '') {
    $page = 'home';
    ob_start();
    include_once($page.'.php');
    $body = ob_get_clean();
}
else {
    extract(['page_id' => $id]);
    ob_start();
    include_once($page.'.php');
    $body = ob_get_clean();
}
extract(['body' => $body]);
include_once('layout.php');

