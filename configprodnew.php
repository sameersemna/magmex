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
 
 
$stores=array(0,1,2);

$list=array();
//echo "'store','sku','attribute_set','configurable_attributes','type','qty','price','special_price','super_attribute_pricing','simples_skus'<br/>";
$list[]=array('store','sku','attribute_set','configurable_attributes','type','qty','price','special_price','super_attribute_pricing','simples_skus');

foreach($stores as $store){

	$sql = " SELECT * FROM `".getStoreFlatTable($store)."` WHERE (`type_id` = 'configurable'); ";
	$query  = mysql_query($sql,$link);


	while ( $obj = mysql_fetch_object($query)){
		
		$children=array();
		$qty=array();
		$chsql=" SELECT cpe.`sku`,csi.`qty` FROM `catalog_product_entity` cpe, `cataloginventory_stock_item` csi
		WHERE (cpe.`entity_id` IN (SELECT `product_id` FROM `catalog_product_super_link`
											WHERE `parent_id`='".$obj->entity_id."')) 
		AND (cpe.`entity_id` = csi.`product_id`)
		ORDER BY `sku`; ";
		
		$resultch=mysql_query($chsql); 
		while($row=mysql_fetch_object($resultch)){
			$children[]=$row->sku;
			$qty[]=(int)$row->qty;
		}
		
		$attrSQL=" SELECT `attribute_code`,`frontend_label` FROM `eav_attribute` ea
								WHERE ea.`attribute_id` IN (SELECT `attribute_id` FROM `catalog_product_super_attribute` 
															WHERE `product_id` = '".$obj->entity_id."'); ";//codes
															
		$attr=mysql_fetch_object(mysql_query($attrSQL));
		
		$sprattrpric=array();					
		$chsql=" SELECT aov.`value`,sap.`pricing_value`,sap.`is_percent`
					FROM `eav_attribute_option_value` aov, 
						`catalog_product_super_attribute_pricing` sap
					WHERE (aov.`option_id` IN (SELECT sap.`value_index` FROM `catalog_product_super_attribute_pricing` sap 
												WHERE (sap.`product_super_attribute_id` IN (SELECT `product_super_attribute_id` FROM `catalog_product_super_attribute` 
																								WHERE `product_id` = '".$obj->entity_id."')) 
																								AND (sap.`website_id`='0'))) 
					AND (sap.`product_super_attribute_id` IN (SELECT `product_super_attribute_id` FROM `catalog_product_super_attribute` WHERE `product_id` = '".$obj->entity_id."')) 
					AND (aov.`option_id`=sap.`value_index`) 
					AND (aov.`store_id`='0') 
					AND (sap.`website_id`='".$store."')
					ORDER BY aov.`value`, aov.`value_id`, aov.`value`, aov.`value_id`; ";//use 0 as store id for eav_attribute_option_value for default value
		
		$resultch=mysql_query($chsql); 
		
		while($row=mysql_fetch_object($resultch)){
			$sprattrpric[]=$row->value.':'.number_format($row->pricing_value,2,'.','').':'.$row->is_percent;
		}

		
		
		$sap=$attr->attribute_code.'::'.implode(';',$sprattrpric);
		/*
		echo getStoreCode($store)
					.','.$obj->sku
					.','.$attr->frontend_label
					.','.$attr->attribute_code
					.','.$obj->type_id
					.','.array_sum($qty)
					.','.number_format($obj->price,2,'.','')
					.','.number_format($obj->special_price,2,'.','')
					.','.$sap
					.','.implode(',',$children)
			.'<br/>';
		*/
		$list[]=array(
					getStoreCode($store)
					,$obj->sku
					,$attr->frontend_label
					,$attr->attribute_code
					,$obj->type_id
					,array_sum($qty)
					,number_format($obj->price,2,'.','')
					,number_format($obj->special_price,2,'.','')
					,$sap
					,implode(',',$children)
				);
		
		//break;
	}
	//break;
}

$filename='reports/csv/configprods/'.'configprod_'.getFileTimeStamp().'.csv';

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

SELECT cpe.*,'||',cpet.*,'|||',ea.*,'||||',aov.*,'||||',sap.*

FROM `catalog_product_entity` cpe, 
`catalog_product_entity_text` cpet, 
`eav_attribute` ea, 
`eav_attribute_option_value` aov, 
`catalog_product_super_attribute_pricing` sap

WHERE (cpe.`entity_id` IN (SELECT `product_id` FROM `catalog_product_super_link` WHERE `parent_id`='167')) 

AND (cpe.`entity_id`=cpet.`entity_id`)
AND (cpet.`attribute_id` = '57')

AND (ea.`attribute_id` IN (SELECT `attribute_id` FROM `catalog_product_super_attribute` WHERE `product_id` = '167'))

AND (aov.`option_id` IN (SELECT sap.`value_index` FROM `catalog_product_super_attribute_pricing` sap 
							WHERE (sap.`product_super_attribute_id` IN (SELECT `product_super_attribute_id` FROM `catalog_product_super_attribute` 
																			WHERE `product_id` = '167')) 
																			AND (sap.`website_id`='0'))) 
AND (aov.`store_id`='0')

AND (sap.`product_super_attribute_id` IN (SELECT `product_super_attribute_id` FROM `catalog_product_super_attribute` WHERE `product_id` = '167')) 
AND (aov.`option_id`=sap.`value_index`) 
AND (aov.`store_id`='0') 
AND (sap.`website_id`='0')

ORDER BY cpe.`sku`, aov.`value`, aov.`value_id`, aov.`value`, aov.`value_id`;








SELECT aov.*,'||||',sap.*

FROM `eav_attribute_option_value` aov, 
`catalog_product_super_attribute_pricing` sap

WHERE (aov.`option_id` IN (SELECT sap.`value_index` FROM `catalog_product_super_attribute_pricing` sap 
							WHERE (sap.`product_super_attribute_id` IN (SELECT `product_super_attribute_id` FROM `catalog_product_super_attribute` 
																			WHERE `product_id` = '167')) 
																			AND (sap.`website_id`='0'))) 
AND (aov.`store_id`='0')

AND (sap.`product_super_attribute_id` IN (SELECT `product_super_attribute_id` FROM `catalog_product_super_attribute` WHERE `product_id` = '167')) 
AND (aov.`option_id`=sap.`value_index`) 
AND (aov.`store_id`='0') 
AND (sap.`website_id`='0')

ORDER BY aov.`value`, aov.`value_id`, aov.`value`, aov.`value_id`;

*/