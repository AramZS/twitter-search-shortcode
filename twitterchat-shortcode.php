<?php

/*
Plugin Name: Twitter Archival Shortcode
Plugin URI: http://aramzs.me/twitterarchival
Description: This plugin allows you to place a shortcode in your post that will archive a Twitter search term, including hashtags. 
Version: 0.85
Author: Aram Zucker-Scharff
Author URI: http://aramzs.me
License: GPL2
*/

/*  Copyright 2012  Aram Zucker-Scharff  (email : azuckers@gmu.edu)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* 

	Author's note: This plugin may or may not violate Twitter's Terms of Service. You use the plugin at your own risk. The author is not liable for how you use or deploy this plugin.
	
	Enjoy.

*/

//Based on the original script from Daniel Thorogood
// who can be found at http://twitter.com/SLODeveloper
//Received through the kind folks who run #wjchat 
//Styling comes from Kim Bui at http://twitter.com/kimbui

include 'ta-options.php';

$ta_option_array = get_option('ta_options');
$ta_datetime_option = $ta_option_array['datetime'];
				
//Doc: http://php.net/manual/en/function.date-default-timezone-set.php

if (!empty($ta_datetime_option)){
	date_default_timezone_set($ta_datetime_option);
} 

//Establish date variable.
$now=getdate();
//$date="2010-02-11T05:59:21Z";
$date=$now["year"];
//$date="2010";

//Establish page variable.
$page=1;

//You may want to limit it by a day, month, or year. In which case we will need this:
$keepGoing = true;

$threefour = $ta_option_array['threefour'];

function twitter_search_archive( $atts ) {



	$post_id = get_the_ID();

	add_post_meta($post_id, 'twitter_search_archive', '', true);

	$checkcache=get_post_meta($post_id, 'twitter_search_archive', true);
	
		
		//Create object to contain the archive.
		$archive = '<div class="twitter-archival-container ta">';
		
		extract( shortcode_atts( array(
			'for' => 'Chronotope',
			'within' => '',
			'order' => 'reverse',
			'title' => 'Twitter Archive for',
			'blackbird' => 'no'
		), $atts ) );
		
		//Add post meta to track and check the options that the user is feeding in. 
		add_post_meta($post_id, 'twitter_archive_control', '', true);
		$controlcheck = $for . "," . $within . "," . $order . "," . $title . "," . $blackbird;
		$checkcontrol = get_post_meta($post_id, 'twitter_archive_control', true);
	
	//Check if the options in the shortcode are the same as they were previously. If not let us refresh the archive.
	if (!($controlcheck == $checkcontrol)) {
	
		
		$minushtml = strip_tags($for);
		$safefor = urlencode($minushtml);
		
		$twitterquery = get_twitter_query($safefor);
		
		$queryresults = execute_twitter_query($twitterquery, $within, $order, $keepGoing, $blackbird);
		
		//If the user does not indicate 'none' for no title, use the default or title entered. 
		if ( !($title=="none") ){
			$archive .= '<h3 class="twitter-archival-title ta">' . $title . ' <a href="https://twitter.com/search/' . $safefor . '" target="_blank">' . $for . '</a></h3>';
		} 
		
		$archive .= $queryresults;
		
		$archive .= "</div><!--End of Twitter Archive-->";
		
		$archive = bbg_pb_twitterize($archive);
		
		update_post_meta($post_id, 'twitter_search_archive', $archive);
		update_post_meta($post_id, 'twitter_archive_control', $controlcheck);
	
	}
	else {
	
		$archive = get_post_meta($post_id, 'twitter_search_archive', true);
	
	}
	return $archive;

}

function get_twitter_query($query) {

	//If you want to use a hashtag, we need a urlencode. 
	$safequery=urlencode($query);
	$file="http://search.twitter.com/search.atom?q=$safequery&rpp=100";
	return $file;

}

function execute_twitter_query($file, $datelimit, $ordered, $keepGoing, $blackbird) {

$execute_archive = '';

	//If you don't want it to be reversed, we need to query the pages in proper order. 
	if ($ordered == 'reverse') {
		for($page=15;$page>=1;$page--)
			{
				$thefile="$file&page=$page";
				if (fopen ($file, "r")) 
					{
						$xml = simplexml_load_file($thefile);

						if (!(empty($xml->entry))){
							
						$execute_archive .= output_data($xml,$datelimit,$ordered,$keepGoing,$blackbird);
						
						}
						
					}
					else 
					{
						$execute_archive .= " Sorry, Twitter doesn't seem to be working right now. Try again later.";
					}
			}
	} else {
		for($page=1;$page<=15;$page++)
			{
				$thefile="$file&page=$page";
				if (fopen ($file, "r")) 
					{
						$xml = simplexml_load_file($thefile);

						if (!(empty($xml->entry))){
							
						$execute_archive .= output_data($xml,$datelimit,$ordered,$keepGoing,$blackbird);
						
						}
						
					}
					else 
					{
						$execute_archive .= " Sorry, Twitter doesn't seem to be working right now. Try again later.";
					}
			}
	}
	
		
		return $execute_archive;

}

function output_data($xml,$datelimit,$ordered,$keepGoing,$blackbird)
{

	$output_archive = '';
	//Oddly, that XML we took in is not yet an array. So let's turn it into one.
	foreach($xml->entry as $entry)
	{
		$a[]=$entry;
	}
	if (is_array($a)) {
		
		//I don't know, perhaps you don't want it in reverse chronological order. Let's give the option. 
		if ($ordered == 'reverse') {
		
			$orderedxml=array_reverse($a);
			
		} 
		//The only other way I can think of is chronological, so there's no need for an else statement here. Put anything else in that field and you'll get it in chronological order. What, you got a better idea? Well, tell me about it!
		else {
		
			$orderedxml = $a;
		
		}
		
		//I needed to use this while testing, because I clearly didn't get enough sleep the day I wrote this. 
		//print_r($orderedxml);

		if (($blackbird == 'yes') || ($blackbird == 'Yes'))
		{
			$blackbirdclass = new Blackbirdpie();
			
			foreach($orderedxml as $entry)
			{
				$link=$entry->link[0]['href'];

				
				//If you've designated a date to retrieve from, either a year, month or day, you can restrict Tweets that appear only to that period. 
				if (!empty($datelimit)) {
					//The given date string must be in the 2012-4-24 format
					//If it matches the published time of the tweet, save it to the object. 
					//Otherwise, don't.
					if(strstr($entry->published,$datelimit))
					{
						$output_archive .= $blackbirdclass->render_tweet(false, $link, false, false);
						$keepGoing = true;
					}
					else { $keepGoing = false; }
				} //End of datecheck. 
				else {
				
						$output_archive .= $blackbirdclass->render_tweet(false, $link, false, false);
				
				}
				
				$errorcheck = "<!-- end of tweet -->\"There was a problem connecting to Twitter";

				if ( strstr($output_archive, $errorcheck)) 
				{
				
					break;
				
				}
			
			} //end foreach		
		
		}
		//If Twitter Blackbird Pie is not activated. 
		else 
		{
			foreach($orderedxml as $entry)
			{
				$uri=$entry->author->uri;
				$name=$entry->author->name;
				$image=$entry->link[1]['href'];
				//Moving the date conversion up top. 
				$timestamp = $entry->published;
				$link=$entry->link[0]['href'];
				$unixtime = strtotime($timestamp);
				$datetime = date('h:i:s A, n-j-y', $unixtime);
				
						//Ok, so here's where it gets complex... If you are running WP3.4, there is now an awesome oEmbed function that rendders tweets for us. 
						//See http://codex.wordpress.org/Embeds and http://core.trac.wordpress.org/browser/tags/3.4/wp-includes/media.php#L0 for more info.
						//But if you are running a WP version before that, no-go. 
						//So let's use this new function, but create a fallback. 
						//Remember we got the WordPress version before?
						//Well, to provide forward compatability, we need to convert it to a number.
						//Using this PHP function allows us to turn the string into a decimal number PHP understands. 
						//Let's get the current WordPress version. 
						$wpver = get_bloginfo('version');
						//Should get a string like '3.4' - for more info see http://core.trac.wordpress.org/browser/tags/3.4/wp-includes/version.php#L0						
						$floatWPVer = floatval($wpver);
				
					//If you've designated a date to retrieve from, either a year, month or day, you can restrict Tweets that appear only to that period. 
					if (!empty($datelimit)) {
						//The given date string must be in the 2012-4-24 format
						//If it matches the published time of the tweet, save it to the object. 
						//Otherwise, don't.
						if(strstr($entry->published,$datelimit))
						{
						
							//Now, we check if the version of WordPress we are running is equal to or greater than 3.4.
							if (($floatWPVer >= 3.4) && ($threefour == "yes")){

								
								$outputlink = (string) $entry->link[0]['href'];
								$output_archive .= wp_oembed_get($outputlink);

							} else {							
								$output_archive .= "<div class=\"ta-twitter_user ta\">
								
								<ul class=\"ta-ul\">
								<li class=\"ta-image ta\"><img class=\"ta-avatar ta\" src=\"$image\"></li>
								<li class=\"ta-published ta\"><a href=\"$link\">$datetime</a></li>
								<li class=\"ta-user ta\"><a href=\"$uri\" target=\"_blank\">$name</a></li>
								<li class=\"ta-description ta\">$entry->title</li>
								
								</ul>
								</div>";
								$keepGoing = true;
							}
						}
						else { $keepGoing = false; }
					} //End of datecheck. 
					else {
						//Now, we check if the version of WordPress we are running is equal to or greater than 3.4.
						if (($floatWPVer >= 3.4) && ($threefour == "yes")){

							
							$outputlink = (string) $entry->link[0]['href'];
							$output_archive .= wp_oembed_get($outputlink);

						} else {					
							$output_archive .= "<div class=\"ta-twitter_user ta\">
							
							<ul class=\"ta-ul\">
							<li class=\"ta-image ta\"><img class=\"ta-avatar ta\" src=\"$image\"></li>
							<li class=\"ta-published ta\"><a href=\"$link\">$datetime</a></li>
							<li class=\"ta-user ta\"><a href=\"$uri\" target=\"_blank\">$name</a></li>
							<li class=\"ta-description ta\">$entry->title</li>
							
							</ul>
							</div>";
						}
					} 
				
			} //end foreach.
			
		} // end blackbirdpie no.
	}
	
	return $output_archive;
}

//Function to make usernames and hashtags hot links via Boone Gorges - https://github.com/boonebgorges
function bbg_pb_twitterize( $content ) {
    // Turn @-mentions into links
    $content = preg_replace("/[@]+([A-Za-z0-9-_]+)/", "<a href=\"http://twitter.com/\\1\" target=\"_blank\">\\0</a>", $content );

    // Turn hashtags into links
    $content = preg_replace("/ [#]+([A-Za-z0-9-_]+)/", " <a href=\"http://twitter.com/search?q=%23\\1\" target=\"_blank\">\\0</a>", $content );
	
	//A little addition of my own for this situation, to turn links into links. -Aram
	$reg_exUrl = "/\040(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
	$content = preg_replace($reg_exUrl, " <a href=\"\\0\">\\0</a>", $content );

    return $content;
} 

add_shortcode( 'searchtwitter', 'twitter_search_archive' );

//Some pretty styling to get it to look decent. 

function style_twitter_search_archive () {

	//Want to replace the default styling applied to tweets? Just add this CSS file to your stylesheet's directory.
	$theme_style_file = get_stylesheet_directory() . '/user-ta-style.css';

	//If you've added a user override CSS, then it will be used instead of the styling I have in the plugin.
	if ( file_exists($theme_style_file) ) {
		wp_register_style( 'user-twitter-archive-style', $theme_style_file );
		wp_enqueue_style( 'user-twitter-archive-style' );
	}
	//If not, you get the default styling. 
	else {
		wp_register_style( 'twitter-archive-style', plugins_url('ta-style.css', __FILE__) );
		wp_enqueue_style( 'twitter-archive-style' );
	}

}

add_action( 'wp_enqueue_scripts', 'style_twitter_search_archive', 1 );


// Since anything that auto-styles Twitter hashtags and at signs will mess up the workings of the shortcode, here's a built in alternative. No need to run another plugin. 
// Adapted from code at http://stackoverflow.com/questions/4913555/find-twitter-hashtags-using-jquery-and-apply-a-link
function zs_twitter_linked() {
	wp_enqueue_script( 'jquery' );
	?>
		<script type="text/javascript">
		<!--Note here the \s at the begining of the regular expression here. This is to enforce that it will only select from hashtags with a space in front of them. Otherwise it may alter links to anchors.-->
			hashtag_regexp = /\s#([a-zA-Z0-9]+)/g;

			function linkHashtags(text) {
				return text.replace(
					hashtag_regexp,
					' <a class="hashtag" target="_blank" href="http://twitter.com/#search?q=$1">#$1</a>'
				);
			}

			jQuery(document).ready(function(){
				jQuery('p').each(function() {
					jQuery(this).html(linkHashtags(jQuery(this).html()));
				});
			});
			
			jQuery('p').html(linkHashtags(jQuery('p').html()));  

			
		</script>
		<script type="text/javascript">
			
			at_regexp = /\s\u0040([a-zA-Z0-9]+)/g;
			
			function linkAt(text) {
				return text.replace(
					at_regexp,
					' <a class="twitter-user" target="_blank" href="http://twitter.com/$1">@$1</a>'
				);
			}

			jQuery(document).ready(function(){
				jQuery('p').each(function() {
					jQuery(this).html(linkAt(jQuery(this).html()));
				});
			});
			
			jQuery('p').html(linkAt(jQuery('p').html()));  			
			
		</script>
	<?php
}

add_action('wp_head', 'zs_twitter_linked');
?>