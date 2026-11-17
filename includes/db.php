<?php
// ============================================================
//  db.php — connects the site to the MySQL database.
//
//  Every page that needs data from the database includes this
//  file first. It uses PDO, the safe way to talk to MySQL from
//  PHP: queries are prepared, so user input can never change
//  the meaning of a query (no SQL injection).
//
//  The database is called "smart_sweep". It is created by the
//  file sql/schema.sql. On XAMPP the MySQL user is "root"
//  with no password — that is the XAMPP default.
// ============================================================

$host   = 'localhost';   // the database runs on the same machine (XAMPP)
$dbName = 'smart_sweep'; // the name of our database
$dbUser = 'root';        // XAMPP's default MySQL user
$dbPass = '';            // XAMPP's default: no password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
