<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Get all users
$stmt = $conn->query("SELECT username, age, bio, interests FROM users ORDER BY id DESC");
$profiles = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>
<div class="container">
    <?php include 'includes/navbar.php'; ?>

    <?php if (isset($_SESSION['username'])): ?>
        <!-- Show profiles only if logged in -->
        <section id="search" class="card">
            <h2>Browse Profiles</h2>
            <div class="controls">
                <a href="search.php"><button class="btn-ghost">Advanced Search</button></a>
            </div>

            <ul id="profiles-list">
                <?php if (empty($profiles)): ?>
                    <li style="color:var(--muted);padding:12px">No profiles yet.</li>
                <?php else: ?>
                    <?php foreach ($profiles as $p): ?>
                        <?php
                        $interests = $p['interests'] ? array_map('trim', explode(',', $p['interests'])) : [];

                        // Check if already liked
                        $checkLike = $conn->prepare("SELECT id FROM likes WHERE user_id = ? AND liked_user_id = (SELECT id FROM users WHERE username = ?)");
                        $checkLike->execute([$_SESSION['user_id'], $p['username']]);
                        $alreadyLiked = $checkLike->fetch();
                        ?>
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
                                <!-- Show Like or Unlike -->
                                <?php if ($alreadyLiked): ?>
                                    <form method="post" action="unlike.php" style="display:inline;">
                                        <input type="hidden" name="liked_username" value="<?= htmlspecialchars($p['username']) ?>">
                                        <button type="submit" class="btn-ghost">Unlike</button>
                                    </form>
                                <?php else: ?>
                                    <form method="post" action="like.php" style="display:inline;">
                                        <input type="hidden" name="liked_username" value="<?= htmlspecialchars($p['username']) ?>">
                                        <button type="submit" class="btn-ghost">Like</button>
                                    </form>
                                <?php endif; ?>


                                <a href="chat.php?user=<?= urlencode($p['username']) ?>" target="chatframe">
                                    <button type="button" class="btn-ghost">Message</button>
                                </a>

                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </section>
    <?php else: ?>
        <!-- Guest message -->
        <section class="card">
            <h2>Welcome</h2>
            <p>Please <a href="login.php">log in</a> or <a href="register.php">register</a> to browse profiles.</p>
        </section>
    <?php endif; ?>
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