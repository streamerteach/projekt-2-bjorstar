<header>
<div class="brand">
  <a href="index.php" style="text-decoration:none; color:inherit;">
    <div class="logo">MD</div>
  </a>
  <div>
    <h1>Mini Dating Site</h1>
    <p class="lead">Find people who share your interests</p>
  </div>
</div>
  <div style="display:flex;gap:10px;align-items:center">
    <?php if (isset($_SESSION['username'])): ?>
        <a href="likes.php"><button class="secondary">Likes</button></a>
      <div style="font-size:13px;color:var(--muted)">Welcome, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></div>
      <a href="myaccount.php"><button class="secondary">My Account</button></a>
      <a href="logout.php"><button class="secondary">Logout</button></a>
    <?php else: ?>
      <div style="font-size:13px;color:var(--muted)">Welcome, <strong>Guest</strong></div>
      <a href="register.php"><button class="secondary">Sign up</button></a>
      <a href="login.php"><button class="secondary">Login</button></a>
    <?php endif; ?>
  </div>
</header>
