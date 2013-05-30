<?php
/*
Plugin Name: NK Google Analytics
Plugin URI: http://www.marodok.com/nk-google-analytics/
Description: Add <a href="http://www.google.com/analytics/">Google Analytics</a> javascript code on all pages.
Version: 1.2.4
Author: Manfred Rodríguez
Author URI: http://www.marodok.com
*/

if (!defined('WP_CONTENT_URL'))
      define('WP_CONTENT_URL', get_option('siteurl').'/wp-content');
if (!defined('WP_CONTENT_DIR'))
      define('WP_CONTENT_DIR', ABSPATH.'wp-content');
if (!defined('WP_PLUGIN_URL'))
      define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins');
if (!defined('WP_PLUGIN_DIR'))
      define('WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins');

function activate_NKgoogleanalytics() {
  add_option('nkweb_id', 'UA-0000000-0');
  add_option('nkweb_Display_Advertising', 'false');  
  add_option('nkweb_Universal_Analytics', 'false');
  add_option('nkweb_Domain', 'your-domain.com');
  add_option('nkweb_Use_Custom', 'false');
  add_option('nkweb_Custom_Code', '');

  //Just for statistics
  try {
    $xml = file_get_contents("http://www.marodok.com/url.php?url=".site_url());  

  } catch (Exception $e) {
    // nothing :-)
  }
}

function deactive_NKgoogleanalytics() {
  delete_option('nkweb_id');
  delete_option('nkweb_Display_Advertising');
  delete_option('nkweb_Universal_Analytics');
  delete_option('nkweb_Domain');
  delete_option('nkweb_Use_Custom');
  delete_option('nkweb_Custom_Code');
}

function admin_init_NKgoogleanalytics() {
  register_setting('NKgoogleanalytics', 'nkweb_id');
  register_setting('NKgoogleanalytics', 'nkweb_Display_Advertising');
  register_setting('NKgoogleanalytics', 'nkweb_Universal_Analytics');
  register_setting('NKgoogleanalytics', 'nkweb_Domain');
  register_setting('NKgoogleanalytics', 'nkweb_Use_Custom');
  register_setting('NKgoogleanalytics', 'nkweb_Custom_Code');
}

function admin_menu_NKgoogleanalytics() {
  add_options_page('NK Google Analytics', 'NK Google Analytics', 'manage_options', 'NKgoogleanalytics', 'options_page_NKgoogleanalytics');
}

function options_page_NKgoogleanalytics() {
  include(WP_PLUGIN_DIR.'/nk-google-analytics/options.php');  
}

function NKgoogleanalytics() {
	
  $comment = "<!-- Tracking code easily added by NK Google Analytics -->\n";
  $nkweb_id = get_option('nkweb_id');
  $Display_Advertising = get_option('nkweb_Display_Advertising');
  $Universal_Analytics = get_option('nkweb_Universal_Analytics');
  $Domain = get_option('nkweb_Domain');
  $nkweb_Use_Custom = get_option('nkweb_Use_Custom');
  $nkweb_Custom_Code = get_option('nkweb_Custom_Code');
  
  if($nkweb_Use_Custom == "true"){
    echo $comment;
    echo "<script>" . $nkweb_Custom_Code . "</script>";

  }else{
    if($nkweb_id != "" && $nkweb_id != "UA-0000000-0"){

      if($Universal_Analytics=="false"){
        echo $comment;
?>
<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', '<?php echo $nkweb_id ?>']);
_gaq.push(['_trackPageview']);
(function() {
var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
<?php if($Display_Advertising=="false"){ ?>
ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
<?php }else{ ?>
ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';	
<?php }?>
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>
<?php
        }else{
          echo $comment;
?>
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
ga('create', '<?php echo $nkweb_id; ?>', '<?php echo $Domain; ?>');
ga('send', 'pageview');
</script>
<?php
        }
      }
    }
  }

register_activation_hook(__FILE__, 'activate_NKgoogleanalytics');
register_deactivation_hook(__FILE__, 'deactive_NKgoogleanalytics');

if (is_admin()) {
  add_action('admin_init', 'admin_init_NKgoogleanalytics');
  add_action('admin_menu', 'admin_menu_NKgoogleanalytics');
}

if (!is_admin()) {
  add_action('wp_head', 'NKgoogleanalytics');
}

?>
