import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Simple theme toggle: updates icon visibility and persists choice
(function () {
	function updateIcons() {
		const darkIcon = document.getElementById('theme-toggle-dark-icon');
		const lightIcon = document.getElementById('theme-toggle-light-icon');
		if (!darkIcon || !lightIcon) return;
		if (document.documentElement.classList.contains('dark')) {
			darkIcon.classList.remove('hidden');
			lightIcon.classList.add('hidden');
		} else {
			darkIcon.classList.add('hidden');
			lightIcon.classList.remove('hidden');
		}
	}

	document.addEventListener('DOMContentLoaded', function () {
		updateIcons();

		const btn = document.getElementById('theme-toggle');
		if (!btn) return;

		btn.addEventListener('click', function () {
			const isDark = document.documentElement.classList.toggle('dark');
			try { localStorage.setItem('theme', isDark ? 'dark' : 'light'); } catch (e) {}
			updateIcons();
		});
	});
})();
