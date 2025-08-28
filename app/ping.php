<?php include 'auth.php'; ?>
<?php include '_header.php'; ?>

<?php
// FIXED: Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<h2>Ping Server</h2>
<!-- FIXED: Menambahkan CSRF token untuk mencegah CSRF attack -->
<form method="post">
  <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
  <input name="target" placeholder="Enter IP or hostname..." required>
  <button>Ping!</button>
</form>
<?php
// FIXED: Validasi input yang ketat untuk mencegah command injection
if ($_POST) {
    // Validasi CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo "<p style='color:red'>Invalid CSRF token.</p>";
    } else if (!isset($_POST['target']) || empty($_POST['target'])) {
        echo "<p style='color:red'>Missing target parameter.</p>";
    } else {
        $target = $_POST['target'];
        
        // FIXED: Validasi format IP address atau hostname yang ketat
        if (filter_var($target, FILTER_VALIDATE_IP) || 
            (preg_match('/^[a-zA-Z0-9.-]+$/', $target) && strlen($target) <= 255)) {
            
            echo "<h3>Ping Result for: " . htmlspecialchars($target) . "</h3>";
            
            // FIXED: Menggunakan escapeshellarg untuk mencegah command injection
            $escapedTarget = escapeshellarg($target);
            $command = "ping -c 2 " . $escapedTarget;
            $output = shell_exec($command);
            echo "<pre>" . htmlspecialchars($output) . "</pre>";
        } else {
            echo "<p style='color:red'>Invalid target format. Please enter a valid IP address or hostname.</p>";
        }
    }
}

?>
<?php include '_footer.php'; ?>