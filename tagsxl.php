<?php 

ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);

require_once('common.php');

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

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");
header("Content-Transfer-Encoding: binary ");
header('Content-type: application/ms-excel');
header('Content-disposition: attachment; filename=tags_'.date('Ymd_his').'.xls');


$PATH=$_SERVER['DOCUMENT_ROOT'].'/indiacircus/magmi/export/';//'/home/ixtremes/public_html/web/';    //absolute path

if(($_SERVER['SERVER_NAME'] == 'localhost') || ($_SERVER['SERVER_NAME'] == '127.0.0.1')  || ($_SERVER['SERVER_NAME'] == '192.168.1.13')){

	$url='http://'.$_SERVER['SERVER_NAME'].'/indiacircus/';
	$baseurl=$url.'index.php/';
	$mediaurl=$url.'media/';
	$server="localhost";
	$username="root";
	$password="root";
	$database="devindiacircus";

}elseif($_SERVER['SERVER_NAME'] == 'app.indiacircus.com'){

	$url='http://'.$_SERVER['SERVER_NAME'].'/';
	$baseurl=$url.'index.php/';
	$mediaurl=$url.'media/';
	$server="indiacircustest.clwm1ltywiik.us-east-1.rds.amazonaws.com";
	$username="root";
	$password="icdbadmin";
	$database="indiacircustestdb";

}else{

	$url='http://'.$_SERVER['SERVER_NAME'].'/';
	$baseurl=$url.'index.php/';
	$mediaurl=$url.'media/';
	$server="indiacircusdb.cf24hpnclzb6.ap-southeast-1.rds.amazonaws.com";
	$username="root";
	$password="icdbadmin";
	$database="indiacircus";
	
}

//////////////////////////////////////////////
$link = mysql_connect( $server,$username,$password) or die("Unable to connect to the database...");
mysql_set_charset('utf8');
mysql_selectdb($database,$link);
 
echoConcat('<table>');

$taglist=array();

$arr=array('sku','tags');
echoArrRow($arr);

$sql = " SELECT `tag_relation`.`product_id`, `catalog_product_flat_1`.`sku`, `tag`.`name` FROM `tag`, `tag_relation`, `catalog_product_flat_1` 
			WHERE (`tag`.`tag_id` = `tag_relation`.`tag_id`) AND (`catalog_product_flat_1`.`entity_id` = `tag_relation`.`product_id`); ";
$query  = mysql_query($sql,$link);

while ( $obj = mysql_fetch_object($query)){
	if(!isset($taglist[$obj->product_id])){	
		$taglist[$obj->sku]=$obj->name;
	}else{
		$taglist[$obj->sku].=",".$obj->name;
	}
}

foreach($taglist as $k=>$val){
	echoArrRow(array($k,$val));
}

echoConcat('</table>');
	
function echoConcat($str){
	echo $str;
}
function echoArrRow($arr){
	echoConcat('<tr>');
	foreach($arr as $a){
		echoConcat('<td>'.$a.'</td>');
	}
	echoConcat('</tr>');
}

