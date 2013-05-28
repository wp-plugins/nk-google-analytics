<div class="wrap">
<h2>NK Google Analytics config</h2>

<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>
<?php settings_fields('NKgoogleanalytics'); ?>

<?php 
	$error = "";

	if(!get_option('nkweb_id')){
		$error = "You must to set an Google Analytics ID.";
	}
	if(get_option('nkweb_Display_Advertising') == "true"){
		if(get_option('nkweb_Universal_Analytics')== "true"){
			$error = "Universal Analytics was set to 'No' because Remarketing is Yes.";
			update_option( "nkweb_Universal_Analytics", "false" );	
		}		
	}
	if(get_option('nkweb_Universal_Analytics')== "true"){
		if(get_option('nkweb_Display_Advertising') == "true"){
			$error = "Remarketing was set to 'No' because Universal Analytics is Yes.";
			update_option( "nkweb_Display_Advertising", "false" );	
		}		
		
		if(get_option('nkweb_Domain')=="your-domain.com" || get_option('nkweb_Domain')==""){
			$error="When you use Universal Analytics you must set your domain.";
		
		}else{
			$userSet = get_option('nkweb_Domain');
			$http = "http";
			
			if(substr_count($userSet,"https")>0){
				$http = "https";				
				$newDomain = str_replace("$http://", "", get_option('nkweb_Domain'));
				update_option( "nkweb_Domain", $newDomain );	
				$error="Your domain was set to $newDomain.";
			}
			if(substr_count($userSet,"www.")>0){
				$newDomain = str_replace("www.", "", get_option('nkweb_Domain'));
				update_option( "nkweb_Domain", $newDomain );	
				$error="Your domain was set to $newDomain.";
			}
		}
	}
	if(get_option('nkweb_Use_Custom')== "true" && !get_option('nkweb_Custom_Code')){
		update_option( "nkweb_Use_Custom", "false" );	
		$error="When you use Custom code you must set your script into 'Custom Google Analytics tracking code' field. Use custom Google Analytics tracking code was set to 'No'.";
	}

if($error != ""){

?>
<div id="setting-error-settings_updated" class="updated settings-error"> 
	<p><strong><?php echo $error; ?></strong></p>
</div>

<?php 
	}
?>

<table class="form-table">


<tr valign="top">
<th scope="row">Google Analytics ID:</th>
<td><input type="text" name="nkweb_id" value="<?php echo get_option('nkweb_id'); ?>" /></td>
</tr>

<tr valign="top">
<th scope="row">Enable Remarketing :<br><small>(Only Clasic analytics)</small></th>
<td>
	<input type="radio" name="nkweb_Display_Advertising" value="true" <?php if (get_option('nkweb_Display_Advertising') == "true"){ echo "checked "; } ?>> Yes<br>
	<input type="radio" name="nkweb_Display_Advertising" value="false"<?php if (get_option('nkweb_Display_Advertising') == "false"){ echo "checked "; } ?>>  No <br>	
</td>	
</tr>

<tr valign="top">
<th scope="row">Universal Analytics</th>
<td>
	<input type="radio" name="nkweb_Universal_Analytics" value="true" <?php if (get_option('nkweb_Universal_Analytics') == "true"){ echo "checked "; } ?>> Yes<br>
	<input type="radio" name="nkweb_Universal_Analytics" value="false"<?php if (get_option('nkweb_Universal_Analytics') == "false"){ echo "checked "; } ?>>  No <br>	
</td>	
</tr>

<tr valign="top">
<th scope="row">Domain :<br><small>(Only Universal analytics)</small></th>
<td><input type="text" name="nkweb_Domain" value="<?php echo get_option('nkweb_Domain'); ?>" /></td>
</tr>

<tr valign="top">
<th scope="row">Use custom Google Analytics tracking code</th>
<td>
	<input type="radio" name="nkweb_Use_Custom" value="true" <?php if (get_option('nkweb_Use_Custom') == "true"){ echo "checked "; } ?>> Yes<br>
	<input type="radio" name="nkweb_Use_Custom" value="false"<?php if (get_option('nkweb_Use_Custom') == "false"){ echo "checked "; } ?>>  No <br>	
</td>	
</tr>

<tr valign="top">
<th scope="row">Custom Google Analytics tracking code</small></th>
<td><textarea name="nkweb_Custom_Code" ><?php echo get_option('nkweb_Custom_Code'); ?></textarea>
</tr>
</table>

<input type="hidden" name="action" value="update" />

<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>

</form>
</div>
