<?php

/**
 * 
 * PHP version 5.6.x | 7.x | 8.x
 * 
 * @category Addons
 * @package WHMCS
 * @author Pejman Kheyri <pejmankheyri@gmail.com>
 * @copyright 2021 All rights reserved.
 */

use Illuminate\Database\Capsule\Manager as Capsule;

add_hook('AfterCronJob', 1, function($vars) {
	
    date_default_timezone_set('Asia/Tehran');
    $today = intval(date("d"));
    
    $hasTable = Capsule::schema()->hasTable('mod_rewrite_management'); 
    
    if($hasTable){
    	$productgroups = Capsule::table('mod_rewrite_management')
    		->select('mod_rewrite_management.product_group_id','mod_rewrite_management.day_number')
    		->get();
    
    	$groupid = $productgroups[0]->product_group_id;
    	$day_number = $productgroups[0]->day_number;	
    	if($today == $day_number){
			if($groupid && ($groupid > 0)){
				$query = Capsule::table('tblproducts')
					->join('tblproductgroups', 'tblproducts.gid', '=', 'tblproductgroups.id')
					->join('tblcustomfields', 'tblcustomfields.relid', '=', 'tblproducts.id')
					->join('tblcustomfieldsvalues', 'tblcustomfieldsvalues.fieldid', '=', 'tblcustomfields.id')
					//->select('tblcustomfieldsvalues.value')
					->where('tblproductgroups.id','=',$groupid)
					->update(['tblcustomfieldsvalues.value' => 0]);    
			} else {
				//Do Nothing
			}
    	} else {
    		//Do Nothing
    	}		
    } else {
    	//Do Nothing
    }
});
	



?>
