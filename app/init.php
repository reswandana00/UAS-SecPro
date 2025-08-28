<?php
$dbFile = __DIR__ . '/data/app.db';
$needSeed = !file_exists($dbFile);

$pdo = new PDO('sqlite:' . $dbFile);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($needSeed) {
    $pdo->exec("
        CREATE TABLE users(id INTEGER PRIMARY KEY, username TEXT, password TEXT, role TEXT);
        CREATE TABLE articles(id INTEGER PRIMARY KEY, title TEXT, body TEXT);
        CREATE TABLE comments(id INTEGER PRIMARY KEY, author TEXT, content TEXT, created_at TEXT);
    ");
    // FIXED: Password sekarang di-hash dengan password_hash()
    $hashedAlice = password_hash('alice123', PASSWORD_DEFAULT);
    $hashedAdmin = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->exec("INSERT INTO users(username,password,role) VALUES('alice','$hashedAlice','user')");
    $pdo->exec("INSERT INTO users(username,password,role) VALUES('admin','$hashedAdmin','admin')");
    $pdo->exec("INSERT INTO articles(title,body) VALUES('PHP','Server side scripting')");
    $pdo->exec("INSERT INTO articles(title,body) VALUES('Java','Programming language')");
}
$GLOBALS['PDO'] = $pdo;