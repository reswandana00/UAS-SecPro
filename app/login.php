<?php
include 'auth.php';

// FIXED: Menggunakan session untuk menyimpan data, bukan serialized cookie
class Profile {
    public $username;
    public $isAdmin = false;

    function __construct($u, $isAdmin = false) {
        $this->username = $u;
        $this->isAdmin = $isAdmin;
    }

    function __toString() {
        return "User: {$this->username}, Role: " . ($this->isAdmin ? "Admin" : "User");
    }
}

// FIXED: Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_POST) {
    // FIXED: Validasi CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid CSRF token.";
    } else {
    $u = $_POST['username'];
    $p = $_POST['password'];

    // FIXED: Menggunakan prepared statement untuk mencegah SQL injection
    $stmt = $GLOBALS['PDO']->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$u]);
    $row = $stmt->fetch();
    
    // FIXED: Verifikasi password menggunakan password_verify()
    if ($row && password_verify($p, $row['password'])) {
        $_SESSION['user'] = $row['username'];
        $_SESSION['role'] = $row['role'];

        // FIXED: Menggunakan session untuk menyimpan data user, bukan cookie yang bisa dimanipulasi
        $_SESSION['profile'] = [
            'username' => $row['username'],
            'isAdmin' => $row['role'] === 'admin'
        ];

        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Login failed.";
    }
    }
}
?>
<?php include '_header.php'; ?>
<h2>Login</h2>
<!-- FIXED: Menambahkan CSRF token untuk mencegah CSRF attack -->
<?php if (!empty($error)) echo "<p style='color:red'>" . htmlspecialchars($error) . "</p>"; ?>
<form method="post">
  <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
  <label>Username <input name="username" required></label>
  <label>Password <input type="password" name="password" required></label>
  <button type="submit">Login</button>
</form>
<?php include '_footer.php'; ?>