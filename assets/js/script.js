// Optional small enhancements
document.addEventListener('DOMContentLoaded', () => {
  // Example: confirm before liking
  document.querySelectorAll('.profile-actions button').forEach(btn => {
    if (btn.textContent.trim() === 'Like') {
      btn.addEventListener('click', (e) => {
        // already has an alert in the HTML, so this is optional
      });
    }
  });
});


function toggleChat() {
  const frame = document.querySelector('iframe[name="chatframe"]');
  const btn = document.getElementById('chat-toggle');
  if (!frame || !btn) return;

  const isCollapsed = frame.style.height === '0px' || frame.style.display === 'none' || !frame.style.height;

  if (isCollapsed) {
    // Show chat
    frame.style.display = 'block';
    frame.style.height = window.innerWidth <= 700 ? '300px' : '420px';
    frame.style.width = window.innerWidth <= 700 ? '240px' : '320px';
    btn.textContent = 'CHAT';
  } else {
    // Hide chat
    frame.style.height = '0px';
    frame.style.display = 'none';
    btn.textContent = 'CHAT';
  }
}

// Start collapsed on mobile
document.addEventListener('DOMContentLoaded', () => {
  if (window.innerWidth <= 700) {
    const frame = document.querySelector('iframe[name="chatframe"]');
    if (frame) {
      frame.style.display = 'none';
      frame.style.height = '0px';
    }
  }
});