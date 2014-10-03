<?php 

ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);

require_once('config.php');
require_once('common.php');


 
 
$sqlCategories="SELECT * FROM `catalog_category_entity_varchar` WHERE `attribute_id` =35 AND `entity_type_id` =3";    //all categories
$categories=null;
$query  = mysqli_query($link, $sqlCategories);
while ( $obj = mysqli_fetch_object($query)){
    $categories[$obj->entity_id]=$obj->value;
}
// so key will be array key, and value will be Category name (more info below)
 
 
$sqlProductsWithCats="SELECT * FROM `catalog_category_product` ORDER BY product_id"; // products with category id's    
$categoriesProds=null;
$query  = mysqli_query($link, $sqlProductsWithCats);
while ( $obj = mysqli_fetch_object($query)){
    $categoriesProds[$obj->product_id][]=$obj->category_id;
} // key will be product_id and value will be array with categories id
 
$sqlStock="SELECT * FROM `cataloginventory_stock_item`";    // stock
$stock=null;
$query  = mysqli_query($link, $sqlStock);
while ( $obj = mysqli_fetch_object($query)){
    $stock[$obj->product_id]=(int)$obj->qty;
}
// key is product_id and value is qty
 
$sql = "SELECT * FROM `catalog_product_entity`;";//`catalog_product_flat_1`";
$query  = mysqli_query($link, $sql);

$list=array();
echo "'store','sku','attribute_set','configurable_attributes','type','qty','price','special_price','super_attribute_pricing','simples_skus'<br/>";
$list[]=array('store','sku','attribute_set','configurable_attributes','type','qty','price','special_price','super_attribute_pricing','simples_skus');

while ( $obj = mysqli_fetch_object($query)){
    
	if(isset($categoriesProds[$obj->entity_id])){ 
		if(isset($categories[end($categoriesProds[$obj->entity_id])])){ 
			$category=$categories[end($categoriesProds[$obj->entity_id])];  // last category
		}	
	}
    $opis=mysqli_fetch_object(mysqli_query($link, " SELECT value FROM `catalog_product_entity_text` WHERE entity_id=".$obj->entity_id."; ")); //  product description
	
	
	if($obj->type_id == 'configurable'){
	
		$children=array();
		$childrenSQL=" SELECT `sku` FROM `catalog_product_entity` 
		WHERE (`entity_id` IN (SELECT `product_id` FROM `catalog_product_super_link`
											WHERE `parent_id`='".$obj->entity_id."')) ORDER BY `sku`; ";
		$resultch=mysqli_query($link, $childrenSQL); 
		while($row=mysqli_fetch_object($resultch)){
			$children[]=$row->sku;
		}
		
		$attrSQL=" SELECT `attribute_code`,`frontend_label` FROM `eav_attribute` ea
								WHERE ea.`attribute_id` IN (SELECT `attribute_id` FROM `catalog_product_super_attribute` 
															WHERE `product_id` = '".$obj->entity_id."'); ";//codes
															
		$attr=mysqli_fetch_object(mysqli_query($link, $attrSQL));
		
		$sprattrpric=array();
				
		$childrenSQL=" SELECT aov.`value`,sap.`pricing_value`,sap.`is_percent`,aov.* FROM `eav_attribute_option_value` aov, `catalog_product_super_attribute_pricing` sap
								WHERE (sap.`product_super_attribute_id` IN (SELECT `product_super_attribute_id` FROM `catalog_product_super_attribute` 
																			WHERE `product_id` = '".$obj->entity_id."'))
								AND (aov.`option_id`=sap.`value_index`)
								AND (aov.`store_id`='0')
								AND (sap.`website_id`='0')
								ORDER BY aov.`value`, aov.`value_id`; ";//prices
		
		$resultch=mysqli_query($link, $childrenSQL); 
		while($row=mysqli_fetch_object($resultch)){
			$sprattrpric[]=$row->value.':'.number_format($row->pricing_value,2,'.',',').':'.$row->is_percent;
		}

		/*
		echo '<pre>';
		print_r($sprattrpric);
		echo '</pre>';
		exit;
		*/
		
		$sap=$attr->attribute_code.'::'.implode(';',$sprattrpric);
		
		echo "'".'admin'."'"
			.",'".$obj->sku."'"
			.",'".$attr->frontend_label."'"
			.",'".$attr->attribute_code."'"
			//.','.$category
			//.','.$obj->name
			.",'".$obj->type_id."'"
			.",'".$stock[$obj->entity_id]."'"
			//*.",'".number_format($obj->price,2,'.',',')."'"
			//*.",'".number_format($obj->special_price,2,'.',',')."'"
			.','."'".$sap."'"
			.','."'".implode(',',$children)."'"
			//.','.$baseurl.$obj->url_path
			//.','.$mediaurl.'catalog/product'.$obj->small_image
			//.','.$opis->value
			.'<br/>';
		
		$list[]=array(
					$obj->sku
					//,$category
					//,$obj->name
					//,$obj->type_id
					,$stock[$obj->entity_id]
					//*,number_format($obj->price,2,'.','')
					//*,number_format($obj->special_price,2,'.','')					
					//,$baseurl.$obj->url_path
					//,$mediaurl.'catalog/product'.$obj->small_image
					//,$opis->value
				);
	}
	
}


$filename='reports/csv/configprods/'.'configprod_'.getFileTimeStamp().'.csv';

saveCSV($filename,$list);

echo '<a href="'.$filename.'">Get file</a>';


 
 
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
