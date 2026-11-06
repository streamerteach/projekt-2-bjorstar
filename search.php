<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$results = [];
$search = trim($_GET['interest'] ?? '');

// Search by interest if something was typed
if ($search !== '') {
    $stmt = $conn->prepare("SELECT username, age, bio, interests FROM users WHERE interests LIKE ?");
    $stmt->execute(["%$search%"]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

include 'includes/header.php';
?>
<div class="container">
  <?php include 'includes/navbar.php'; ?>
  <section class="card">
    <h2>Search by Interest</h2>
    <form method="get">
      <div class="controls">
        <input type="text" name="interest" placeholder="e.g. hiking" value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
      </div>
    </form>

    <ul id="profiles-list">
      <?php if ($search === ''): ?>
        <li style="color:var(--muted);padding:12px">Enter an interest to search.</li>
      <?php elseif (empty($results)): ?>
        <li style="color:var(--muted);padding:12px">No profiles found.</li>
      <?php else: ?>
        <?php foreach ($results as $p): ?>
          <?php $interests = $p['interests'] ? array_map('trim', explode(',', $p['interests'])) : []; ?>
          <li class="profile">
            <div class="avatar"><?= strtoupper(substr($p['username'], 0, 1)) ?></div>
            <div class="meta">
              <h4><?= htmlspecialchars($p['username']) ?>, <?= (int)$p['age'] ?></h4>
              <p><?= htmlspecialchars($p['bio'] ?? '') ?></p>
              <div class="tags">
                <?php foreach ($interests as $i): ?>
                  <span class="tag"><?= htmlspecialchars($i) ?></span>
                <?php endforeach; ?>
              </div>
            </div>
          </li>
        <?php endforeach; ?>
      <?php endif; ?>
    </ul>
  </section>
</div>
<?php include 'includes/footer.php'; ?>