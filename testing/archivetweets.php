<?php
//Original script from Daniel Thorogood, found at http://twitter.com/SLODeveloper
//Received through the kind folks who run #wjchat 

//require('../../shared/sendMail.php');
$recipients=array("dthorogood@gmail.com");

$query=urlencode("#me3log");
$file="http://search.twitter.com/search.atom?q=$query&rpp=100";
//$date="2010-04-01";
$now=getdate();
//$date="2010-02-11T05:59:21Z";
$date=$now["year"]."-".str_pad($now["mon"],2,"0",STR_PAD_LEFT)."-".str_pad($now["mday"]+1,2,"0",STR_PAD_LEFT);
//$date="2010-07-01";
echo $date;
$page=1;
$keepGoing=true;
echo $file;
$outfile=fopen('wjchat2.html',"w") or die ("can't open file");

	$thefile="$file&page=$page";
	
		
			$xml = simplexml_load_file($file);
			output_data($xml,$date,$keepGoing, $outfile);
		


fclose($outfile);
//sendMailWithAttachment($recipients,"WJChat Log","Yay!","dthorogood@gmail.com","Danny",'/var/www/slonews/test/twitter/wjchat2.html',"wjchat_log.html");


function output_data($xml,$date,$keepGoing,$outfile)
{

	foreach($xml->entry as $entry)
	{
		$a[]=$entry;
	}
	$orderedxml=array_reverse($a);
	print_r($orderedxml);
	foreach($orderedxml as $entry)
	{
		$uri=$entry->author->uri;
		$name=$entry->author->name;
		$image=$entry->link[1]['href'];

			fwrite($outfile, "<div class=\"twitter_user\">
			
			<ul>
				<li class=\"image\"><img src=\"$image\"></li>
				<li class=\"published\">$entry->published</li>
				<li class=\"user\"><a href=\"$uri\" target=\"_blank\">$name</a></li>
				<li class=\"description\">$entry->title</li>
			
			</ul>
			</div>");
			$keepGoing = true;
		
	}
}
?>
