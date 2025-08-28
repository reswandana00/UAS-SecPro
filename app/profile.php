<?php
include 'auth.php';

// FIXED: Menggunakan session daripada cookie yang dapat dimanipulasi
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

// FIXED: Menggunakan data dari session yang aman
if (!isset($_SESSION['profile'])) {
    die("Profile data tidak ditemukan. Silakan login ulang.");
}

$profile = new Profile($_SESSION['profile']['username'], $_SESSION['profile']['isAdmin']);

// jika admin, boleh hapus user lain
if ($profile->isAdmin && isset($_POST['delete_user'])) {
    // FIXED: Validasi CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $msg = "<p style='color:red'>Invalid CSRF token.</p>";
    } else {
        $target = $_POST['delete_user'];
        // FIXED: Menggunakan prepared statement untuk mencegah SQL injection
        $stmt = $GLOBALS['PDO']->prepare("DELETE FROM users WHERE username = ?");
        $stmt->execute([$target]);
        $msg = "<p style='color:green'>User <b>" . htmlspecialchars($target) . "</b> berhasil dihapus!</p>";
    }
}

include '_header.php';
?>
<h2>Profile Page</h2>
<p><?php echo $profile; ?></p>

<?php if ($profile->isAdmin): ?>
  <h3>Admin Panel</h3>
  <!-- FIXED: Menambahkan CSRF token untuk mencegah CSRF attack -->
  <form method="post">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
    <label>Delete user:
      <select name="delete_user" required>
        <?php
        $users = $GLOBALS['PDO']->query("SELECT username FROM users");
        foreach ($users as $u) {
            if ($u['username'] !== $profile->username) {
                echo "<option value='{$u['username']}'>{$u['username']}</option>";
            }
        }
        ?>
      </select>
    </label>
    <button type="submit">Delete</button>
  </form>
  <?php if (!empty($msg)) echo $msg; ?>
<?php else: ?>
  <p style="color:red">You are a regular user. You do not have admin panel access.</p>
<?php endif; ?>

<?php include '_footer.php'; ?>