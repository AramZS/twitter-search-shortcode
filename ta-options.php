<?php

//Based on code at http://ottopress.com/2009/wordpress-settings-api-tutorial/

// add the admin options page
add_action('admin_menu', 'ta_admin_add_page');
function ta_admin_add_page() {
	add_options_page('Options for Twitter Archival Shortcode plugin', 'Twitter Archival Options', 'manage_options', 'ta', 'ta_options_page');
}


// display the admin options page
function ta_options_page() {
	?>
	<div>
	<h2>Twitter Archival Shortcode Options</h2>
	Options and documentation relating to the Twitter Archival Shortcode plugin.
	<form action="options.php" method="post">
	<?php settings_fields('ta_options'); ?>
	<?php do_settings_sections('twitter_archive'); ?>
	<br />
	<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
	</form>

		<div>
			<h3>The options you have when using the plugin:</h3>
			
			
			<p>The shortcode's default is the following:</p>
				<ul>
					<li><b>[searchtwitter for="Chronotope" within="" order="reverse" title="Twitter Archive for" blackbird="no"]</b></li>
				</ul>
			<ul style="list-style-type: circle;  list-style-position:outside;padding-left: 18px;">	
				<li>The <b>for</b> option contains the term you are searching Twitter for. Enter it just as you would into <a title="Twitter Search" href="http://search.twitter.com" target="_blank">http://search.twitter.com</a>. If you are using a hashtag, this is the place to put it: <b>for="#term"</b>.</li>
				
				<li>The <b>within</b> option designates a limiting time period. You can specify year in the full four number format as in <b>within="2012"</b> or year-month <b>within="2012-04"</b> or year-month-day <b>within="2012-04-28"</b>. This will limit shown tweets by year, month or day. Remember, nothing you can do will change that I can still only pull items from no more than 7 days back. Dates must be in the <em>YYYY-MM-DD</em> format.</li>
				
				<li>The <b>order</b> option designates if you would like it in <em>reverse</em> or <em>normal</em> order. In reverse order, the tweet captured with the earliest timestamp displayed first and goes in order from there. In normal order, the tweets appear like you would see them on Twitter, with the earliest one first.</li>
				
				<li><b>title</b> is the option that designates the wrapped title that precedes the Twitter archive. The text within will be followed by the search term hyperlinked to Twitter's search page. You can also enter <em>none</em> in this field and no H3 title will be generated.</li>
				
				<li>The <b>blackbird</b> feature is HIGHLY experimental. The concept is that captured tweets are displayed using the method of <a title="Twitter Blackbird Pie" href="http://wordpress.org/extend/plugins/twitter-blackbird-pie/" target="_blank">the Twitter Blackbird Pie plugin</a>. Using this requires that you have the Blackbird Pie plugin installed. I do not have good error checks working for this part yet, so don't use it if you don't know if the plugin is installed and activated. Even beyond all that, there is an additional problem. Each time Blackbird Pie generates one of its very good-looking tweets it queries Twitter's API. If you have 1500 tweets, or even around 100, Twitter gets upset and stops feeding you anything for a few seconds, enough time for the script to fail. So unless you are collecting a relatively small number of tweets, it is pretty much useless. For now... If you want to turn it on, enter <em>yes</em> into the <b>blackbird </b>option.</li>
				
				<li>This plugin also gives you the ability to override my styling. Just create a stylesheet named "<em>user-ta-style.css</em>" in your stylesheet directory and it will completely replace the stylesheet I provide.</li>
				
				<li>Note: You do not have to call all the options if you are not using them. Your shortcode can be as minimal as [searchtwitter for="term"]</li>
			</ul>

			<p>If you use this plugin and encounter <b>any</b> issues please inform me. Either here, <a title="Aram Zucker-Scharff on Twitter" href="http://twitter.com/chronotope" target="_blank">on Twitter</a>, <a title="Aram Zucker-Scharff on Facebook" href="https://www.facebook.com/aramzs" target="_blank">Facebook</a>, <a title="Aram Zucker-Scharff on Google Plus" href="http://aramzs.me/gplus" target="_blank">Google+</a>, or in <a title="Make a new issue on the Twitter Search Shortcode repository." href="https://github.com/AramZS/twitter-search-shortcode/issues/new" target="_blank">an issue post on GitHub</a>.</p>

			<p>Thank you!
			<br />
			-Aram Zucker-Scharff
			</p>
		</div>

	</div>

	<?php
}

// add the admin settings and such
add_action('admin_init', 'ta_admin_init');
function ta_admin_init(){
	register_setting( 'ta_options', 'ta_options', 'ta_options_validate' );
	add_settings_section('ta_main', 'Main Settings', 'ta_section_text', 'twitter_archive');
	add_settings_field('ta_datetime_select', 'Set Twitter Archive Timezone', 'ta_settings_datetime', 'twitter_archive', 'ta_main');
}

function ta_section_text() {
echo '<p>Set available defaults for your Twitter Archival Shortcodes.</p>';

	//testing
	/**
	$options = get_option('ta_options');
	echo "The options: ";
	print_r($options);
	**/


}

function ta_settings_datetime() {

	$options = get_option('ta_options');
	$setvalue = $options['datetime'];
	?>
		<select id="ta_options_datetime" name="ta_options[datetime]">
			<?php
			
				//echo '<option value="' . $setvalue . '">' . $setvalue . '</option>';
			
			?>
			<option value="America/New_York">America/New York</option>
			<option value="America/Los_Angeles">America/Los Angeles</option>
		
	<?php
	
	$idents = DateTimeZone::listIdentifiers();
	
	
	foreach ($idents as $timezone) {
	
		if ((!empty($setvalue)) && ($timezone == $setvalue)){
		
			echo '<option value="' . $timezone . '" selected="selected">' . $timezone . '</option>';
		
		} else {
		
			echo '<option value="' . $timezone . '">' . $timezone . '</option>';
		
		}
	}

	echo '</select>';
	
}

// validate our options
function ta_options_validate($input) {
$newinput['datetime'] = trim($input['datetime']);
//die( preg_match( '!\w!i', $newinput['datetime'] ) );
if(!preg_match('/^[-_\w\/]+$/i', $newinput['datetime'])) {
$newinput['datetime'] = '';
}
return $newinput;
}

?>