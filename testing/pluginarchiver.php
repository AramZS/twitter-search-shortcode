<?php
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



	$for = "#me3log";
	$within = '';
	$order = "reverse";
	$safefor = urlencode($for);
	
	$twitterquery = get_twitter_query($for);
	
	$queryresults = execute_twitter_query($twitterquery, $within, $order, $keepGoing, $archive);
	
	$archive .= '<h3 class="twitter-archival-title ta">Twitter Archive for <a href="https://twitter.com/search/' . $safefor . '" target="_blank">' . $for . '</a></h3>';
	
	$archive .= $queryresults;
	
	$archive .= "</div>
				 </div><!--End of Twitter Archive-->";
	




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

					if (!(empty($xml->entry))){	
					$execute_archive .= output_data($xml,$datelimit,$ordered,$keepGoing);
					}
					
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
					<li class=\"ta-published ta\">$entry->published</li>
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
					<li class=\"ta-published ta\">$entry->published</li>
					<li class=\"ta-user ta\"><a href=\"$uri\" target=\"_blank\">$name</a></li>
					<li class=\"ta-description ta\">$entry->title</li>
					
					</ul>
					</div>";
			
			} //End of datecheck. 
		
		}
	}
	
	return $output_archive;
}
?>
<html>
<body>
<?php echo $archive; ?>
</body>
</html>