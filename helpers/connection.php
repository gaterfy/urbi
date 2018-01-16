<?php
/*
 * connection.php
 *
 * authors : Berfy
 *
 * Enables the connection with the postgresql database hosted on webtp
 * If an error occured during the process, a message is printed out and the code stops running
 */

try {
    $connection = new PDO("pgsql:host=localhost;dbname=berfy", "root", "root");
} catch (PDOException $e) {
    echo "Connection error : ", $e->getMessage();
    exit();
}
?>
