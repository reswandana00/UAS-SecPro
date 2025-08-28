<?php include 'auth.php'; ?>
<?php include '_header.php'; ?>

<?php
// FIXED: Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<h2>Post comments</h2>
<!-- FIXED: Menambahkan CSRF token untuk mencegah CSRF attack -->
<form method="post">
  <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
  <input name="author" placeholder="Name..." required>
  <textarea name="content" placeholder="Comments..." required></textarea>
  <button>Post</button>
</form>

<?php
if ($_POST) {
    // FIXED: Validasi CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo "<p style='color:red'>Invalid CSRF token.</p>";
    } else {
        $stmt = $GLOBALS['PDO']->prepare("INSERT INTO comments(author,content,created_at) VALUES(?,?,datetime('now'))");
        $stmt->execute([$_POST['author'], $_POST['content']]);
        echo "<p style='color:green'>Comment posted successfully!</p>";
    }
}
?>
<h3>Comment lists : </h3>
<?php
foreach ($GLOBALS['PDO']->query("SELECT * FROM comments ORDER BY id DESC") as $row) {
    // FIXED: Output di-escape untuk mencegah XSS attack
    echo "<p><b>" . htmlspecialchars($row['author']) . "</b>: " . htmlspecialchars($row['content']) . "</p>";
}
?>
<?php include '_footer.php'; ?>