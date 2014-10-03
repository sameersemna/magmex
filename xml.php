<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);
echo 'SHS';
/* 
 *   Magento Exporter by Sebastijan Placento 
 *   E-mail: 9a3bsp@gmail.com
 * 
 */
$PATH=$_SERVER['DOCUMENT_ROOT'].'/indiacircus/magmi/export/';//'/home/ixtremes/public_html/web/';    //absolute path
$url='http://192.168.1.13/indiacircus/';
$baseurl=$url.'index.php/';
$mediaurl=$url.'media/';
$server="localhost";
$username="root";
$password="root";
$database="devindiacircus";

//////////////////////////////////////////////
$link = mysql_connect( $server,$username,$password) or die("Unable to connect to the database...");
mysql_set_charset('utf8');
mysql_selectdb($database,$link);
 
 
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
 
$sqlStock="SELECT * FROM `cataloginventory_stock_item`";    // stock
$stock=null;
$query  = mysql_query($sqlStock,$link);
while ( $obj = mysql_fetch_object($query)){
    $stock[$obj->product_id]=(int)$obj->qty;
}
// key is product_id and value is qty
 
// XML init;
$doc = new DOMDocument('1.0', 'UTF-8');    // add header
$doc->formatOutput = true;
 
$r = $doc->createElement( "products" );
$doc->appendChild( $r );
 
$sql = "SELECT * FROM `catalog_product_flat_1`";
$query  = mysql_query($sql,$link);
while ( $obj = mysql_fetch_object($query)){
    $b = $doc->createElement( "product" );
	
	if(isset($categoriesProds[$obj->entity_id])){ 
		if(isset($categories[end($categoriesProds[$obj->entity_id])])){ 
			$category=$categories[end($categoriesProds[$obj->entity_id])];  // last category
		}	
	}
    $opis=mysql_fetch_object(mysql_query("SELECT value FROM `catalog_product_entity_text` WHERE entity_id=".$obj->entity_id)); //  product description
    $podatci = array(
            'sku' =>     $obj->sku,
            'category' =>    $category,
            'name' =>  $obj->name,
            'special_price' => number_format($obj->special_price,2,'.',',') .' kn',
            'remaining' => $stock[$obj->entity_id],
            'url' => $baseurl.$obj->url_path,
            'small_image' =>$mediaurl.'catalog/product'.$obj->small_image,
            'description'  =>  $opis->value
    );
    foreach($podatci as $key => $value){
        createChild($key, $value);
    }
    $r->appendChild( $b );
}
$doc->save($PATH."export.xml");
 
function createChild($childName,$data){
    global $b;
    global $doc;
    $data=utf8_decode($data);
    $child = $doc->createElement( $childName );
    $child->appendChild(
            $doc->createTextNode($data)
    );
    $b->appendChild( $child );
}