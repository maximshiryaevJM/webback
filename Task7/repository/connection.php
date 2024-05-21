<?php
include  __DIR__ . '/../../variables.php';

$db = new PDO('mysql:host=localhost;' . $dbname, $user, $pass,
    [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

