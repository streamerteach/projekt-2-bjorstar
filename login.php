<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$message = '';

// Handle login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify password
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit;
    } else {
        $message = "Invalid username or password.";
    }
}

include 'includes/header.php';
?>
<div class="container">
  <?php include 'includes/navbar.php'; ?>
  <section class="card">
    <h2>Login</h2>
    <form method="post">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
    <p id="auth-message"><?= htmlspecialchars($message) ?></p>
    <p>No account? <a href="register.php">Register</a></p>
  </section>
</div>
<?php include 'includes/footer.php'; ?>