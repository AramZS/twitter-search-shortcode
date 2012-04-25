<?php

$file="http://search.twitter.com/search.atom?q=me3log&rpp=100";
$xml = simplexml_load_file($file);
print_r($xml);
foreach($xml->entry as $entry)
	{
		$a[]=$entry;
	}
$orderedxml = array_reverse($a);
echo "<br />More:";
echo is_array($a) ? 'Array' : 'not an Array';
echo "<br />Even More:";
echo is_array($orderedxml) ? 'Array' : 'not an Array';
print_r($orderedxml);

	foreach($orderedxml as $entry)
	{
		$uri=$entry->author->uri;
		$name=$entry->author->name;
		$image=$entry->link[1]['href'];
		$published = $entry->published;
		$description = $entry->title;

			?><div class="twitter_user">
			
			<ul>
				<li class="image"><img src="<?php echo $image; ?>"></li>
				<li class="published"><?php echo $published; ?></li>
				<li class="user"><a href="<?php echo $uri; ?>" target="_blank"><?php echo $name; ?></a></li>
				<li class="description"><?php echo $description; ?></li>
			
			</ul>
			</div>
			<?php
		
	}

?>