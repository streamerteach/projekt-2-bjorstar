<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $age = (int)($_POST['age'] ?? 0);
    $bio = trim($_POST['bio'] ?? '');
    $interests = trim($_POST['interests'] ?? '');

    if ($username && $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password, age, bio, interests) VALUES (?, ?, ?, ?, ?)");
        try {
            $stmt->execute([$username, $hash, $age ?: null, $bio, $interests]);
            $message = "Account created! You can now log in.";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $message = "Username already taken.";
            } else {
                $message = "Error: " . $e->getMessage();
            }
        }
    } else {
        $message = "Username and password are required.";
    }
}

include 'includes/header.php';
?>
<div class="container">
  <?php include 'includes/navbar.php'; ?>
  <section class="card">
    <h2>Register</h2>
    <form method="post" id="register-form">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="number" name="age" placeholder="Age" min="18">
      <input type="text" name="bio" placeholder="Short bio">

      <label>Interests</label>
      <div id="interest-tags" style="display:flex; flex-wrap:wrap; gap:6px; margin-bottom:8px;"></div>
      <input type="text" id="interest-input" placeholder="Type an interest and press space" autocomplete="off">
      <input type="hidden" name="interests" id="interests-hidden">

      <button type="submit">Create account</button>
    </form>
    <p id="auth-message"><?= htmlspecialchars($message) ?></p>
    <p>Already have an account? <a href="login.php">Login</a></p>
  </section>
</div>

<script>
  const tagContainer = document.getElementById('interest-tags');
  const input = document.getElementById('interest-input');
  const hidden = document.getElementById('interests-hidden');
  let tags = [];

  function renderTags() {
    tagContainer.innerHTML = '';
    tags.forEach((tag, index) => {
      const span = document.createElement('span');
      span.className = 'tag';
      span.style.cursor = 'default';
      span.innerHTML = `${tag} <span style="margin-left:6px; cursor:pointer; color:#ff6b81;" data-index="${index}">×</span>`;
      tagContainer.appendChild(span);
    });
    hidden.value = tags.join(', ');
  }

  tagContainer.addEventListener('click', (e) => {
    if (e.target.dataset.index !== undefined) {
      tags.splice(e.target.dataset.index, 1);
      renderTags();
    }
  });

  input.addEventListener('keydown', (e) => {
    if (e.key === ' ' || e.key === 'Enter') {
      e.preventDefault();
      const value = input.value.trim();
      if (value && !tags.includes(value)) {
        tags.push(value);
        renderTags();
      }
      input.value = '';
    }
  });

  renderTags();
</script>

<?php include 'includes/footer.php'; ?>