<?php
// Declare available messages
$messages = array();
$messages['default'] = array(
	'text' => 'DEFAULT / TODO', 
	'params' => array()
);
$messages['created'] = array(
	'text' => 'Great job ! Vous venez de créer <strong>%d</strong> nouvelles identités.', 
	'params' => array('count' => FILTER_VALIDATE_INT)
);

// Select requested messages
$messageName = filter_input(INPUT_GET, 'message');
if (!$messageName || !isset($messages[$messageName])) {
	$message = $messages['default'];
} else {
	$message = $messages[$messageName];
}

// Check message parameters
$messageParameters = array();
foreach ($message['params'] as $paramName => $paramValidator) {
	if (!isset($_GET[$paramName])) {
		throw new InvalidArgumentException(sprintf('Missing parameter "%s"', $paramName));
	}
	$messageParameters[$paramName] = filter_input(INPUT_GET, $paramName, $paramValidator);
}
$messageText = call_user_func_array('sprintf', array_merge(array($message['text']), array_values($messageParameters)));

// TODO : return JSON object : text / audio
$response = array('text' => $messageText);
$responseJson = json_encode($response);

// Response headers
header('Content-Type: application/json; charset=utf-8');
header(sprintf('Content-Length: %d', strlen($responseJson)));

// Output response body
echo $responseJson;
exit(0);

print_r($response);