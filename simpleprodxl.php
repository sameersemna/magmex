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
header('Content-disposition: attachment; filename=simpleproducts_'.date('Ymd_his').'.xls');


$PATH=$_SERVER['DOCUMENT_ROOT'].'/indiacircus/magmi/export/';//'/home/ixtremes/public_html/web/';    //absolute path

if(($_SERVER['SERVER_NAME'] == 'localhost') || ($_SERVER['SERVER_NAME'] == '127.0.0.1')  || ($_SERVER['SERVER_NAME'] == '192.168.1.13')){

	$url='http://'.$_SERVER['SERVER_NAME'].'/indiacircus/';
	$baseurl=$url.'index.php/';
	$mediaurl=$url.'media/';
	$server="localhost";
	$username="root";
	$password="root";
	$database="devindiacircus";

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
 
/*
$sqlCategories="SELECT * FROM `catalog_category_entity_varchar` WHERE `attribute_id` =35 AND `entity_type_id` =3";    //all categories
$categories=null;
$query  = mysql_query($sqlCategories,$link);
while ( $obj = mysql_fetch_object($query)){
    $categories[$obj->entity_id]=$obj->value;
}
// so key will be array key, and value will be Category name (more info below)
 
 
$sqlProductsWithCats="SELECT * FROM `catalog_category_product` ORDER BY product_id"; // products with category id's    
$categoriesProds=null;
$query  = mysql_query($sqlProductsWithCats,$link);
while ( $obj = mysql_fetch_object($query)){
    $categoriesProds[$obj->product_id][]=$obj->category_id;
} // key will be product_id and value will be array with categories id
*/

$sqlStock="SELECT * FROM `cataloginventory_stock_item`";    // stock
$stock=null;
$query  = mysql_query($sqlStock,$link);
while ( $obj = mysql_fetch_object($query)){
    $stock[$obj->product_id]=(int)$obj->qty;
}
// key is product_id and value is qty


$stores=array(1);//(0,1,2);

echoConcat('<table>');

$list=array();
//echo "'sku','price','special_price','quantity'<br/>";
$arr=array('store','sku','name','qty','price','special_price','status','visibility');
echoArrRow($arr);
$list[]=$arr;

foreach($stores as $store){

	$sqlVis=" SELECT *,cpei.`entity_id` AS 'product_id', cpei.`value` AS 'visibility', cpei2.`value` AS 'status' FROM `catalog_product_entity_int` cpei, `catalog_product_entity_int` cpei2 
					WHERE cpei.`attribute_id`=(SELECT `attribute_id` FROM `eav_attribute` WHERE (`attribute_code` = 'visibility'))
					AND cpei2.`attribute_id`=(SELECT `attribute_id` FROM `eav_attribute` WHERE (`frontend_label` = 'Status')) 
					AND (cpei.`entity_id` = cpei2.`entity_id`) 
					AND (cpei.`store_id` = cpei2.`store_id`) 
					AND (cpei.`store_id` = '0'); ";
	//$sqlVis="SELECT * FROM `eav_attribute`;";
	$visibility=array();
	$status=array();
	$query  = mysql_query($sqlVis,$link);
	while ( $obj = mysql_fetch_object($query)){
		$visibility[$obj->product_id]=$obj->visibility;	
		$status[$obj->product_id]=$obj->status;	
	}
	
	
	$sql = "SELECT * FROM `".getStoreFlatTable($store)."`";
	$query  = mysql_query($sql,$link);
	
	while ( $obj = mysql_fetch_object($query)){
		
		if(isset($categoriesProds[$obj->entity_id])){ 
			if(isset($categories[end($categoriesProds[$obj->entity_id])])){ 
				$category=$categories[end($categoriesProds[$obj->entity_id])];  // last category
			}	
		}
				
		/*
		echo '<pre>';
		print_r($obj);
		echo '</pre>';
		exit;
		*/
		
		
		if($obj->type_id == 'simple'){
			
			$arr=array(
						getStoreCode($store)
						,$obj->sku
						//,$category
						,$obj->name
						//,$obj->type_id
						,$stock[$obj->entity_id]
						,number_format($obj->price,2,'.','')
						,number_format($obj->special_price,2,'.','')					
						//,$baseurl.$obj->url_path
						//,$mediaurl.'catalog/product'.$obj->small_image
						,getStatusText($status[$obj->entity_id])
						,getVisibilityText($visibility[$obj->entity_id])
					);
			echoArrRow($arr);
			$list[]=$arr;
		}
		//break;
	}
}

$filename='reports/csv/simpleprods/'.'simpleprod_'.getFileTimeStamp().'.csv';

//saveCSV($filename,$list);

//echo '<a href="'.$filename.'">Get file</a>';

function saveCSV($filname,$list){
	$fp = fopen($filname, 'w');
	foreach ($list as $fields) {
		fputcsv($fp, $fields,',',"'");
	}
	fclose($fp);
}

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
function getStatusText($i){
	if($i==1){
		return 'Enabled';
	}elseif($i==2){
		return 'Disabled';
	}
	return 'error';
}
function getVisibilityText($i){
	if($i==1){
		return 'Not Visible Individually';
	}elseif($i==2){
		return 'Catalog';
	}elseif($i==3){
		return 'Search';
	}elseif($i==4){
		return 'Catalog/Search';
	}
	return 'error';
}
 
 /*
 
 stdClass Object
(
    [entity_id] => 75
    [attribute_set_id] => 36
    [type_id] => simple
    [applied_tothetrade] => 1
    [cost] => 
    [created_at] => 2012-09-13 06:50:14
    [enable_googlecheckout] => 1
    [gift_message_available] => 0
    [has_options] => 0
    [image_label] => 
    [is_imported] => 0
    [popular_products] => 3
    [popular_products_value] => No
    [measurements] => 
    [cod] => 1
    [prod_details] => 
    [est_delivery_date] => 
    [gift_amount] => 
    [is_recurring] => 0
    [links_exist] => 
    [links_purchased_separately] => 
    [links_title] => 
    [name] => Colour Coats Coasters - (Set of 6)
    [news_from_date] => 
    [news_to_date] => 
    [price] => 799.0000
    [price_type] => 
    [price_view] => 
    [recurring_profile] => 
    [required_options] => 0
    [returns] => 1
    [coming_soon] => 2188
    [coming_soon_value] => No
    [shipment_type] => 
    [shipping_type] => 
    [shipping_type_value] => 
    [ships_in] => 7 Business Days
    [short_description] => Acrylic Coasters
    [sku] => 11120
    [sku_type] => 
    [small_image] => /1/1/11120-k23-2.jpg
    [small_image_label] => 
    [special_from_date] => 2013-01-30 00:00:00
    [special_price] => 688.0000
    [special_to_date] => 
    [tax_class_id] => 0
    [thumbnail] => /1/1/11120-k23-2.jpg
    [thumbnail_label] => 
    [updated_at] => 2014-02-02 01:07:38
    [url_key] => kuheli-colour-coats-coasters-set-of-6
    [url_path] => kuheli-colour-coats-coasters-set-of-6.html
    [visibility] => 3
    [volume_weight] => 
    [weight] => 350.0000
    [weight_type] => 
)
 
 */
