<?php include 'auth.php'; ?>
<?php include '_header.php'; ?>
<h2>Wiki Search</h2>
<!-- FIXED: Search menggunakan GET method yang aman -->
<form>
  <input name="q" placeholder="Search articles..." required>
  <button>Search</button>
</form>
<?php
if (isset($_GET['q'])) {
    $q = $_GET['q'];
    // FIXED: Menggunakan prepared statement untuk mencegah SQL injection
    $stmt = $GLOBALS['PDO']->prepare("SELECT * FROM articles WHERE title LIKE ?");
    $stmt->execute(['%' . $q . '%']);
    
    echo "<h3>Search Results:</h3>";
    $results = $stmt->fetchAll();
    if (count($results) > 0) {
        foreach ($results as $row) {
            // FIXED: Output di-escape untuk mencegah XSS
            echo "<li>" . htmlspecialchars($row['title']) . ": " . htmlspecialchars($row['body']) . "</li>";
        }
    } else {
        echo "<p>No articles found for: " . htmlspecialchars($q) . "</p>";
    }
}
?>
<?php include '_footer.php'; ?>