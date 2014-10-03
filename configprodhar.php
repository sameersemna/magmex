<?php 

ini_set('display_errors', 1);
ini_set('memory_limit', '5000M');
ini_set('max_execution_time', 30*60);
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
 
 
$maxassprod=4;
$stores=isset($_GET['stores'])?explode(',',$_GET['stores']):array(0);

$list=array();
//echo "'store','sku','attribute_set','configurable_attributes','type','qty','price','special_price','super_attribute_pricing','simples_skus'<br/>";
//$list[]=array('store','sku','attribute_set','configurable_attributes','type','qty','price','special_price','super_attribute_pricing','simples_skus');
$list0=array('store','sku','price','special_price','super_attribute');
for($i=1;$i<=$maxassprod;$i++){
	$list0[]='sap_'.$i.'_size';
	$list0[]='sap_'.$i.'_price';
	$list0[]='row_'.$i.'_sku';
	$list0[]='row_'.$i.'_price';
	$list0[]='row_'.$i.'_special_price';
}

$list[]=$list0;

foreach($stores as $store){

	$sql = "SELECT * FROM `".getStoreFlatTable($store)."` WHERE (`type_id` = 'configurable'); ";
	$query  = mysql_query($sql,$link);


	while ( $obj = mysql_fetch_object($query)){
		
		$children=array();
		/*
		$chsql=" SELECT cpe.`sku`,ced.`value` AS 'price',ced2.`value` AS `special_price` FROM `catalog_product_entity` cpe, `catalog_product_entity_decimal` ced, `catalog_product_entity_decimal` ced2
		WHERE (cpe.`entity_id` IN (SELECT `product_id` FROM `catalog_product_super_link`
											WHERE `parent_id`='".$obj->entity_id."'))
		AND (cpe.`entity_id` = ced.`entity_id`)
		AND (ced.`attribute_id` = '60')
		AND (ced.`store_id`='".$store."')
		AND (cpe.`entity_id` = ced2.`entity_id`)
		AND (ced2.`attribute_id` = '65')
		AND (ced2.`store_id`='".$store."')
		AND (ced.`store_id`=ced2.`store_id`)
		ORDER BY `sku`; ";
		*/
		$chsql=" SELECT cpe.`sku`,
				(SELECT `value` FROM `catalog_product_entity_decimal`  
				WHERE (cpe.`entity_id` = `entity_id`) 
				AND (`attribute_id` = '60') 
				AND (`store_id`='".$store."')) AS 'price',
				(SELECT `value` FROM `catalog_product_entity_decimal`  
				WHERE (cpe.`entity_id` = `entity_id`) 
				AND (`attribute_id` = '65') 
				AND (`store_id`='".$store."')) AS `special_price`
				FROM `catalog_product_entity` cpe
				WHERE (cpe.`entity_id` IN (SELECT `product_id` FROM `catalog_product_super_link` WHERE `parent_id`='".$obj->entity_id."')) 
				ORDER BY `sku`; ";
		
		$resultch=mysql_query($chsql); 
		while($row=mysql_fetch_object($resultch)){
			$children[]=array('sku'=>$row->sku, 'price'=>number_format($row->price,2,'.',''), 'special_price'=>number_format($row->special_price,2,'.',''));
		}
		
		$attrSQL=" SELECT `attribute_code`,`frontend_label` FROM `eav_attribute` ea
								WHERE ea.`attribute_id` IN (SELECT `attribute_id` FROM `catalog_product_super_attribute` 
															WHERE `product_id` = '".$obj->entity_id."'); ";//codes
															
		$attr=mysql_fetch_object(mysql_query($attrSQL));
		
		$attrOpVal=array();
		/*
		$chsql=" SELECT aov.`value` FROM `eav_attribute_option_value` aov, `catalog_product_super_attribute_pricing` sap
								WHERE (sap.`product_super_attribute_id` IN (SELECT `product_super_attribute_id` FROM `catalog_product_super_attribute` 
																			WHERE `product_id` = '".$obj->entity_id."'))
								AND (aov.`option_id`=sap.`value_index`)
								AND (aov.`store_id`='0')
								AND (sap.`website_id`='0')
								ORDER BY aov.`value`, aov.`value_id`; ";//codes
		*/
		
		$chsql=" SELECT aov.`value` FROM `eav_attribute_option_value` aov 									
								WHERE (aov.`option_id` IN (SELECT sap.`value_index` FROM `catalog_product_super_attribute_pricing` sap 
																WHERE (sap.`product_super_attribute_id` IN (SELECT `product_super_attribute_id` FROM `catalog_product_super_attribute` 
																			WHERE `product_id` = '".$obj->entity_id."'))
																AND (sap.`website_id`='0')))
								AND (aov.`store_id`='0')									
								ORDER BY aov.`value`, aov.`value_id`; ";//codes
														
		$resultch=mysql_query($chsql); 
		while($row=mysql_fetch_object($resultch)){
			$attrOpVal[]=$row->value;
		}
		
		/*
		echo '<pre>';
		print_r($attrOpVal);
		echo '</pre>';
		exit;
		*/
		
		$sprattrpric=array();							
		$chsql=" SELECT aov.`value`,sap.`pricing_value`,sap.`is_percent` FROM `eav_attribute_option_value` aov, `catalog_product_super_attribute_pricing` sap
								WHERE (sap.`product_super_attribute_id` IN (SELECT `product_super_attribute_id` FROM `catalog_product_super_attribute` 
																			WHERE `product_id` = '".$obj->entity_id."'))
								AND (aov.`option_id`=sap.`value_index`)
								AND (aov.`store_id`='".$store."')
								AND (sap.`website_id`='".$store."')
								ORDER BY aov.`value`, aov.`value_id`; ";//prices
		
		$resultch=mysql_query($chsql); 
		$i=0;
		while($row=mysql_fetch_object($resultch)){
			//fileAppend($i.'>'.$store.']'.$obj->entity_id.' '.$obj->sku.')'.$attrOpVal[$i].':'.number_format($row->pricing_value,2,'.','').':'.$row->is_percent.'<br/>');
			//$sprattrpric[]=$attrOpVal[$i].':'.number_format($row->pricing_value,2,'.','').':'.$row->is_percent;
			$sprattrpric[]=number_format($row->pricing_value,2,'.','');
			$i++;
		}

		
		
		//$sap=$attr->attribute_code.'::'.implode(';',$sprattrpric);
		/*
		echo "'".'admin'."'"
			.",'".$obj->sku."'"
			.",'".$attr->frontend_label."'"
			.",'".$attr->attribute_code."'"
			//.','.$category
			//.','.$obj->name
			.",'".$obj->type_id."'"
			.",'".$stock[$obj->entity_id]."'"
			.",'".number_format($obj->price,2,'.',',')."'"
			.",'".number_format($obj->special_price,2,'.',',')."'"
			.','."'".$sap."'"
			.','."'".implode(',',$children)."'"
			//.','.$baseurl.$obj->url_path
			//.','.$mediaurl.'catalog/product'.$obj->small_image
			//.','.$opis->value
			.'<br/>';
		*/
		/*
		$list1=array(
					getStoreCode($store)
					,$obj->sku
					,$attr->frontend_label
					,$attr->attribute_code
					,$obj->type_id
					,$stock[$obj->entity_id]
					,number_format($obj->price,2,'.','')
					,number_format($obj->special_price,2,'.','')
					,$sap
					,implode(',',$children)
				);
		*/	
		$list1=array(
					getStoreCode($store)
					,$obj->sku
					,number_format($obj->price,2,'.','')
					,number_format($obj->special_price,2,'.','')
					,$attr->attribute_code
				);
		for($i=1;$i<=$maxassprod;$i++){
			$j=$i-1;
			if($j<count($children)){
				$list1[]=($j<count($attrOpVal))?$attrOpVal[$j]:'';
				$list1[]=($j<count($sprattrpric))?$sprattrpric[$j]:'';
				$list1[]=$children[$j]['sku'];
				$list1[]=$children[$j]['price'];
				$list1[]=$children[$j]['special_price'];
			}else{
				$list1[]='';
				$list1[]='';
				$list1[]='';
				$list1[]='';
				$list1[]='';
			}
		}
				
		$list[]=$list1;
		//break;
		
		
	}
}

$filename='reports/csv/configprods/'.'configprodhar_'.getFileTimeStamp().'.csv';

saveCSV($filename,$list);

echo '<a href="'.$filename.'">Get file</a>';

function saveCSV($filname,$list){
	$fp = fopen($filname, 'w');
	$csv='';
	foreach ($list as $fields) {
		//fputcsv($fp, $fields,',',"'");
		$csv.=arrayToCsv($fields, ',', "'",  true)."\r\n";
	}
	fwrite($fp, $csv);
	fclose($fp);
}

function arrayToCsv( array &$fields, $delimiter = ';', $enclosure = '"', $encloseAll = false, $nullToMysqlNull = false ) {
    $delimiter_esc = preg_quote($delimiter, '/');
    $enclosure_esc = preg_quote($enclosure, '/');

    $output = array();
    foreach ( $fields as $field ) {
        if ($field === null && $nullToMysqlNull) {
            $output[] = 'NULL';
            continue;
        }

        // Enclose fields containing $delimiter, $enclosure or whitespace
        if ( $encloseAll || preg_match( "/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $field ) ) {
            $output[] = $enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure;
        }
        else {
            $output[] = $field;
        }
    }

    return implode( $delimiter, $output );
}

function fileAppend($str){
	$myFile = "testFile.txt";
	$fh = fopen($myFile, 'a') or die("can't open file");
	$stringData = $str."\n";
	fwrite($fh, $stringData);
	fclose($fh);
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
