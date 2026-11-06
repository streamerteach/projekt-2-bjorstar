<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';
requireLogin();

$otherUsername = $_GET['user'] ?? '';
$otherUser = null;
$messages = [];

// Get list of people the current user has chatted with
$chatsStmt = $conn->prepare("
    SELECT DISTINCT u.username
    FROM messages m
    JOIN users u ON (u.id = m.sender_id OR u.id = m.receiver_id)
    WHERE (m.sender_id = ? OR m.receiver_id = ?)
      AND u.id != ?
    ORDER BY u.username
");
$chatsStmt->execute([$_SESSION['user_id'], $_SESSION['user_id'], $_SESSION['user_id']]);
$existingChats = $chatsStmt->fetchAll(PDO::FETCH_COLUMN);

if ($otherUsername) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$otherUsername]);
    $otherUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($otherUser) {
        $stmt = $conn->prepare("
            SELECT m.message, m.created_at, u.username AS sender
            FROM messages m
            JOIN users u ON m.sender_id = u.id
            WHERE (m.sender_id = ? AND m.receiver_id = ?)
               OR (m.sender_id = ? AND m.receiver_id = ?)
            ORDER BY m.created_at ASC
        ");
        $stmt->execute([
            $_SESSION['user_id'], $otherUser['id'],
            $otherUser['id'], $_SESSION['user_id']
        ]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($messages)) {
            $messages[] = [
                'sender' => 'System',
                'message' => 'The chats are not moderated nor encrypted so DO NOT share personal info or your password lol',
                'created_at' => date('Y-m-d H:i:s')
            ];
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $otherUser && !empty($_POST['message'])) {
    $text = trim($_POST['message']);
    if ($text !== '') {
        $insert = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $insert->execute([$_SESSION['user_id'], $otherUser['id'], $text]);
        header("Location: chat.php?user=" . urlencode($otherUsername));
        exit;
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    body { margin: 0; padding: 0; background: #0b1220; }
    .chat-switcher { display: flex; gap: 6px; overflow-x: auto; margin-bottom: 10px; padding-bottom: 4px; }
    .chat-switcher a { 
      color: var(--muted); 
      text-decoration: none; 
      font-size: 13px; 
      padding: 4px 8px; 
      border-radius: 8px; 
      background: rgba(255,255,255,0.05);
      white-space: nowrap;
    }
    .chat-switcher a.active { background: rgba(255,107,129,0.2); color: white; }
  </style>
</head>
<body>
<div class="chat-panel">
  <!-- Chat switcher -->
  <?php if (!empty($existingChats)): ?>
    <div class="chat-switcher">
      <?php foreach ($existingChats as $chatUser): ?>
        <a href="chat.php?user=<?= urlencode($chatUser) ?>" 
           class="<?= $chatUser === $otherUsername ? 'active' : '' ?>">
          <?= htmlspecialchars($chatUser) ?>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <h3 style="margin:0 0 10px 0; font-size:16px;">
    <?= $otherUsername ? 'Chat with ' . htmlspecialchars($otherUsername) : 'Select a chat' ?>
  </h3>

  <div class="chat-messages">
    <?php if ($otherUsername && $otherUser): ?>
      <?php foreach ($messages as $msg): ?>
        <div class="chat-msg <?= $msg['sender'] === $_SESSION['username'] ? 'mine' : '' ?>">
          <strong><?= htmlspecialchars($msg['sender']) ?>:</strong>
          <?= htmlspecialchars($msg['message']) ?>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p style="color:var(--muted); font-size:14px;">No chat selected.</p>
    <?php endif; ?>
  </div>

  <?php if ($otherUser): ?>
    <form method="post">
      <input type="text" name="message" placeholder="Type a message..." required>
      <button type="submit">Send</button>
    </form>
  <?php endif; ?>
</div>
</body>
</html>