<?php

$server = 'localhost';
$database = 'roomfinder';
$username = 'root';
$password = '';
$charset = 'utf8';

$dsn = "mysql:host=$server;dbname=$database;charset=$charset";

$option = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $connect = new PDO($dsn, $username, $password, $option);
}
catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

?>