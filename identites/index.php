<?php
// Client language detection
// TODO : could be better !
$languages = getUserLanguage($_SERVER['HTTP_ACCEPT_LANGUAGE']);
if ($lang = filter_input(INPUT_GET, 'lang')) {
	if ($lang == 'en') {
			$locale = 'en';
			setlocale(LC_ALL, 'en_EN');
	} else {
		$locale = 'fr';
		setlocale(LC_ALL, 'fr_FR.utf8');
	}
} else {
	foreach ($languages as $language => $weight) {
		if (stristr($language, 'fr')) {
			$locale = 'fr';
			setlocale(LC_ALL, 'fr_FR.utf8');
			break;
		} else if (stristr($language, 'en')) {
			$locale = 'en';
			setlocale(LC_ALL, 'en_EN');
		} else {
			$locale = 'fr';
			setlocale(LC_ALL, 'fr_FR.utf8');
			break;
		}
	}
}
$localeInfo = localeconv();

function getUserLanguage($acceptLanguage) {
	$langs = array();

    // break up string into pieces (languages and q factors)
    preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $lang_parse);

    if (count($lang_parse[1])) {
        // create a list like "en" => 0.8
        $langs = array_combine($lang_parse[1], $lang_parse[4]);
    	
        // set default to 1 for any without q factor
        foreach ($langs as $lang => $val) {
            if ($val === '') {
            	$langs[$lang] = 1;
            }
        }

        // sort list based on value	
        arsort($langs, SORT_NUMERIC);
    }
    
    return $langs;
}

$images = array(
	'top'    => filter_input(INPUT_GET, 'part1'),
	'middle' => filter_input(INPUT_GET, 'part2'),
	'bottom' => filter_input(INPUT_GET, 'part3')
);

// Configure
$urlRoot = sprintf('http://%s%s', $_SERVER['SERVER_NAME'], dirname($_SERVER['PHP_SELF']));

// Count available identities
// TODO : this should be obtained with an API call
$countIdentities = pow(count(glob(sprintf('%s/images/full/*.png', dirname(__FILE__)))), 3);

$strings = array(
	'fr' => array(
		'info-1'     => "
<p>Mille Milliards De Hasard est un générateur d'identités incongrues.</p>
<p>Un projet inspiré par Raymond Queneau, les livres pour enfants, et l'émerveillement que procure la magie aléatoire de l'Internet.</p>",
		'info-2'     => 
sprintf('Le projet recense %s identités uniques à ce jour.', number_format($countIdentities, 0, $localeInfo['decimal_point'], $localeInfo['thousands_sep'])),
		'share'      => 'partager',
		'mix'        => 'brasser',
		'download'   => 'télécharger',
		'contribute' => 'contribuer',
		'close'      => "fermer (cliquer sur l'identité pour réouvrir)",
		'footer'     => 
sprintf('<a href="%s">Mille Milliards de Hasard</a> est développé conjointement par <a href="http://cobrafoutre.tumblr.com" title="Meet the Cobra">Cobra Foutre</a> et <a href="http://www.constructions-incongrues.net" title="Les Constructions Incongrues">Constructions Incongrues</a>.
			Le code source est <a href="https://github.com/contructions-incongrues/millemilliards">diffusé</a> sous license <a href="" title="">AGPL3</a>. Le projet est hébergé par <a href="http://www.pastis-hosting.net" title="L\'hébergeur dopé au Pastis">Pastis Hosting</a>.', $urlRoot)
),
	'en' => array(
		'info-1'     => "
<p>Mille Milliards De Hasard (Trillion of Random) is a generator of incongrous identities.</p>
<p>A project inspired by Raymond Queneau, children's books, and the wonder of hazardous magic given by the internet.</p>",
		'info-2'     => 
sprintf('This project gathers %d distinct identities.', number_format($countIdentities, 0, $localeInfo['decimal_point'], $localeInfo['thousands_sep'])),
		'share'      => 'share',
		'mix'        => 'remix',
		'download'   => 'download',
		'contribute' => 'contribute',
		'close'      => "close (click on identity to reopen)",
		'footer'     => 
sprintf('<a href="%s">This project</a> is maintained by <a href="http://cobrafoutre.tumblr.com" title="Meet the Cobra">Cobra Foutre</a> and <a href="http://www.constructions-incongrues.net" title="Les Constructions Incongrues">Constructions Incongrues</a>.
			Source code is <a href="https://github.com/contructions-incongrues/millemilliards">distributed</a> under a <a href="" title="">AGPL3</a> licence. Project is hosted by <a href="http://www.pastis-hosting.net" title="L\'hébergeur dopé au Pastis">Pastis Hosting</a>.', $urlRoot)
),
);
?>
<!DOCTYPE html>
<html xmlns:og="http://ogp.me/ns#">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		
		<title>Mille Milliards de Hasard</title>
		
		<script type="text/javascript" src="js/jquery-1.5.2.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui-1.8.12.custom.min.js"></script>
		<script src="js/behaviors.js?v=1"></script>
		
		<link rel="stylesheet" type="text/css" href="css/main.css?v=1" />
		<link href='http://fonts.googleapis.com/css?family=Expletus+Sans' rel='stylesheet' type='text/css'>
		<link rel="shortcut icon" type="image/png" href="images/static/favicon.png" /> 
	
		<!-- Opengraph -->
<?php if ($images['top'] && $images['middle'] && $images['bottom']): ?>
		<meta property="og:image" content="<?php echo sprintf('%s/download.php?part1=%s&part2=%s&part3=%s', $urlRoot, $images['top'], $images['middle'], $images['bottom']) ?>" />
<?php endif;  ?>
		
		<script type="text/javascript">
			var urlRoot = '<?php echo $urlRoot ?>';
<?php if (isset($_GET['refresh']) && filter_var($_GET['refresh'], FILTER_VALIDATE_INT)): ?>
			setInterval('$("a#random").click()', <?php echo $_GET['refresh'] ?>);
<?php endif; ?>
		</script>
	</head>

	<body>

	<img id="ln-top" src="images/static/cidrolin_top.png" />
	<img id="ln-middle" src="images/static/cidrolin_middle.png" />
	<img id="ln-bottom" src="images/static/cidrolin_bottom.png" />

	<img src="images/static/loader.gif" style="display:none;" />

	<div id="info">
		<div  id="info-about">
			<?php echo $strings[$locale]['info-1']?>
			<p>
				<?php echo $strings[$locale]['info-2'] ?>
			</p>

			<p style="text-align: center"><a href="api.php" id="random" title="Générer une nouvelle identité">♻</a><br /><?php echo $strings[$locale]['mix'] ?></p>

			<p class="button">
				<a href="<?php echo sprintf('%s/?part1=%s&part2=%s&part3=%s', $urlRoot, $images['top'], $images['middle'], $images['bottom']) ?>" id="permalink" title="Accéder à l'URL vers l'identité courante"><?php echo $strings[$locale]['share'] ?></a>
				&bull; <a href="<?php echo sprintf('%s/download.php?part1=%s&part2=%s&part3=%s', $urlRoot, $images['top'], $images['middle'], $images['bottom']) ?>" title="Télécharger l'image" id="download"><?php echo $strings[$locale]['download'] ?></a>
				&bull; <a href="contribute.php" title="Soumettre de nouvelles identités"><?php echo $strings[$locale]['contribute'] ?></a>
				<br /><input id="permalinkUrl" type="text" style="display:none" size="50" value="<?php echo sprintf('%s/?part1=%s&part2=%s&part3=%s', $urlRoot, $images['top'], $images['middle'], $images['bottom']) ?>" /> 
			</p>
			
			<p class="button"><a id="close" href="#"><?php echo $strings[$locale]['close'] ?></a></p>
		</div>

		<div id="bubble">
			<img src="images/static/bubble.gif" />
		</div>
	</div>

	<div id="content">
		<div id="top">
			<img class="icare" src="images/static/layer-top.png" />
		</div>

		<div id="middle">
			<img class="icare" src="images/static/layer-middle.png" />
		</div>
			
		<div id="bottom">
			<img class="icare" src="images/static/layer-bottom.png" />
		</div>
		<p id="footer">
			<?php echo $strings[$locale]['footer'] ?>
		</p>
	</div>
	
		<script type="text/javascript" src="http://www.google-analytics.com/urchin.js"></script>
		<script type="text/javascript"> 
            // <![CDATA[
            _uacct="UA-673133-2";
            urchinTracker();
            // ]]>
		</script>

	</body>

</html>
