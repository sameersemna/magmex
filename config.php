<?php 
date_default_timezone_set('Asia/Calcutta');

//echo 'SHS';

/*
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");
header("Content-Transfer-Encoding: binary ");
header('Content-type: text/csv');
header('Content-disposition: attachment; filename=simpleprodsexp.csv');
*/


$PATH=$_SERVER['DOCUMENT_ROOT'].'/indiacircus/magmi/export/';//'/home/ixtremes/public_html/web/';    //absolute path
$url='http://192.168.1.13/indiacircus/';
$baseurl=$url.'index.php/';
$mediaurl=$url.'media/';
$server="localhost";
$username="root";
$password="";
$database="magento_sample";

//////////////////////////////////////////////
$link = mysqli_connect( $server,$username,$password, $database) or die("Unable to connect to the database...");
mysqli_set_charset($link, 'utf8');
//mysqli_selectdb($database,$link);
