<?php
require __DIR__ . '/../src/SimpleMail.php';

use Landlib\SimpleMail;

function consoleLog($k, $v, $bIsVarDump = false) {
	echo "{$k}:\n";
	if ($bIsVarDump) {
		var_dump($v);
	} else {
		print_r($v);
	}
}


//Simple email
$sender = 'yourmailbox@gmail.com';
$recipient = 'yourothermailbox@gmail.com';
$mailer = new SimpleMail();
$mailer->setSubject('It test package landlib/simplemail');
$mailer->setFrom($sender, 'Andrey Lamzin');
$mailer->setTo($recipient);
$mailer->setBody('Hello, my friend', 'text/html', 'UTF-8');
$r = $mailer->send();
var_dump($r);

//Mail with attach
$mailer->setSubject('It test package landlib/simplemail - mail with attachment');
$mailer->setTextWithImages('Hello, my friend, {smile}!' . "\nI am a very satisfied person!", ['{smile}' => __DIR__ . '/smile.png']);
$r = $mailer->send();
var_dump($r);


