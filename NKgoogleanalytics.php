<?php
/*
Plugin Name: NK Google Analytics
Plugin URI: http://www.marodok.com/nk-google-analytics/
Description: Add <a href="http://www.google.com/analytics/">Google Analytics</a> javascript code on all pages.
Version: 1.4.6
Author: Manfred Rodríguez
Author URI: http://www.marodok.com
Text Domain: NKgoogleanalytics
*/

defined('ABSPATH') or die("No script kiddies please!");

if (!defined('WP_CONTENT_URL'))
    define('WP_CONTENT_URL', get_option('siteurl').'/wp-content');
if (!defined('WP_CONTENT_DIR'))
    define('WP_CONTENT_DIR', ABSPATH.'wp-content');
if (!defined('WP_PLUGIN_URL'))
    define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins');
if (!defined('WP_PLUGIN_DIR'))
    define('WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins');


/*
    Functions
*/

/**
 * Custom links
 */
function nk_custom_links($links) 
{   
    $donate_link = '<a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=CUC2VE9F3LADU">Donate</a>'; 
    $settings_link = '<a href="options-general.php?page=NKgoogleanalytics">Settings</a>';     
    array_unshift($links, $donate_link); 
    array_unshift($links, $settings_link); 
    return $links; 
}

$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'nk_custom_links' );


/**
 * is a login page?
 */
function nk_is_login_page() 
{
    return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
}


/**
 * CSS and JS
 */
function add_assets()
{
    wp_enqueue_style('style_plugin',plugins_url( 'css/style.css' , __FILE__ ) ); 
    wp_enqueue_script('script_plugin',plugins_url( 'js/script.js' , __FILE__ ) ); 
}
add_action('admin_init','add_assets');


/**
 *  Get evaluation code
 */
function nk_evaluation() 
{
    if (get_option('nkweb_code_in_head')=="true") {
        $location = "wp_head";
    } else {
        $location = "wp_footer";
    }

    if (is_user_logged_in()) {

        $current_user   = wp_get_current_user();
        $user_role      = $current_user->roles[0];


        $if = 'if( ';
        $a  = 1;
        $ignored_roles          = get_option('nkweb_ignore');
        $total_ignored_roles    = count(get_option('nkweb_ignore'));

        if (is_array($ignored_roles) && $total_ignored_roles > 0) {

            foreach ( $ignored_roles as $role => $ignore) {

                $roletmp = explode('_', $role);
                $roleName = $roletmp[2];

                if($ignore == 'true')
                    $if .= '("'.$user_role.'"=="'.$roleName.'")';

                if($a<$total_ignored_roles)
                    $if .= ' || ';

                ++$a;
            }

            $if .= ') return; else add_action('.$location.', "NKgoogleanalytics"); ';
            eval($if);

        } else {
            add_action($location, 'NKgoogleanalytics');
        }
    } else {
        add_action($location, 'NKgoogleanalytics');
    }
}


/**
 * Plugin activation
 */
function activate_NKgoogleanalytics() 
{

    $domain = 'your-domain.com';
    if ($_SERVER['SERVER_NAME']) {
        $domain = $_SERVER['SERVER_NAME'];
    }

    add_option('nkweb_id', 'UA-0000000-0');
    add_option('nkweb_Display_Advertising', 'false');  
    add_option('nkweb_track_login_and_register', 'false');  
    add_option('nkweb_Universal_Analytics', 'true');
    add_option('nkweb_Domain', $domain);
    add_option('nkweb_Use_Custom', 'false');
    add_option('nkweb_Custom_Code', '');
    add_option('nkweb_Enable_GA', 'true');  
    add_option('nkweb_Error', '');
    add_option('nkweb_code_in_head', 'true');
    add_option('nkweb_ignore', '');
 
}


/**
 * Plugin  deactivation 
 */
function deactive_NKgoogleanalytics() 
{
  delete_option('nkweb_id');
  delete_option('nkweb_Display_Advertising');
  delete_option('nkweb_track_login_and_register');
  delete_option('nkweb_Universal_Analytics');
  delete_option('nkweb_Domain');
  delete_option('nkweb_Use_Custom');
  delete_option('nkweb_Custom_Code');
  delete_option('nkweb_Enable_GA');
  delete_option('nkweb_Error');
  delete_option('nkweb_code_in_head');
  delete_option('nkweb_ignore');
}


function admin_init_NKgoogleanalytics() 
{
  register_setting('NKgoogleanalytics', 'nkweb_id');
  register_setting('NKgoogleanalytics', 'nkweb_Display_Advertising');
  register_setting('NKgoogleanalytics', 'nkweb_track_login_and_register');
  register_setting('NKgoogleanalytics', 'nkweb_Universal_Analytics');
  register_setting('NKgoogleanalytics', 'nkweb_Domain');
  register_setting('NKgoogleanalytics', 'nkweb_Use_Custom');
  register_setting('NKgoogleanalytics', 'nkweb_Custom_Code');
  register_setting('NKgoogleanalytics', 'nkweb_Enable_GA');
  register_setting('NKgoogleanalytics', 'nkweb_Error');
  register_setting('NKgoogleanalytics', 'nkweb_code_in_head');
  register_setting('NKgoogleanalytics', 'nkweb_ignore');
}


function admin_menu_NKgoogleanalytics() 
{ 
  add_options_page('NK Google Analytics', 'NK Google Analytics', 'manage_options', 'NKgoogleanalytics', 'options_page_NKgoogleanalytics');   
}


function options_page_NKgoogleanalytics() 
{
  include(WP_PLUGIN_DIR.'/nk-google-analytics/options.php');  
}


function NKgoogleanalytics() 
{
	
    $comment = '<!-- Tracking code easily added by NK Google Analytics -->'."\n";
    $nkweb_id = get_option('nkweb_id');
    $Display_Advertising = get_option('nkweb_Display_Advertising');
    $Universal_Analytics = get_option('nkweb_Universal_Analytics');
    $Domain = get_option('nkweb_Domain');
    $nkweb_Use_Custom = get_option('nkweb_Use_Custom');
    $nkweb_Custom_Code = get_option('nkweb_Custom_Code');
    $nkweb_Enable_GA = get_option('nkweb_Enable_GA');
    $nkweb_Error = get_option('nkweb_Error');  

    $tk = '';
  

    if ($nkweb_Enable_GA != 'false') {

        $tk = $comment;

        if ($nkweb_Use_Custom == 'true') {
          
            $tk .= '<script type="text/javascript">' . $nkweb_Custom_Code . '</script>';
            $tk = str_replace('<script><script>', '<script>', $tk);
            $tk = str_replace('<script type="text/javascript"><script>', '<script>', $tk);
            $tk = str_replace('</script></script>', '</script>', $tk);

        } else {
          
            if ($nkweb_id != '' && $nkweb_id != 'UA-0000000-0') {
            
                if ($Universal_Analytics=='false') {

                    $tk .= "<script type=\"text/javascript\">\n";
                    $tk .= " var _gaq = _gaq || [];\n";
                    $tk .= " _gaq.push( ['_setAccount', '".$nkweb_id . "'],['_trackPageview'] );\n";
                    $tk .= "\n";
                    $tk .= " (function() {\n";
                    $tk .= "  var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;\n";

                    if ($Display_Advertising=='false') { 
                        $tk .= " ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';\n";
                    } else {
                        $tk .= " ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';\n";
                    }

                    $tk .= "  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);\n";
                    $tk .= " })();\n";
                    $tk .= "\n";
                    $tk .= " window.onload = function() {\n";
                    $tk .= "  if(_gaq.I==undefined){\n";
                    $tk .= "   _gaq.push(['_trackEvent', 'tracking_script', 'loaded', 'ga.js', ,true]);\n";
                    $tk .= "   ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;\n";
                    
                    if ($Display_Advertising=='false') {
                        $tk .= "   ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';\n";
                    } else {
                        $tk .= "   ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';\n";
                    }

                    $tk .= "   s = document.getElementsByTagName('script')[0];\n";
                    $tk .= "   gaScript = s.parentNode.insertBefore(ga, s);\n";
                    $tk .= "  } else {\n";
                    $tk .= "   _gaq.push(['_trackEvent', 'tracking_script', 'loaded', 'dc.js', ,true]);\n";
                    $tk .= "  }\n";
                    $tk .= " };\n";          

                    $tk .= "</script> \n";

                } else {

                    $tk .= "<script type=\"text/javascript\"> \n";
                    $tk .= "(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){ \n";
                    $tk .= "(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o), \n";
                    $tk .= "m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m) \n";
                    $tk .= "})(window,document,'script','https://www.google-analytics.com/analytics.js','ga'); \n";

                    $tk .= "ga('create', '" . $nkweb_id. "', '" . $Domain . "'); \n";

                    if ($Display_Advertising=="true") {
                        $tk .= "ga('require', 'displayfeatures'); \n";
                    }

                    $tk .= "ga('send', 'pageview'); \n";

                    $tk .= "</script> \n";

                }

            } else {
                update_option( 'nkweb_Error', 'There is a problem with your Google Analytics ID' );
            }
        }
        echo $tk;
    }
}


/*
    Start process
*/
register_activation_hook(__FILE__, 'activate_NKgoogleanalytics');
register_deactivation_hook(__FILE__, 'deactive_NKgoogleanalytics');

if (is_admin()) {
  add_action('admin_init', 'admin_init_NKgoogleanalytics');
  add_action('admin_menu', 'admin_menu_NKgoogleanalytics');
}


if (!is_admin()) { 
    add_action('init', 'nk_evaluation',10);
}  

if(get_option('nkweb_track_login_and_register')=="true"){
  add_action( 'login_head', 'NKgoogleanalytics');
}
if(nk_is_login_page() && get_option('nkweb_track_login_and_register')=="true"){
  add_action( 'login_head', 'NKgoogleanalytics'); 
}