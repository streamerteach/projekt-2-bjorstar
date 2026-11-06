<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';
requireLogin();

$message = '';
$editing = isset($_GET['edit']);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $age = (int)($_POST['age'] ?? 0);
    $bio = trim($_POST['bio'] ?? '');
    $interests = trim($_POST['interests'] ?? '');

    $stmt = $conn->prepare("UPDATE users SET age = ?, bio = ?, interests = ? WHERE id = ?");
    $stmt->execute([$age ?: null, $bio, $interests, $_SESSION['user_id']]);
    $message = "Profile updated!";
    $editing = false;
}

// Get current user info
$stmt = $conn->prepare("SELECT username, age, bio, interests FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>
<div class="container">
  <?php include 'includes/navbar.php'; ?>
  <section class="card">
    <h2>My Account</h2>

    <?php if ($message): ?>
      <p id="auth-message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <?php if ($editing): ?>
      <!-- Edit mode -->
      <form method="post" id="profile-form">
        <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?> (cannot be changed)</p>

        <label>Age</label>
        <input type="number" name="age" value="<?= (int)$user['age'] ?>" min="18" placeholder="Age">

        <label>Bio</label>
        <input type="text" name="bio" value="<?= htmlspecialchars($user['bio'] ?? '') ?>" placeholder="Short bio">

        <label>Interests</label>
        <div id="interest-tags" style="display:flex; flex-wrap:wrap; gap:6px; margin-bottom:8px;"></div>
        <input type="text" id="interest-input" placeholder="Type an interest and press space" autocomplete="off">
        <!-- Hidden field that actually gets submitted -->
        <input type="hidden" name="interests" id="interests-hidden" value="<?= htmlspecialchars($user['interests'] ?? '') ?>">

        <button type="submit">Save changes</button>
        <a href="myaccount.php"><button type="button" class="secondary">Cancel</button></a>
      </form>

      <script>
        const tagContainer = document.getElementById('interest-tags');
        const input = document.getElementById('interest-input');
        const hidden = document.getElementById('interests-hidden');

        // Load existing interests into bubbles
        let tags = hidden.value ? hidden.value.split(',').map(t => t.trim()).filter(Boolean) : [];

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

        // Remove tag when clicking ×
        tagContainer.addEventListener('click', (e) => {
          if (e.target.dataset.index !== undefined) {
            tags.splice(e.target.dataset.index, 1);
            renderTags();
          }
        });

        // Add tag on space
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

    <?php else: ?>
      <!-- View mode -->
      <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
      <p><strong>Age:</strong> <?= (int)$user['age'] ?></p>
      <p><strong>Bio:</strong> <?= htmlspecialchars($user['bio'] ?? '—') ?></p>
      <p><strong>Interests:</strong> <?= htmlspecialchars($user['interests'] ?? '—') ?></p>

      <a href="myaccount.php?edit=1"><button>Edit profile</button></a>
    <?php endif; ?>
  </section>
</div>
<?php include 'includes/footer.php'; ?>