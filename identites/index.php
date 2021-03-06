<?php
$assetsVersion = 11;

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
	'part1' => filter_input(INPUT_GET, 'part1'),
	'part2' => filter_input(INPUT_GET, 'part2'),
	'part3' => filter_input(INPUT_GET, 'part3')
);
$identityId = null;
if ($images['part1']) {
	$matches = array();
	preg_match('/millemilliard_(.+)_part_1.png/', $images['part1'], $matches);
	$identityId = $matches[1];
}

// Configure
$urlRoot = sprintf('http://%s%s', $_SERVER['SERVER_NAME'], dirname($_SERVER['PHP_SELF']));

// Count available identities
// TODO : this should be obtained with an API call
$countIdentities = pow(count(glob(sprintf('%s/images/full/*.png', dirname(__FILE__)))), 3);

$strings = array(
	'fr' => array(
		'info-1'     => "
<p>Mille Milliards De Hasard est un générateur d'identités incongrues.
Un projet <strong>ouvert et collaboratif</strong> inspiré par Raymond Queneau, les livres pour enfants, et l'émerveillement que procure la magie aléatoire de l'Internet.</p>",
		'info-2'     => 
sprintf('Le projet recense <strong>%s identités uniques</strong> à ce jour.', number_format($countIdentities, 0, $localeInfo['decimal_point'], $localeInfo['thousands_sep'])),
		'share'      => 'partager',
		'download'   => 'télécharger',
		'contribute' => 'contribuer',
		'about'      => 'à propos',
		'footer'     => sprintf('<p>Ailleurs : <a href="feeds.feedburner.com/mille-milliards-identites">RSS</a> / <a href="https://www.facebook.com/pages/Mille-Milliards-de-Hasards/269861196377758">Facebook</a></p>', $urlRoot),
		'contribute.submit_title' => 'Soumettre une nouvelle identité',
		'contribute.image_constraints' => "L'image doit être au format PNG et avoir<br /> pour dimensions 800x600 pixels.",
		'contribute.download' => 'Télécharger le modèle',
		'contribute.submit' => 'Envoyer'
),
	'en' => array(
		'info-1'     => "
<p>Mille Milliards De Hasard (Trillion of Random) is a generator of incongrous identities.
A <strong>collaborative and open</strong> project inspired by Raymond Queneau, children's books, and the wonder of hazardous magic given by the internet.</p>",
		'info-2'     => 
sprintf('This project gathers <strong>%d</strong> distinct identities.', number_format($countIdentities, 0, $localeInfo['decimal_point'], $localeInfo['thousands_sep'])),
		'share'      => 'share',
		'download'   => 'download',
		'contribute' => 'contribute',
		'about'      => 'about',
		'footer'     => sprintf('<p>Elsewhere : <a href="feeds.feedburner.com/mille-milliards-identites">RSS</a> / <a href="https://www.facebook.com/pages/Mille-Milliards-de-Hasards/269861196377758">Facebook</a></p>', $urlRoot),
		'contribute.submit_title' => 'Submit a new identity',
		'contribute.image_constraints' => "Image must be in PNG format, 800x600 pixels.",
		'contribute.download' => 'Download layer',
		'contribute.submit' => 'Send'

),
);
?>
<!DOCTYPE html>
<html xmlns:og="http://ogp.me/ns#">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?php if ($identityId): ?>
		<title><?php echo $identityId ?> | Identités | Mille Milliards</title>
<?php else: ?>
		<title>Identités | Mille Milliards</title>
<?php endif; ?>
		
		<script type="text/javascript" src="js/jquery-1.5.2.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui-1.8.12.custom.min.js"></script>
		<script type="text/javascript" src="js/jquery.flip.min.js"></script>
		<script src="js/behaviors.js?v=<?php echo $assetsVersion ?>"></script>
		
		<link rel="stylesheet" type="text/css" href="css/main.css?v=<?php echo $assetsVersion ?>" />
		<link href='http://fonts.googleapis.com/css?family=Didact+Gothic' rel='stylesheet' type='text/css'>
		<link rel="shortcut icon" type="image/png" href="images/static/favicon.png" />
		<link type="application/rss+xml" title="Identités | Mille Milliards" rel="alternate" href="http://feeds.feedburner.com/mille-milliards-identites" /> 
	
		<!-- Opengraph -->
		<meta property="og:description" content="<?php echo strip_tags($strings[$locale]['info-1']) ?>" />
<?php if ($images['part1'] && $images['part2'] && $images['part3']): ?>
		<meta property="og:image" content="<?php echo sprintf('%s/download.php?part1=%s&part2=%s&part3=%s', $urlRoot, $images['part1'], $images['part2'], $images['part3']) ?>" />
<?php else: ?>
		<meta property="og:image" content="<?php echo sprintf('%s/images/static/major.png', $urlRoot) ?>" />
<?php endif;  ?>
		
		<script type="text/javascript">
			var urlRoot = '<?php echo $urlRoot ?>';
<?php if (isset($_GET['refresh']) && filter_var($_GET['refresh'], FILTER_VALIDATE_INT)): ?>
			setInterval('$("a#random").click()', <?php echo $_GET['refresh'] ?>);
<?php endif; ?>
		</script>
	</head>

	<body>
	
	<!-- Placeholders -->
	<img id="ln-top" />
	<img id="ln-middle" />
	<img id="ln-bottom" />
	
	<p style="display:none;"><a href="api.php" id="random" title="Générer une nouvelle identité">♻</a></p>
	
	<div id="info" style="display:none;">
		<div id="info-about">
			<?php echo $strings[$locale]['info-1']?>
			<p><?php echo $strings[$locale]['info-2'] ?></p>
			<p><?php echo $strings[$locale]['footer'] ?></p>
		</div>
	</div>

	<div id="contribute" style="display:none;">
		<div id="info-contribute">		
			<h2><?php echo $strings[$locale]['contribute.submit_title'] ?></h2>
			<p><?php echo $strings[$locale]['contribute.image_constraints'] ?></p>
			<p><a href="images/static/anonymous.png" title="<?php echo $strings[$locale]['contribute.download'] ?>"><?php echo $strings[$locale]['contribute.download'] ?></a></p>
			<form method="post" enctype="multipart/form-data" action="contribute.php">
				<p><input type="file" name="file" size="30" /></p>
				<p><input type="submit" name="upload" value="<?php echo $strings[$locale]['contribute.submit'] ?>" /></p>
			</form>
		</div>
	</div>


	<div id="sharebox" style="display:none;">
	<p>&nbsp;</p>
	<a href="#" class="close">close</a>
	</div>

	<div id="container">
		
		<ul id="menu">
			<li style="display:none;"><a class="back" href="index.php">Retour</a></li>
			<li><a class="about" href="" title="<?php echo $strings[$locale]['about'] ?>"><?php echo $strings[$locale]['about'] ?></a></li>
			<li><a class="contribute" href="contribute.php" title="<?php echo $strings[$locale]['contribute'] ?>"><?php echo $strings[$locale]['contribute'] ?></a></li>
<?php if ($images['part1']): ?>
			<li><a class="share" href="<?php echo sprintf('%s/?part1=%s&part2=%s&part3=%s', $urlRoot, $images['part1'], $images['part2'], $images['part3']) ?>" title="<?php echo $strings[$locale]['share'] ?>"><?php echo $strings[$locale]['share'] ?></a></li>
			<li><a class="download" href="<?php echo sprintf('%s/download.php?part1=%s&part2=%s&part3=%s', $urlRoot, $images['part1'], $images['part2'], $images['part3']) ?>" title="<?php echo $strings[$locale]['download'] ?>"><?php echo $strings[$locale]['download'] ?></a></li>
<?php else: ?>
			<li style="display:none;"><a class="share" href="<?php echo sprintf('%s/?part1=%s&part2=%s&part3=%s', $urlRoot, $images['part1'], $images['part2'], $images['part3']) ?>" title="<?php echo $strings[$locale]['share'] ?>"><?php echo $strings[$locale]['share'] ?></a></li>
			<li style="display:none;"><a class="download" href="<?php echo sprintf('%s/download.php?part1=%s&part2=%s&part3=%s', $urlRoot, $images['part1'], $images['part2'], $images['part3']) ?>" title="<?php echo $strings[$locale]['download'] ?>"><?php echo $strings[$locale]['download'] ?></a></li>
<?php endif; ?>
		</ul>
		
		<div id="content" class="identity">
<?php if($images['part1']): ?>
			<div id="top" class="part" style="background-image:url(<?php echo sprintf('images/parts/1/%s', $images['part1']) ?>);">
				<img class="icare" src="images/static/layer-top.png" />
			</div>
<?php else: ?>
			<div id="top" class="part" style="background-image:url(images/static/major_top.png);">
				<img class="icare" src="images/static/layer-top.png" />
			</div>
<?php endif; ?>

	
<?php if($images['part2']): ?>
			<div id="middle" class="part" style="background-image:url(<?php echo sprintf('images/parts/2/%s', $images['part2']) ?>);">
				<img class="icare" src="images/static/layer-middle.png" />
			</div>
<?php else: ?>
			<div id="middle" class="part" style="background-image:url(images/static/major_middle.png);">
				<img class="icare" src="images/static/layer-middle.png" />
			</div>
<?php endif; ?>

				
<?php if($images['part3']): ?>
			<div id="bottom" class="part" style="background-image:url(<?php echo sprintf('images/parts/3/%s', $images['part3']) ?>);">
				<img class="icare" src="images/static/layer-bottom.png" />
			</div>
<?php else: ?>
			<div id="bottom" style="background-image:url(images/static/major_bottom.png);">
				<img class="icare" src="images/static/layer-bottom.png" />
			</div>
<?php endif; ?>

		</div>
		<div id="footer">
			<a href="%s">Mille Milliards de Hasard</a> est développé conjointement par <a href="http://cobrafoutre.tumblr.com" title="Meet the Cobra">Cobra Foutre</a> et <a href="http://www.constructions-incongrues.net" title="Les Constructions Incongrues">Constructions Incongrues</a>.
			Le code source est <a href="https://github.com/contructions-incongrues/millemilliards">diffusé</a> sous license <a href="" title="">AGPL3</a>. Le projet est hébergé par <a href="http://www.pastis-hosting.net" title="L\'hébergeur dopé au Pastis">Pastis Hosting</a>.		
		</div>
	</div>
	
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-27467726-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
	</body>

</html>
