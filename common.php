<?php 

function getFileTimeStamp(){
	return date('Ymd_His');
}
function getStoreCode($strid){
	if($strid == 0){
		return 'admin';
	}elseif($strid == 1){
		return 'english';
	}elseif($strid == 2){
		return 'icworldviewcode';
	}elseif($strid == 4){
		return 'retailplus_india';
	}
}
function getStoreFlatTable($strid){
	if($strid == 0){
		return 'catalog_product_flat_1';
	}elseif($strid == 1){
		return 'catalog_product_flat_1';
	}elseif($strid == 2){
		return 'catalog_product_flat_2';
	}elseif($strid == 4){
		return 'catalog_product_flat_1';
	}
}

function getQueryRows($sql){
	global $link;
	$rows = array();
	$query  = mysqli_query($link, $sql);
	while ( $obj = mysqli_fetch_object($query)){
		$rows[$obj->entity_id]=$obj->value;
	}
	return $rows;
}

function saveCSV($filname,$list){
	$fp = fopen($filname, 'w');
	foreach ($list as $fields) {
		fputcsv($fp, $fields,',',"'");
	}
	fclose($fp);
}
