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

function rewrite_management_config() {
	$configarray = array(
		'name' => 'Rewrite Management', 
		'version' => '1.0.0', 
		'author' => 'pejman kheyri', 
		'description' => 'Monthly Rewrite Management Values', 
		'language' => 'english',  		
	); 
	return $configarray; 
} 

function rewrite_management_activate() {
	
	try {
		Capsule::schema()->create(
			'mod_rewrite_management',
			function ($table) {

				$table->increments('id');
				$table->integer('product_group_id');
				$table->integer('day_number');
				$table->timestamps();
			}
		);
	} catch (\Exception $e) {
		echo "Unable to create mod_rewrite_management: {$e->getMessage()}";
	}
} 

function rewrite_management_deactivate() {

	$query = "DROP TABLE `mod_rewrite_management`";
	$result = full_query($query);
} 

function rewrite_management_output($vars) {

	$modulelink = $vars['modulelink']; 

	$groupId = $_POST['groupId'];
	$dayno = $_POST['dayno'];
	$mess = $_GET['mess'];

	date_default_timezone_set('Asia/Tehran');
	$now = date("Y-m-d h:i:s");
	
	if($mess == "InsertDone"){
		echo rewrite_management_insert_success_mess();
	}
	if($mess == "UpdateDone"){
		echo rewrite_management_update_success_mess();
	}

	$management = Capsule::table('mod_rewrite_management')
		->select('mod_rewrite_management.product_group_id','mod_rewrite_management.day_number')
		->get();		
	$TBLgroupId = $management[0]->product_group_id;
	$TBLday = $management[0]->day_number;

	if(!empty($management)){
		if($groupId){
			$updateGroupid = Capsule::table('mod_rewrite_management')
				->where('id', 1)
				->update(['product_group_id' => $groupId,'day_number' => $dayno,'updated_at' => $now]);

			if($updateGroupid >= 0){
				header("Location: $modulelink&mess=UpdateDone");
				exit;
			} else {
				echo rewrite_management_update_failed_mess();
			}
		} else {
			$groupId = $TBLgroupId;
		}
	} else {
		$insertGroupid = Capsule::table('mod_rewrite_management')->insert([
			['product_group_id' => 0,'day_number' => 1,'created_at' => $now]
		]);

		if($insertGroupid){
			header("Location: $modulelink&mess=InsertDone");
			exit;
		} else {
			echo rewrite_management_insert_failed_mess();
		}
	}

	$productgroups = Capsule::table('tblproductgroups')
		->select('tblproductgroups.name','tblproductgroups.id')
		->get();

	echo "Choose Group that you want to rewrite values monthly :";
	echo "<br><br>";
	
	echo "<form method='post' action='$modulelink'>";
	echo "<label>Group : </label> ";
	echo "<select name='groupId'><option></option>";
		
	foreach($productgroups as $gkey => $gval){
		if($TBLgroupId == ($gval->id)){
			$selected = "selected";
		} else {
			$selected = "";
		}
		echo "<option value='".$gval->id."' ".$selected.">".$gval->name."</option>";
	}
	echo "</select><br>";
	echo "<label>Day of month : </label> ";
	echo "<select name='dayno'>";
	for($i = 1; $i <= 31; $i++){
		if($TBLday == $i){
			$selectedday = "selected";
		} else {
			$selectedday = "";
		}
		echo "<option value='".$i."' ".$selectedday.">".$i."</option>";
	}
	
	echo "</select><br>";
	
	echo "<input type='submit' value='submit' />";
	echo "</form>";
		
} 

function rewrite_management_insert_success_mess(){
	echo '<div class="successbox">
		<strong>
		<span class="title">Success</span>
		</strong><br>Sample row inserted successfully for setting addon module.</div>';
}

function rewrite_management_update_success_mess(){
	echo '<div class="successbox">
		<strong>
		<span class="title">Success</span>
		</strong><br>Updating setting record successfully done for addon module.</div>';
}

function rewrite_management_update_failed_mess(){
	echo '<div class="errorbox">
		<strong>
		<span class="title">Error</span>
		</strong><br>Error updating setting</div>';
}

function rewrite_management_insert_failed_mess(){
	echo '<div class="errorbox">
		<strong>
		<span class="title">Error</span>
		</strong><br>Error inserting setting</div>';
}

function rewrite_management_poll_delete_confirmation_js(){
	$output = '<script type="text/javascript">
				function confirmation() {
					if (!confirm("Are you sure")) {
						return false;
					}
				}	
		</script>';
	return $output;
}

?>