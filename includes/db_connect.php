db_connect.php
<?php
$host = 'localhost'; // Server name
$db = 'f-code'; // Database name
$user = 'root'; // Username for MySQL
$pass = ''; // Password for MySQL (leave blank if no password)

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    // Set PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully"; 
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>