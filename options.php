 <div class="wrap">
<h2>Google Analytics config</h2>

<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>
<?php settings_fields('NKgoogleanalytics'); ?>

<table class="form-table">


<tr valign="top">
<th scope="row">Google Analytics ID:</th>
<td><input type="text" name="nkweb_id" value="<?php echo get_option('nkweb_id'); ?>" /></td>
</tr>

<tr valign="top">
<th scope="row">Enable Remarketing (Only Clasic analytics):</th>
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
<th scope="row">Domain (Only Universal analytics):</th>
<td><input type="text" name="nkweb_Domain" value="<?php echo get_option('nkweb_Domain'); ?>" /></td>
</tr>


</table>

<input type="hidden" name="action" value="update" />

<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>

</form>
</div>
