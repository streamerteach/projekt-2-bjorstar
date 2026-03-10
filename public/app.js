const API_BASE = '';

const authMessage = document.getElementById('auth-message');
const profilesList = document.getElementById('profiles-list');

document.getElementById('register-form').addEventListener('submit', async (e) => {
  e.preventDefault();
  const username = document.getElementById('reg-username').value.trim();
  const password = document.getElementById('reg-password').value;
  const age = document.getElementById('reg-age').value;
  const bio = document.getElementById('reg-bio').value.trim();
  const interestsRaw = document.getElementById('reg-interests').value.trim();

  const interests = interestsRaw
    ? interestsRaw.split(',').map(s => s.trim()).filter(Boolean)
    : [];

  try {
    const res = await fetch(`${API_BASE}/api/register`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ username, password, age, bio, interests })
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data.error || 'Registration failed');
    authMessage.textContent = `Registered as ${data.user.username}`;
  } catch (err) {
    authMessage.textContent = err.message;
  }
});

document.getElementById('login-form').addEventListener('submit', async (e) => {
  e.preventDefault();
  const username = document.getElementById('login-username').value.trim();
  const password = document.getElementById('login-password').value;

  try {
    const res = await fetch(`${API_BASE}/api/login`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ username, password })
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data.error || 'Login failed');
    authMessage.textContent = `Logged in as ${data.user.username}`;
  } catch (err) {
    authMessage.textContent = err.message;
  }
});

document.getElementById('load-profiles').addEventListener('click', async () => {
  await loadProfiles();
});

document.getElementById('search-button').addEventListener('click', async () => {
  const interest = document.getElementById('search-interest').value.trim();
  if (!interest) {
    profilesList.innerHTML = '<li>Please enter an interest.</li>';
    return;
  }
  await loadProfiles(interest);
});

async function loadProfiles(interest) {
  profilesList.innerHTML = '<li>Loading...</li>';
  try {
    const url = interest
      ? `${API_BASE}/api/profiles/search?interest=${encodeURIComponent(interest)}`
      : `${API_BASE}/api/profiles`;

    const res = await fetch(url);
    const data = await res.json();

    if (!Array.isArray(data) || data.length === 0) {
      profilesList.innerHTML = '<li>No profiles found.</li>';
      return;
    }

    profilesList.innerHTML = '';
    data.forEach(p => {
      const li = document.createElement('li');
      li.className = 'profile-card';
      li.innerHTML = `
        <strong>${p.username}</strong> ${p.age ? `(${p.age})` : ''}<br/>
        ${p.bio || 'No bio'}<br/>
        <em>Interests:</em> ${(p.interests || []).join(', ') || 'None'}
      `;
      profilesList.appendChild(li);
    });
  } catch (err) {
    profilesList.innerHTML = `<li>Error: ${err.message}</li>`;
  }
}
