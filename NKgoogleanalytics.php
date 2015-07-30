<?php
/*
Plugin Name: NK Google Analytics
Plugin URI: http://www.marodok.com/nk-google-analytics/
Description: Add <a href="http://www.google.com/analytics/">Google Analytics</a> javascript code on all pages.
Version: 1.4.12
Author: Manfred Rodríguez
Author URI: http://www.marodok.com
Text Domain: NKgoogleanalytics
Domain Path: /languages/
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


function add_fingerprintjs()
{
    wp_enqueue_script('script_fingerprintjs',plugins_url( 'js/fingerprint.min.js' , __FILE__ ) );
}

if (get_option('nkweb_fingerprintjs')=="true" && get_option('nkweb_Universal_Analytics')=="true") {
    add_action('init','add_fingerprintjs');
}

/**
 *  Get page or post id
 */
function nk_get_post_id() {
    $post_id = "false";

    if ( is_page() ) {
	$post_id = get_queried_object_id();
    } else if ( is_single() ) {
	$post_id = get_the_ID();
    }

    return $post_id;
}

/**
 *  Get evaluation code
 */
function nk_evaluation()
{
    $post_id = nk_get_post_id();

    if ($post_id != "false") {
        $nkweb_code_in_head = get_post_meta( $post_id, 'nkweb_code_in_head', true );
        if ( ($nkweb_code_in_head != "false") && ($nkweb_code_in_head != "true") ) {
            $nkweb_code_in_head = get_option('nkweb_code_in_head');
        }
    }else{
        $nkweb_code_in_head = get_option('nkweb_code_in_head');
    }

    if ($nkweb_code_in_head == "true" ) {
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
    if ( ! current_user_can( 'activate_plugins' ) )
	return;

    $domain = 'your-domain.com';

    if ($_SERVER['SERVER_NAME']) {
        $domain = $_SERVER['SERVER_NAME'];
    }

    add_option('nkweb_id', 'UA-0000000-0');
    add_option('nkweb_Display_Advertising', 'false');
    add_option('nkweb_track_login_and_register', 'false');
    add_option('nkweb_Universal_Analytics', 'true');
    add_option('nkweb_Domain', $domain);
    add_option('nkweb_Domain_Auto', 'true');
    add_option('nkweb_fingerprintjs', 'false');
    add_option('nkweb_anonymizeip', 'false');
    add_option('nkweb_Use_Custom', 'false');
    add_option('nkweb_Custom_Code', '');
    add_option('nkweb_Enable_GA', 'true');
    add_option('nkweb_Error', '');
    add_option('nkweb_code_in_head', 'true');
    add_option('nkweb_ignore', '');
    add_option('nkweb_Custom_js', '');
    add_option('nkweb_Custom_Values', '');

}


/**
 * Plugin  deactivation
 */
function uninstall_NKgoogleanalytics()
{
  if ( ! current_user_can( 'activate_plugins' ) )
	          return;

  delete_option('nkweb_id');
  delete_option('nkweb_Display_Advertising');
  delete_option('nkweb_track_login_and_register');
  delete_option('nkweb_Universal_Analytics');
  delete_option('nkweb_Domain');
  delete_option('nkweb_Domain_Auto');
  delete_option('nkweb_fingerprintjs');
  delete_option('nkweb_anonymizeip');
  delete_option('nkweb_Use_Custom');
  delete_option('nkweb_Custom_Code');
  delete_option('nkweb_Enable_GA');
  delete_option('nkweb_Error');
  delete_option('nkweb_code_in_head');
  delete_option('nkweb_ignore');
  delete_option('nkweb_Custom_js');
  delete_option('nkweb_Custom_Values');

  $allposts = get_posts( 'numberposts=-1&post_type=post&post_status=any' );
  $allpages = get_posts( 'numberposts=-1&post_type=page&post_status=any' );

  foreach( $allposts as $postinfo ) {
	  delete_post_meta( $postinfo->ID, 'nkweb_code_in_head');
	  delete_post_meta( $postinfo->ID, 'nkweb_Use_Custom_js');
	  delete_post_meta( $postinfo->ID, 'nkweb_Custom_js');
	  delete_post_meta( $postinfo->ID, 'nkweb_Use_Custom_Values');
	  delete_post_meta( $postinfo->ID, 'nkweb_Custom_Values');
	  delete_post_meta( $postinfo->ID, 'nkweb_Use_Custom');
	  delete_post_meta( $postinfo->ID, 'nkweb_Custom_Code');
  }

  foreach( $allpages as $postinfo ) {
	  delete_post_meta( $postinfo->ID, 'nkweb_code_in_head');
	  delete_post_meta( $postinfo->ID, 'nkweb_Use_Custom_js');
	  delete_post_meta( $postinfo->ID, 'nkweb_Custom_js');
	  delete_post_meta( $postinfo->ID, 'nkweb_Use_Custom_Values');
	  delete_post_meta( $postinfo->ID, 'nkweb_Custom_Values');
	  delete_post_meta( $postinfo->ID, 'nkweb_Use_Custom');
	  delete_post_meta( $postinfo->ID, 'nkweb_Custom_Code');
  }
}

/**
 * Options page
 */

function admin_init_NKgoogleanalytics()
{
  register_setting('NKgoogleanalytics', 'nkweb_id');
  register_setting('NKgoogleanalytics', 'nkweb_Display_Advertising');
  register_setting('NKgoogleanalytics', 'nkweb_track_login_and_register');
  register_setting('NKgoogleanalytics', 'nkweb_Universal_Analytics');
  register_setting('NKgoogleanalytics', 'nkweb_Domain');
  register_setting('NKgoogleanalytics', 'nkweb_Domain_Auto');
  register_setting('NKgoogleanalytics', 'nkweb_fingerprintjs');
  register_setting('NKgoogleanalytics', 'nkweb_anonymizeip');
  register_setting('NKgoogleanalytics', 'nkweb_Use_Custom');
  register_setting('NKgoogleanalytics', 'nkweb_Custom_Code');
  register_setting('NKgoogleanalytics', 'nkweb_Enable_GA');
  register_setting('NKgoogleanalytics', 'nkweb_Error');
  register_setting('NKgoogleanalytics', 'nkweb_code_in_head');
  register_setting('NKgoogleanalytics', 'nkweb_ignore');
  register_setting('NKgoogleanalytics', 'nkweb_Custom_js');
  register_setting('NKgoogleanalytics', 'nkweb_Custom_Values');
}

function admin_menu_NKgoogleanalytics()
{
  add_options_page('NK Google Analytics', 'NK Google Analytics', 'manage_options', 'NKgoogleanalytics', 'options_page_NKgoogleanalytics');
}


function options_page_NKgoogleanalytics()
{
  include(WP_PLUGIN_DIR.'/nk-google-analytics/admin/options.php');
}

/**
 * Metabox for each post and page
 */

function add_metabox_NKgoogleanalytics()
{
	$screens = array( 'post', 'page');

	foreach ( $screens as $screen) {
		add_meta_box(
			'NKgoogleanalytics',
			__( 'NK Google Analytics Settings', 'NKgoogleanalytics'),
			'render_metabox_NKgoogleanalytics',
			$screen
		);

	}
}

function render_metabox_NKgoogleanalytics( $post )
{
  include(WP_PLUGIN_DIR.'/nk-google-analytics/admin/metabox.php');
}

function save_metabox_NKgoogleanalytics( $post_id )
{
  include(WP_PLUGIN_DIR.'/nk-google-analytics/admin/save_metabox.php');
}

function load_metabox_NKgoogleanalytics()
{
	add_action( 'add_meta_boxes', 'add_metabox_NKgoogleanalytics');
	add_action( 'save_post', 'save_metabox_NKgoogleanalytics');
}

/**
 * Outputs the javascript code for google analytics
 */

function NKgoogleanalytics()
{

    $comment = '<!-- Tracking code easily added by NK Google Analytics -->'."\n";
    $nkweb_id = get_option('nkweb_id');
    $Display_Advertising = get_option('nkweb_Display_Advertising');
    $Universal_Analytics = get_option('nkweb_Universal_Analytics');
    $Domain = get_option('nkweb_Domain');
    $nkweb_Domain_Auto = get_option('nkweb_Domain_Auto');
    $nkweb_fingerprintjs = get_option('nkweb_fingerprintjs');
    $nkweb_anonymizeip = get_option('nkweb_anonymizeip');
    $nkweb_Use_Custom = get_option('nkweb_Use_Custom');
    $nkweb_Custom_Code = get_option('nkweb_Custom_Code');
    $nkweb_Enable_GA = get_option('nkweb_Enable_GA');
    $nkweb_Error = get_option('nkweb_Error');
    $nkweb_Custom_js = get_option('nkweb_Custom_js');
    $nkweb_Custom_Values = get_option('nkweb_Custom_Values');
    $post_id = nk_get_post_id();

    if ( $post_id != "false" ) {
	$nkweb_Use_Custom_post = get_post_meta( $post_id, 'nkweb_Use_Custom', true );
	$nkweb_Custom_Code_post = get_post_meta( $post_id, 'nkweb_Custom_Code', true );
	$nkweb_Use_Custom_js_post = get_post_meta( $post_id, 'nkweb_Use_Custom_js', true );
	$nkweb_Custom_js_post = get_post_meta( $post_id, 'nkweb_Custom_js', true );
	$nkweb_Use_Custom_Values_post = get_post_meta( $post_id, 'nkweb_Use_Custom_Values', true );
	$nkweb_Custom_Values_post = get_post_meta( $post_id, 'nkweb_Custom_Values', true );

	if ( (  $nkweb_Use_Custom_post == "true" ) ) {
		$nkweb_Use_Custom = "$nkweb_Use_Custom_post";
		$nkweb_Custom_Code = "$nkweb_Custom_Code_post";
	}

	if ( $nkweb_Use_Custom_js_post == "true" ) {
		$nkweb_Custom_js = "$nkweb_Custom_js_post";
	} else if ( $nkweb_Use_Custom_js_post == "false" ) {
		$nkweb_Custom_js = "";
	}

	if ( $nkweb_Use_Custom_Values_post == "true" ) {
		$nkweb_Custom_Values = "$nkweb_Custom_Values_post";
	} else if ( $nkweb_Use_Custom_Values_post == "false" ) {
		$nkweb_Custom_Values = "";
	}

    }

    $tk = '';


    if ($nkweb_Enable_GA != 'false') {

        $tk = $comment;

        if ($nkweb_Use_Custom == 'true') {

            $tk .= '<script type="text/javascript">' . $nkweb_Custom_Code . "</script> \n";
            $tk = str_replace('<script><script>', '<script>', $tk);
            $tk = str_replace('<script type="text/javascript"><script>', '<script>', $tk);
            $tk = str_replace('</script></script>', "</script> \n", $tk);

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

                    $tk .= "ga('create', '" . $nkweb_id. "'";

                    if ($nkweb_fingerprintjs=="true") {
                        $tk .= ", { 'storage': 'none', 'clientId': new Fingerprint().get()}";
                    } else if ($nkweb_Domain_Auto=="true") {
                        $tk .= ", 'auto'";
                    } else {
                        $tk .= ", '" . $Domain . "'";
                    }

                    $tk .= "); \n";

                    if ($nkweb_anonymizeip=="true") {
                       $tk .= "ga('set', 'anonymizeIp', true); \n";
                    }

                    if ($Display_Advertising=="true") {
                        $tk .= "ga('require', 'displayfeatures'); \n";
                    }

		    if ( ! empty($nkweb_Custom_js)) {
			$tk .= "$nkweb_Custom_js\n";
		    }

		    if ( ! empty($nkweb_Custom_Values)) {
			$tk .= "ga('set', {" . $nkweb_Custom_Values . "}); \n";
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
    Load textdomain
*/
/* Not working for now
function nk_load_textdomain()
{
    global $l10n;

    $domain = 'NKgoogleanalytics';

    if ( get_locale() == $locale ) {
        $locale = null;
    }

    if ( empty( $locale ) ) {
        if ( is_textdomain_loaded( $domain ) ) {
            return true;
        } else {
            return load_plugin_textdomain( $domain, false, $domain . '/languages' );
        }
    } else {
        $mo_orig = $l10n[$domain];
        unload_textdomain( $domain );

        $mofile = $domain . '-' . $locale . '.mo';
        $path = WP_PLUGIN_DIR . '/' . $domain . '/languages';

        if ( $loaded = load_textdomain( $domain, $path . '/'. $mofile ) ) {
            return $loaded;
        } else {
            $mofile = WP_LANG_DIR . '/plugins/' . $mofile;
            return load_textdomain( $domain, $mofile );
        }

        $l10n[$domain] = $mo_orig;
    }

    return false;
    //load_plugin_textdomain( 'NKgoogleanalytics', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
}
*/


/*
    Start process
*/
register_activation_hook(__FILE__, 'activate_NKgoogleanalytics');
register_uninstall_hook(__FILE__, 'uninstall_NKgoogleanalytics');


if (is_admin()) {
    //add_action('plugins_loaded', 'nk_load_textdomain');
    add_action('admin_init', 'admin_init_NKgoogleanalytics');
    add_action('admin_menu', 'admin_menu_NKgoogleanalytics');
    add_action('load-post.php', 'load_metabox_NKgoogleanalytics');
    add_action('load-post-new.php', 'load_metabox_NKgoogleanalytics');
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