<?php

/*
Plugin Name: Twitter Archival Shortcode
Plugin URI: http://aramzs.me/twitterarchival
Description: This plugin allows you to place a shortcode in your post that will archive a Twitter search term, including hashtags. 
Version: 0.5
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

//Establish date variable.
$now=getdate();
//$date="2010-02-11T05:59:21Z";
$date=$now["year"];
//$date="2010";

//Establish page variable.
$page=1;

//You may want to limit it by a day, month, or year. In which case we will need this:
$keepGoing = true;



//Create object to contain the archive.
$archive = ' <div class="twitter-archival-container ta">';

function twitter_search_archive( $atts ) {

	$post_id = get_the_ID();

	add_post_meta($post_id, 'twitter_search_archive', '', true);

	$checkcache=get_post_meta($post_id, 'twitter_search_archive', true);
	if (empty($checkcache)) {
		extract( shortcode_atts( array(
			'for' => 'Chronotope',
			'within' => '',
			'order' => 'reverse',
			'title' => 'Twitter Archive for'
		), $atts ) );
		
		$safefor = urlencode($for);
		
		$twitterquery = get_twitter_query($for);
		
		$queryresults = execute_twitter_query($twitterquery, $within, $order, $keepGoing, $archive);
		
		//If the user does not indicate 'none' for no title, use the default or title entered. 
		if ( !($title=="none") ){
			$archive .= '<h3 class="twitter-archival-title ta">' . $title . '<a href="https://twitter.com/search/' . $safefor . '" target="_blank"> ' . $for . '</a></h3>';
		} 
		
		$archive .= $queryresults;
		
		$archive .= "</div>
					 </div><!--End of Twitter Archive-->";
		
		update_post_meta($post_id, 'twitter_search_archive', $archive);
	
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

function execute_twitter_query($file, $datelimit, $ordered, $keepGoing) {

$execute_archive = '';

	for($page=15;$page>=1;$page--)
		{
			$thefile="$file&page=$page";
			if (fopen ($file, "r")) 
				{
					$xml = simplexml_load_file($thefile);

						
					$execute_archive .= output_data($xml,$datelimit,$ordered,$keepGoing);

				}
				else 
				{
					$execute_archive .= " Sorry, Twitter doesn't seem to be working right now. Try again later.";
				}
		}
		
		return $execute_archive;

}

function output_data($xml,$datelimit,$ordered,$keepGoing)
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

	
		foreach($orderedxml as $entry)
		{
			$uri=$entry->author->uri;
			$name=$entry->author->name;
			$image=$entry->link[1]['href'];
			$link=$entry->link[0]['href'];
			$timestamp = $entry->published;
			$unixtime = strtotime($timestamp);
			$datetime = date('h:i:s A, n-j-y', $unixtime);
			
			//If you've designated a date to retrieve from, either a year, month or day, you can restrict Tweets that appear only to that period. 
			if (!empty($datelimit)) {
				//The given date string must be in the 2012-4-24 format
				//If it matches the published time of the tweet, save it to the object. 
				//Otherwise, don't.
				if(strstr($entry->published,$datelimit))
				{
					$output_archive .= "<div class=\"ta-twitter_user ta\">
					
					<ul>
					<li class=\"ta-image ta\"><img class=\"ta-avatar ta\" src=\"$image\"></li>
					<li class=\"ta-published ta\"><a href=\"$link\" target=\"_blank\">$datetime</a></li>
					<li class=\"ta-user ta\"><a href=\"$uri\" target=\"_blank\">$name</a></li>
					<li class=\"ta-description ta\">$entry->title</li>
					
					</ul>
					</div>";
					$keepGoing = true;
				}
				else { $keepGoing = false; }
			} 
			else {
			
					$output_archive .= "<div class=\"ta-twitter_user ta\">
					
					<ul>
					<li class=\"ta-image ta\"><img class=\"ta-avatar ta\" src=\"$image\"></li>
					<li class=\"ta-published ta\"><a href=\"$link\">$datetime</a></li>
					<li class=\"ta-user ta\"><a href=\"$uri\" target=\"_blank\">$name</a></li>
					<li class=\"ta-description ta\">$entry->title</li>
					
					</ul>
					</div>";
			
			} //End of datecheck. 
		
		}
	}
	
	return $output_archive;
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

add_action( 'wp_enqueue_scripts', 'style_twitter_search_archive' );


?>