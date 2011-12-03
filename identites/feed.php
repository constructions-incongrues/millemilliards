<?php
// Time limitations
$now = time();
$tsFile = dirname(__FILE__).'/latestUpdate';
$lastUpdate = file_get_contents($tsFile);
$updateTs = $lastUpdate;
$lastIdentity = dirname(__FILE__).'/latestIdentity'; 
$updateRate = (int)filter_input(INPUT_GET, 'refresh');
if (!$updateRate) {
	$updateRate = 60 * 60 * 24;
}
if (!file_exists($tsFile) || $lastUpdate + $updateRate < $now) {
	// Configure
	$response = array('top' => null, 'middle' => null, 'bottom' => null);
	$store = sprintf('%s/images', dirname(__FILE__));
	$updateTs = time();
	
	// Get images list
	$imagesList = array(
		'top'    => glob(sprintf('%s/parts/1/*.png', $store)),
		'middle' => glob(sprintf('%s/parts/2/*.png', $store)),
		'bottom' => glob(sprintf('%s/parts/3/*.png', $store))
	);
	
	// Select random images
	$response = array(
		'top'    => basename($imagesList['top'][array_rand($imagesList['top'])]),
		'middle' => basename($imagesList['middle'][array_rand($imagesList['middle'])]),
		'bottom' => basename($imagesList['bottom'][array_rand($imagesList['bottom'])])
	);
	
	$imgDownloadUrl = sprintf(
		'http://www.millemilliards.net/identites/download.php?part1=%s&part2=%s&part3=%s',
		$response['top'],
		$response['middle'],
		$response['bottom']
	);
	
	$imgUrl = sprintf(
		'http://www.millemilliards.net/identites/?part1=%s&part2=%s&part3=%s',
		$response['top'],
		$response['middle'],
		$response['bottom']
	);
	
	// Fetch random MP3 and enclose
	$dataUrl = 'http://data.musiques-incongrues.net/collections/links/segments/mp3/get?sort_field=random&limit=1&format=php';
	$curl = curl_init($dataUrl);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$response = unserialize(curl_exec($curl));
	$mp3 = $response[0];
	$enclosureUrl = $mp3['url'];
	
	// Update last update
	file_put_contents($tsFile, $now);
	file_put_contents($lastIdentity, json_encode(array(
		'url' 			=> $imgUrl, 
		'download_url' 	=> $imgDownloadUrl,
		'enclosure_url'	=> $mp3['url'])
	));
} else {
	$last = json_decode(file_get_contents($lastIdentity), true);
	$imgDownloadUrl = $last['download_url'];
	$imgUrl = $last['url'];
	$enclosureUrl = $last['enclosure_url'];
}

/**
 * Create the parent feed
 */
set_include_path(get_include_path().PATH_SEPARATOR.'/usr/share/php');
require_once('/usr/share/php/Zend/Feed/Writer/Feed.php');
$feed = new Zend_Feed_Writer_Feed();
$feed->setTitle('Mille Milliards | Identités');
$feed->setLink('http://www.mille-milliards.net');
$feed->setFeedLink('http://www.mille-milliards.net/feed.php', 'rss');
$feed->setDescription("Mille Milliards De Hasard est un générateur d'identités incongrues.");
$feed->setDateModified($updateTs);

/**
 * Add one or more entries. Note that entries must
 * be manually added once created.
 */
$entry = $feed->createEntry();
$entry->setTitle(sprintf("L'identité incongrue du %s", date('d/m/Y', $updateTs)));
$entry->setLink($imgUrl);
$entry->setDateModified($updateTs);
$entry->setDateCreated($updateTs);
$entry->setContent(sprintf("
	<p>Un peu de musique ? %s</p>
	<img src=\"%s\" />"
, $enclosureUrl, $imgDownloadUrl));
$entry->setEnclosure(array('uri' => $enclosureUrl, 'type' => 'audio/mpeg', 'length' => 666));
$feed->addEntry($entry);

/**
 * Render the resulting feed to Atom 1.0 and assign to $out.
 * You can substitute "atom" with "rss" to generate an RSS 2.0 feed.
 */
header('Content-Type: text/xml');
echo $feed->export('rss');