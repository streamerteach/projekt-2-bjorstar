<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';
requireLogin();

// Get profiles the user has liked
$stmt = $conn->prepare("
    SELECT u.username, u.age, u.bio, u.interests
    FROM likes l
    JOIN users u ON l.liked_user_id = u.id
    WHERE l.user_id = ?
    ORDER BY l.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$liked = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>
<div class="container">
    <?php include 'includes/navbar.php'; ?>
    <section class="card">
        <h2>Profiles You Liked</h2>
        <ul id="profiles-list">
            <?php if (empty($liked)): ?>
                <li style="color:var(--muted);padding:12px">You haven't liked anyone yet.</li>
            <?php else: ?>
                <?php foreach ($liked as $p): ?>
                    <?php $interests = $p['interests'] ? array_map('trim', explode(',', $p['interests'])) : []; ?>
                    <li class="profile">
                        <div class="avatar"><?= strtoupper(substr($p['username'], 0, 1)) ?></div>
                        <div class="meta">
                            <h4><?= htmlspecialchars($p['username']) ?>, <span style="font-weight:600;color:var(--muted)"><?= (int)$p['age'] ?></span></h4>
                            <p><?= htmlspecialchars($p['bio'] ?? '') ?></p>
                            <div class="tags">
                                <?php foreach ($interests as $i): ?>
                                    <span class="tag"><?= htmlspecialchars($i) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="profile-actions">
                            <!-- Unlike button -->
                            <form method="post" action="unlike.php" style="display:inline;">
                                <input type="hidden" name="liked_username" value="<?= htmlspecialchars($p['username']) ?>">
                                <button type="submit" class="btn-ghost">Unlike</button>
                            </form>
                            <a href="chat.php?user=<?= urlencode($p['username']) ?>" target="chatframe">
                                <button type="button" class="btn-ghost">Message</button>
                            </a>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </section>
<?php if (isset($_SESSION['username'])): ?>
  <div style="position:fixed; right:20px; bottom:20px; z-index:1000;">
    <button id="chat-toggle" 
            style="display:block; margin-left:auto; margin-bottom:8px;
                   padding:8px 14px; border-radius:20px; 
                   background:#ff6b81; color:white; border:none; 
                   font-weight:600; font-size:13px; cursor:pointer;
                   box-shadow:0 4px 12px rgba(255,107,129,0.3);">
      CHAT
    </button>
    <iframe name="chatframe" id="chatframe" src="chat.php" 
            style="width:320px; height:420px; border:none; border-radius:12px; 
                   box-shadow:0 8px 30px rgba(0,0,0,0.4); background:#0b1220; 
                   transition: all 0.25s ease;">
    </iframe>
  </div>

  <script>
    document.getElementById('chat-toggle').addEventListener('click', function () {
      const frame = document.getElementById('chatframe');
      const isHidden = frame.style.display === 'none' || frame.style.height === '0px';

      if (isHidden) {
        // Show
        frame.style.display = 'block';
        frame.style.height = window.innerWidth <= 700 ? '300px' : '420px';
        frame.style.width = window.innerWidth <= 700 ? '240px' : '320px';
      } else {
        // Hide
        frame.style.display = 'none';
        frame.style.height = '0px';
      }
    });

    // Start hidden on mobile
    if (window.innerWidth <= 700) {
      const frame = document.getElementById('chatframe');
      if (frame) {
        frame.style.display = 'none';
        frame.style.height = '0px';
      }
    }
  </script>
<?php endif; ?>
    <?php include 'includes/footer.php'; ?>