<?php

require __DIR__ . "/config/Database.class.php";
require __DIR__ . "/mail/Mail.class.php";

$db = new Database();
$db->getConnection();
$mail = new Mail($db);
$mail->createUser($argv);



