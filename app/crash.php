<?php include 'auth.php'; ?>
<?php include '_header.php'; ?>
<h2>Crash Test</h2>
<?php
$factor = $_GET['factor'] ?? 1;

// FIXED: Validasi untuk mencegah division by zero dan XSS
if (!is_numeric($factor)) {
    echo "<p style='color:red'>Factor must be a number.</p>";
} else if ($factor == 0) {
    echo "<p style='color:red'>Cannot divide by zero!</p>";
} else {
    $result = 100 / $factor; 
    // FIXED: Output di-escape untuk mencegah XSS
    echo "100 / " . htmlspecialchars($factor) . " = " . htmlspecialchars($result);
}
?>
<?php include '_footer.php'; ?>