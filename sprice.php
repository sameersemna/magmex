<?php 

ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);

require_once('common.php');

//echo 'SHS';


$PATH=$_SERVER['DOCUMENT_ROOT'].'/indiacircus/magmi/export/';//'/home/ixtremes/public_html/web/';    //absolute path
$url='http://192.168.1.13/indiacircus/';
$baseurl=$url.'index.php/';
$mediaurl=$url.'media/';
//mysql_connect('indiacircusdb.cf24hpnclzb6.ap-southeast-1.rds.amazonaws.com','root','icdbadmin'); //Live
//mysql_connect('icbetadbdec13i.clwm1ltywiik.us-east-1.rds.amazonaws.com','root','icdbadmin'); //Test
$server="indiacircusdb.cf24hpnclzb6.ap-southeast-1.rds.amazonaws.com";
$username="root";
$password="icdbadmin";
$database="indiacircus";

//////////////////////////////////////////////
$link = mysql_connect( $server,$username,$password) or die("Unable to connect to the database...");
mysql_set_charset('utf8');
mysql_selectdb($database,$link);
 
 

$stores=array(0,1);


foreach($stores as $store){
	/*
	get special price
	
	SELECT `value` FROM `catalog_product_entity_decimal`
					WHERE `attribute_id` = (SELECT `attribute_id` FROM `eav_attribute` 
												WHERE (`attribute_code`='special_price')) 
					AND `entity_id`  IN (SELECT `entity_id` FROM `catalog_product_entity`
												WHERE (`sku` IN ('13453'))) 
					AND (`store_id`='0');
	*/
	//$sql = " UPDATE `".getStoreFlatTable($store)."` SET `special_price`=NULL, `special_from_date`=NULL, `special_to_date`=NULL WHERE (`sku` IN ('13470')); ";
	
	$sql=" UPDATE `catalog_product_entity_decimal`
				SET `value`=NULL
					WHERE `attribute_id` = (SELECT `attribute_id` FROM `eav_attribute` 
												WHERE (`attribute_code`='special_price')) 
					AND `entity_id`  IN (SELECT `entity_id` FROM `catalog_product_entity`
												WHERE (`sku` IN ('S - 11337 / M - 11335 / L - 11336',
'S - 11340 / M - 11338 / L - 11339',
'S - 11343 / M - 11341 / L - 11342',
'S - 11346 / M - 11344 / L - 11345',
'S - 11349 / M - 11347 / L - 11348',
'S - 11352/ M - 11350/ L - 11351',
'12*12 - 11160 / 16*16 - 11161',
'12*12 - 11129 / 16*16 - 11130',
'12*12 - 11136 / 16*16 - 11137',
'12*12 - 11171 / 16*16 - 11172',
'12*12 - 11185 / 16*16 - 11189',
'12*12 - 11188 / 16*16 - 11186',
'S-13151 / M-11629 / L-11514',
'M-11630/L-11519',
'M-11510/ L-11518',
'16*16 - 11631 / 18*18 - 11632',
'16*16 - 11679 / 18*18 - 11680',
'16*16 - 11819 / 18*18 - 11820',
'16*16 - 11829 / 18*18 - 11830',
'16*16 - 11831 / 18*18 - 11832',
'16*16 - 11833 / 18*18 - 11834',
'16*16 - 11821 / 18*18 - 11822',
'16*16 - 11823 / 18*18 - 11824',
'16*16 - 11825 / 18*18 - 11826',
'16*16 - 11815 / 18*18 - 11816',
'16*16 - 11657 / 18*18 - 11658',
'16*16 - 11835 / 18*18 - 11836',
'16*16 - 11817 / 18*18 - 11818',
'Mn - 12225 / M-12229 / L-12233',
'S-13150 / M-12228 / L-12232',
'M-12227 / L-12231',
'Mn - 12224 / M-12226 / L-12230',
'M - 12483 / L - 12484 / XL - 12485',
'M - 12486 / L - 12487 / XL - 12488',
'M - 12489 / L - 12490 / XL - 12491',
'M - 12495 / L - 12496 / XL - 12497',
'M - 12498 / L - 12499 / XL - 12500',
'M - 12501 / L - 12502 / XL - 12503',
'S - 12521 / M - 12522 / L - 12523',
'S - 12525 / M - 12526 / L - 12527',
'S - 12549 / M - 12550 / L - 12551 / XL - 12552',
'S - 12553 / M - 12554 / L - 12555 / XL - 12556',
'S - 12561 / M - 12562 / L - 12563',
'S - 12564 / M - 12565 / L - 12566',
'S - 12573 / M - 12574 / L - 12575',
'S - 12576 / M - 12577 / L - 12578',
'filler',
'M - 12596 / L - 12597 / XL - 12598',
'M - 12492 / L - 12493 / XL - 12494',
'M - 12504 / L - 12505 / XL - 12506',
'M - 12507 / L - 12508 / XL - 12509',
'M - 12510 / L - 12511 / XL - 12512',
'S - 12513 / M - 12514 / L - 12515 / XL - 12516',
'S - 12517 / M - 12518 / L - 12519 / XL - 12520',
'S - 12529 / M - 12530 / L - 12531 / XL - 12532',
'S - 12533 / M - 12534 / L - 12535 / XL - 12536',
'S - 12537 / M - 12538 / L - 12539 / XL - 12540',
'S - 12541 / M - 12542 / L - 12543 / XL - 12544',
'S - 12545 / M - 12546 / L - 12547 / XL - 12548',
'M - 12579 / L - 12580 / XL - 12581',
'M - 12582 / L - 12583 / XL - 12584',
'M - 12585 / L - 12586 / XL - 12587',
'M - 12588 / L - 12589 / XL - 12590',
'M - 12591 / L - 12592 / XL - 12593',
'S - 12557 / M - 12558 / L - 12559 / XL - 12560',
'18*18 - 11837',
'18*18 - 11838',
'18*18 - 11839',
'M - 12567 / L - 12568 / XL - 12569',
'M - 12570 / L - 12571 / XL - 12572',
'M - 12670 / L - 12671 / XL - 12672',
'M - 12673 / L - 12674 / XL - 12675',
'M - 12676 / L - 12677 / XL - 12678',
'M - 12679/ L - 12680 / XL - 12681',
'S - 13153/ M - 13154/ L - 13155/ XL- 13156',
'S - 13157/ M - 13158/ L - 13159/ XL- 13160',
'S - 13161 / M - 13162 / L - 13163 / XL- 13164',
'S - 13165 / M - 13166 / L - 13167 / XL- 13168',
'S - 13169 / M - 13170 / L - 13171 / XL- 13172',
'S - 13173 / M - 13174 / L - 13175 / XL- 13176',
'S - 13177 / M - 13178 / L - 13179 / XL- 13180',
'S - 13181 / M - 13182 / L - 13183 / XL- 13184',
'S - 13185 / M - 13186 / L - 13187 / XL- 13188',
'M - 13189 / L - 13190 / XL- 13191',
'M - 13192 / L - 13193 / XL- 13194',
'M - 13195 / L - 13196 / XL- 13197',
'M - 13198 / L - 13199 / XL- 13200',
'M - 13201 / L - 13202 / XL- 13203',
'M - 13204 / L - 13205 / XL- 13206',
'M - 13207 / L - 13208 / XL- 13209',
'M - 13210 / L - 13211 / XL- 13212',
'Mn - 12990 / S - 12999 / M - 13003 / L - 13215',
'Mn - 12988 / S - 12997 / M - 13001 / L - 13213',
'Mn - 12989 / S - 12998 / M - 13002 / L - 13214',
'Mn - 12991 / S - 13000 / M - 13004 / L - 13216 /',
'16x16-13050/18x18-13051',
'12x12 - 13109 / 16x16 - 13110 / 18x18 - 13111',
'12x12 - 13091 / 16x16 - 13092 / 18x18 - 13093',
'16x16 - 13045 / 18X18 - 13046',
'16x16 - 13080 / 18x18 - 13081',
'12x12 - 13103 / 16x16 - 13104 / 18x18 - 13105',
'12x12 - 13040 / 16x16 - 13042 / 18x18 - 13043',
'12x12 - 12979 / 16x16 - 12970',
'16x16 - 13082 / 18x18 - 13083',
'12x12 - 13052 / 16x16 - 13053 / 18x18 - 13054',
'12x12 - 13055 / 16x16 - 13057 / 18x18 - 13058',
'12x12 - 13059 / 16x16 - 13061 / 18x18 - 13062',
'16x16 - 13038 / 18x18 - 13039',
'12x12 - 13077 / 16x16 - 13078 / 18x18 - 13079',
'16x16 - 13107 / 18x18 - 13108',
'16x16 - 13067 / 18x18 - 13068',
'12x12 - 12977 / 16x16 - 12968',
'12x12 - 12978 / 16x16 - 12969',
'12x12 - 13073 / 16x16 - 13075 / 18x18 - 13076',
'16x16 - 13032 / 18x18 - 13033',
'12x12 - 13112 / 16x16 - 13113 / 18x18 - 13114',
'16x16 - 13034 / 18x18 - 13035',
'16x16 - 13036 / 18x18 - 13037',
'12x12 - 13084 / 16x16 - 13085 / 18x18 - 13086',
'12x12 - 13069 / 16x16 - 13071 / 18x18 - 13072',
'12x12 - 13063 / 16x16 - 13064 / 18x18 - 13065',
'12x12 - 13047 / 16x16 - 13048 / 18x18 - 13049',
'12x12 - 13115 / 16x16 - 13116 / 18x18 - 13117',
'M - 13508 / L - 13509 / XL- 13510',
'M - 13511 / L - 13512 / XL- 13513',
'M - 13514 / L - 13515 / XL- 13516',
'S - 13517/ M - 13518 / L - 13519 / XL- 13520',
'S - 13521/ M - 13522 / L - 13523 / XL- 13525',
'S - 13524/ M - 13526 / L - 13527 / XL- 13528',
'S - 13529/ M - 13530 / L - 13531 / XL- 13532',
'M - 13533 / L - 13534 / XL- 13535',
'M - 13536 / L - 13537 / XL- 13538',
'S - 13539/ M - 13540 / L - 13541 / XL- 13542',
'18 x 18 - 13606 / 24 x 24 - 13614 / 36 x 36 - 13622',
'18 x 18 - 13607 / 24 x 24 - 13615 / 36 x 36 - 13623',
'18 x 18 - 13608 / 24 x 24 - 13616 / 36 x 36 - 13624',
'18 x 18 - 13609 / 24 x 24 - 13617 / 36 x 36 - 13625',
'18 x 18 - 13610 / 24 x 24 - 13618 / 36 x 36 - 13626',
'18 x 18 - 13611 / 24 x 24 - 13619 / 36 x 36 - 13627',
'18 x 18 - 13612 / 24 x 24 - 13620 / 36 x 36 - 13628',
'18 x 18 - 13613 / 24 x 24 - 13621 / 36 x 36 - 13629',
'13690 - 19 x 27 / 13698 - 24 x 36',
'13683 - 19 x 27 / 13691 - 24 x 36',
'13684 - 19 x 27 / 13692 - 24 x 36',
'13685 - 19 x 27 / 13693 - 24 x 36',
'13686 - 19 x 27 / 13694 - 24 x 36',
'13687 - 19 x 27 / 13695 - 24 x 36',
'13688 - 19 x 27 / 13696 - 24 x 36',
'13689 - 19 x 27 / 13697 - 24 x 36',
'S - 12838 / M - 12839 / L - 12840 / XL - 13135',
'S - 12844 / M - 12845 / L - 12846 / XL - 13137',
'S - 12829 / M - 12830 / L - 12831 / XL - 13132',
'S - 12841 / M - 12842 / L - 12843 / XL - 13136',
'S - 12832 / M - 12833 / L - 12834 / XL - 13133',
'S - 12826 / M - 12827 / L - 12828 / XL - 13131',
'S - 12850 / M - 12851 / L - 12852 / XL - 13139',
'S - 12847 / M - 12848 / L - 12849 / XL - 13138',
'S - 12835 / M - 12836 / L - 12837 / XL - 13134',
'S - 12790 / M - 12791 / L - 12792 / XL - 13140',
'S - 12793 / M - 12794 / L - 12795 / XL - 13141',
'S - 12796 / M - 12797 / L - 12798 / XL - 13142',
'S - 12802 / M - 12803 / L - 12804 / XL - 13144',
'S - 12805 / M - 12806 / L - 12807 / XL - 13145',
'S - 12808 / M - 12809 / L - 12810 / XL - 13146',
'S - 12811 / M - 12812 / L - 12813 / XL - 13147',
'S - 12814 / M - 12815 / L - 12816 / XL - 13148',
'S - 12817 / M - 12818 / L - 12819 / XL - 13149',
'S - 12799 / M - 12800 / L - 12801 / XL - 13143',
'16x16 - 13482 / 18x18 - 13483',
'12x12 - 13484 / 16x16 - 13485 / 18x18 - 13486',
'12x12 - 13487 / 16x16 - 13488 / 18x18 - 13489',
'16x16 - 13490 / 18x18 - 13491',
'S - 13630 / M - 13631 / L - 13632',
'S - 13633 / M - 13634 / L - 13635',
'S - 13636 / M - 13637 / L - 13638',
'S - 13639 / M - 13640 / L - 13641',
'S - 13642 / M - 13643 / L - 13644',
'S - 13648 / M - 13649 / L - 13650',
'S - 13651 / M - 13652 / L - 13653',
'S - 13654 / M - 13655 / L - 13656',
'S - 13657 / M - 13658 / L - 13659',
'S - 13660 / M - 13661 / L - 13662',
'S - 13666 / M - 13667 / L - 13668',
'S - 13723 / M - 13724 / L - 13725',
'S - 13726 / M - 13727 / L - 13728',
'S - 13729 / M - 13730 / L - 13731',
'S - 13732 / M - 13733 / L - 13734',
'3 Ft - 11495 / 4 Ft - 11489',
'3Ft - 11492 / 4Ft - 11762',
'A-5 - 13226 / A-6 - 13242',
'A-5 - 13219 / A-6 - 13235',
'A-5 - 13228 / A-6 - 13244',
'A-5 - 13227 / A-6 - 13243',
'A-5 - 13225 / A-6 - 13241',
'A-5 - 13230 / A-6 - 13246',
'A-5 - 13218 / A-6 - 13234',
'A-5 - 13221 / A-6 - 13237',
'A-5 - 13229 / A-6 - 13245',
'A-5 - 13224 / A-6 - 13240',
'A-5 - 13232 / A-6 - 13248',
'A-5 - 13223 / A-6 - 13239',
'A-5 - 13220 / A-6 - 13236',
'A-5 - 13231 / A-6 - 13247',
'A-5 - 13217 / A-6 - 13233',
'A-5 - 13222 / A-6 - 13238',
'12x12 - 14020 / 16x16 - 14016',
'12x12 - 14019 / 16x16 - 14015',
'12x12 - 14024 / 16x16 - 14026 / 18x18 - 14027',
'12x12 - 14033 / 16x16 - 14034',
'12x12 - 14028 / 16x16 - 14029',
'16x16 - 14035 / 18x18 - 14036',
'12x12 - 14037 / 16x16 - 14039',
'12x12 - 14031 / 18x18 - 14032',
'12x12 - 14040 / 16x16 - 14042 / 18x18 - 14043'
))) 
					AND (`store_id`='$store'); ";
	$query  = mysql_query($sql,$link);
	echo $sql.'<br/>';
	
	
}



function saveCSV($filname,$list){
	$fp = fopen($filname, 'w');
	foreach ($list as $fields) {
		fputcsv($fp, $fields,',',"'");
	}
	fclose($fp);
}


