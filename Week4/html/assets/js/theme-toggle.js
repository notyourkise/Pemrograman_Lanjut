// Week 4 — Theme Toggle Script
// Fitur: toggle dark/light dengan menyetel class .dark pada <html>
// Menyimpan preferensi di localStorage agar persist.

(function() {
  const STORAGE_KEY = 'pref-theme';
  const root = document.documentElement; // <html>
  const btn = document.getElementById('themeToggle');
  const icon = document.getElementById('themeIcon');
  const label = document.getElementById('themeLabel');

  function applyTheme(theme) {
    if(theme === 'dark') {
      root.classList.add('dark');
      icon.className = 'fa-solid fa-moon';
      label.textContent = 'Dark';
    } else {
      root.classList.remove('dark');
      icon.className = 'fa-solid fa-sun';
      label.textContent = 'Light';
    }
  }

  function toggleTheme() {
    const isDark = root.classList.contains('dark');
    const next = isDark ? 'light' : 'dark';
    localStorage.setItem(STORAGE_KEY, next);
    applyTheme(next);
  }

  // Initial load: use saved or system preference
  const saved = localStorage.getItem(STORAGE_KEY);
  if(saved) {
    applyTheme(saved);
  } else {
    // Optional: system prefers dark?
    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    applyTheme(prefersDark ? 'dark' : 'light');
  }

  if(btn) {
    btn.addEventListener('click', toggleTheme);
  }
})();
