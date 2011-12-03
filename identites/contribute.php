<?php
$feedback = null;
$store = dirname(__FILE__).'/images'; 
if (isset($_FILES['file'])) {
	try {
	// Sanity checks
	// -- File type
	if ($_FILES['file']['type'] != 'image/png') {
		throw new InvalidArgumentException('Error : Image must be of type "image/png"', 400);
	}
	// -- File size
	$imageSize = getimagesize($_FILES['file']['tmp_name']);
	if ($imageSize[0] !== 800 || $imageSize[1] !== 600) {
		throw new InvalidArgumentException(sprintf('Error : Image dimensions must be 800x600 (uploaded image dimensions : %s)', $imageSize[3]), 400);
	}
	
	// Build image name
	$imageName = uniqid('millemilliard_');
	
	// Cut image
	$image = imagecreatefrompng($_FILES['file']['tmp_name']);
	$imageTop = imagecreatetruecolor(800, 200);
	$imageMiddle = imagecreatetruecolor(800, 200);
	$imageBottom = imagecreatetruecolor(800, 200);
    imagecopyresampled($imageTop, $image, 0, 0, 0, 0, 800, 200, 800, 200);
    imagecopyresampled($imageMiddle, $image, 0, 0, 0, 200, 800, 200, 800, 200);
    imagecopyresampled($imageBottom, $image, 0, 0, 0, 400, 800, 200, 800, 200);

	// Count created identities 
	// TODO : this should be obtained with an API call
	$countIdentitiesPrev = pow(count(glob(sprintf('%s/images/full/*.png', dirname(__FILE__)))), 3);
	
	// Store files
	move_uploaded_file($_FILES['file']['tmp_name'], sprintf('%s/full/%s.png', $store, $imageName));
	imagepng($imageTop, sprintf('%s/parts/1/%s_part_1.png', $store, $imageName));
	imagepng($imageMiddle, sprintf('%s/parts/2/%s_part_2.png', $store, $imageName));
	imagepng($imageBottom, sprintf('%s/parts/3/%s_part_3.png', $store, $imageName));
	
	// Count created identities 
	// TODO : this should be obtained with an API call
	$countIdentitiesNew = pow(count(glob(sprintf('%s/images/full/*.png', dirname(__FILE__)))), 3);
	$countIdentitiesCreated = $countIdentitiesNew - $countIdentitiesPrev;

	// Free resources
	imagedestroy($image);
	imagedestroy($imageTop);
	imagedestroy($imageMiddle);
	imagedestroy($imageBottom);
	
	// Redirect user to uploaded parts 
	$url = sprintf('index.php?part1=%s_part_1.png&part2=%s_part_2.png&part3=%s_part_3.png&created=%d', $imageName, $imageName, $imageName, $countIdentitiesCreated);
	$feedback = array('class' => 'success', 'text' => sprintf('Vous venez de créer <strong>%d</strong> nouvelles identités.', $countIdentitiesCreated));
	header(sprintf('Refresh:2;url=%s', $url));
	
	
	} catch (Exception $e) {
		$feedback = array('class' => 'error', 'text' => $e->getMessage());
		header(sprintf('Refresh:2;url=%s', 'index.php'));
	}
}
?>
<!DOCTYPE html>
<html>

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>Mille Milliards de Hasard - Contribution</title>
		<link rel="shortcut icon" type="image/png" href="images/static/favicon.png" />
		<style type="text/css">
			body {margin:auto;text-align:center;font-size:4em;}
			p.success, p.error {color: white;background-color:black;}
		</style>
	</head>
	
	<body>
	
<?php if($feedback): ?>
		<p class="<?php echo $feedback['class'] ?>"><strong><?php echo $feedback['text'] ?></strong></p>
<?php endif; ?>

	</body>

</html>
