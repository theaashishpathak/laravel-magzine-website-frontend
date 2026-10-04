import './tiptap-editor.js';

const themeStorageKey = 'crm-theme';
const sidebarStorageKey = 'crm-sidebar-collapsed';

const isDesktop = () => window.innerWidth >= 1024;

const setTheme = (theme) => {
	const root = document.documentElement;
	const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
	const shouldUseDark = theme === 'dark' || (theme === 'system' && prefersDark);

	root.classList.toggle('dark', shouldUseDark);
	localStorage.setItem(themeStorageKey, theme);
	document.cookie = `crm_theme=${shouldUseDark ? 'dark' : 'light'}; path=/; max-age=31536000; SameSite=Lax`;

	document.querySelectorAll('[data-theme-choice]').forEach((radio) => {
		radio.checked = radio.value === theme;
	});
};

const showToast = (message, type = 'success') => {
	if (!message || !window.toastr) {
		return;
	}

 const normalizedType = type === 'danger' ? 'error' : type;

	toastr.options = {
		closeButton: true,
		progressBar: true,
		positionClass: 'toast-top-right',
		timeOut: 2500,
	};

			 if (typeof toastr[normalizedType] === 'function') {
			  toastr[normalizedType](message);
	} else {
		toastr.success(message);
	}
};

const openModal = (id) => {
	const modal = document.querySelector(`[data-modal="${id}"]`);

	if (!modal) {
		return;
	}

	modal.classList.remove('hidden');
	modal.classList.add('flex');

	const panel = modal.querySelector('[data-modal-panel]');
	if (panel) {
		panel.classList.remove('translate-x-full');
	}
};

const closeModal = (id) => {
	const modal = document.querySelector(`[data-modal="${id}"]`);

	if (!modal) {
		return;
	}

	const panel = modal.querySelector('[data-modal-panel]');
	if (panel) {
		panel.classList.add('translate-x-full');
	}

	modal.classList.add('hidden');
	modal.classList.remove('flex');
};

let dropdownHoverTimer = null;
let sidebarHoverTimer = null;

const closeAllDropdowns = () => {
	clearTimeout(dropdownHoverTimer);
	document.querySelectorAll('[data-dropdown]').forEach((dropdown) => {
		dropdown.classList.add('hidden');
	});
	document.querySelectorAll('[data-dropdown-toggle]').forEach((toggle) => {
		toggle.setAttribute('aria-expanded', 'false');
	});
};

const openDropdown = (id) => {
	clearTimeout(dropdownHoverTimer);
	const dropdown = document.querySelector(`[data-dropdown="${id}"]`);
	if (!dropdown) return;

	closeAllDropdowns();
	dropdown.classList.remove('hidden');
	const toggle = document.querySelector(`[data-dropdown-toggle="${id}"]`);
	if (toggle) {
		toggle.setAttribute('aria-expanded', 'true');
	}
};

const closeDropdown = (id) => {
	const dropdown = document.querySelector(`[data-dropdown="${id}"]`);
	if (!dropdown) return;

	dropdown.classList.add('hidden');
	const toggle = document.querySelector(`[data-dropdown-toggle="${id}"]`);
	if (toggle) {
		toggle.setAttribute('aria-expanded', 'false');
	}
};

const toggleDropdown = (id) => {
	const dropdown = document.querySelector(`[data-dropdown="${id}"]`);
	if (!dropdown) return;

	if (dropdown.classList.contains('hidden')) {
		openDropdown(id);
	} else {
		closeDropdown(id);
	}
};

const closeSidebarSubmenus = (exceptId = '') => {
	document.querySelectorAll('[data-sidebar-submenu]').forEach((submenu) => {
		if (submenu.dataset.sidebarSubmenu === exceptId) {
			return;
		}

		submenu.classList.add('hidden');
		delete submenu.dataset.pinned;
		const toggle = document.querySelector(`[data-sidebar-menu-toggle="${submenu.dataset.sidebarSubmenu}"]`);
		if (toggle) {
			toggle.setAttribute('aria-expanded', 'false');
		}
	});
};

const openSidebarSubmenu = (id) => {
	clearTimeout(sidebarHoverTimer);
	const submenu = document.querySelector(`[data-sidebar-submenu="${id}"]`);
	if (!submenu) return;

	closeSidebarSubmenus(id);
	submenu.classList.remove('hidden');
	const toggle = document.querySelector(`[data-sidebar-menu-toggle="${id}"]`);
	if (toggle) {
		toggle.setAttribute('aria-expanded', 'true');
	}
};

const toggleSidebarSubmenu = (toggle) => {
	const id = toggle.dataset.sidebarMenuToggle || '';
	const submenu = document.querySelector(`[data-sidebar-submenu="${id}"]`);

	if (!submenu) {
		return;
	}

	const willOpen = submenu.classList.contains('hidden');
	closeSidebarSubmenus(id);
	submenu.classList.toggle('hidden', !willOpen);
	toggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
	if (willOpen) {
		submenu.dataset.pinned = 'true';
	} else {
		delete submenu.dataset.pinned;
	}
	toggle.blur();
};

const initSidebarMenus = () => {
	document.querySelectorAll('[data-sidebar-menu-toggle]').forEach((toggle) => {
		const id = toggle.dataset.sidebarMenuToggle || '';
		const submenu = document.querySelector(`[data-sidebar-submenu="${id}"]`);

		if (!submenu) {
			return;
		}

		toggle.setAttribute('aria-controls', id);
		toggle.setAttribute('aria-expanded', submenu.classList.contains('hidden') ? 'false' : 'true');
	});
};

const updateSidebarLabels = (collapsed) => {
	document.querySelectorAll('[data-sidebar-label]').forEach((label) => {
		label.classList.toggle('hidden', collapsed);
	});
};

const applySidebarState = (collapsed) => {
	const sidebar = document.querySelector('[data-sidebar]');

	if (!sidebar) {
		return;
	}

	sidebar.classList.toggle('w-60', !collapsed);
	sidebar.classList.toggle('w-16', collapsed);
	sidebar.classList.toggle('lg:w-60', !collapsed);
	sidebar.classList.toggle('lg:w-16', collapsed);
	updateSidebarLabels(collapsed);
	localStorage.setItem(sidebarStorageKey, collapsed ? '1' : '0');
};

const toggleSidebar = () => {
	const sidebar = document.querySelector('[data-sidebar]');
	const backdrop = document.querySelector('[data-sidebar-backdrop]');

	if (!sidebar) {
		return;
	}

	if (isDesktop()) {
		const collapsed = sidebar.classList.contains('w-60');
		applySidebarState(collapsed);
		return;
	}

	const isHidden = sidebar.classList.contains('-translate-x-full');
	sidebar.classList.toggle('-translate-x-full', !isHidden);
	sidebar.classList.toggle('translate-x-0', isHidden);

	if (backdrop) {
		backdrop.classList.toggle('hidden', !isHidden);
	}
};

const closeSidebar = () => {
	const sidebar = document.querySelector('[data-sidebar]');
	const backdrop = document.querySelector('[data-sidebar-backdrop]');

	if (sidebar && !isDesktop()) {
		sidebar.classList.add('-translate-x-full');
		sidebar.classList.remove('translate-x-0');
	}

	if (backdrop) {
		backdrop.classList.add('hidden');
	}
};

const initCharts = () => {
	if (!window.Chart) {
		return;
	}

	document.querySelectorAll('[data-chart-canvas]').forEach((canvas) => {
		if (canvas.dataset.chartInitialized === '1') {
			return;
		}

		const chartConfig = canvas.dataset.chartConfig;
		if (!chartConfig) {
			return;
		}

		try {
			const parsedConfig = JSON.parse(chartConfig);
			new Chart(canvas, parsedConfig);
			canvas.dataset.chartInitialized = '1';
		} catch (error) {
			console.error('Failed to parse chart config', error);
		}
	});

	const dashboardData = window.crmDashboardData;
	const reportsData = window.crmReportsData;

	const revenueCanvas = document.getElementById('dashboardRevenueChart');
	if (revenueCanvas && dashboardData) {
		new Chart(revenueCanvas, {
			type: 'line',
			data: {
				labels: dashboardData.labels,
				datasets: [
					{
						label: 'Revenue',
						data: dashboardData.revenue,
						borderColor: '#4f46e5',
						backgroundColor: 'rgba(79, 70, 229, 0.12)',
						tension: 0.4,
						fill: true,
					},
				],
			},
			options: {
				responsive: true,
				plugins: {
					legend: {
						display: false,
					},
				},
				scales: {
					y: {
						beginAtZero: true,
					},
				},
			},
		});
	}

	const leadsCanvas = document.getElementById('dashboardLeadsChart');
	if (leadsCanvas && dashboardData) {
		new Chart(leadsCanvas, {
			type: 'bar',
			data: {
				labels: dashboardData.labels,
				datasets: [
					{
						label: 'Leads',
						data: dashboardData.leads,
						backgroundColor: '#6366f1',
					},
				],
			},
			options: {
				responsive: true,
				plugins: {
					legend: {
						display: false,
					},
				},
				scales: {
					y: {
						beginAtZero: true,
					},
				},
			},
		});
	}

	const reportsCanvas = document.getElementById('reportsRevenueChart');
	if (reportsCanvas && reportsData) {
		new Chart(reportsCanvas, {
			type: 'bar',
			data: {
				labels: reportsData.labels,
				datasets: [
					{
						label: 'Revenue',
						data: reportsData.values,
						backgroundColor: '#4f46e5',
					},
				],
			},
			options: {
				responsive: true,
				plugins: {
					legend: {
						display: false,
					},
				},
			},
		});
	}
};

const initPickers = () => {
	if (!window.flatpickr) {
		return;
	}

	document.querySelectorAll('[data-date-range]').forEach((element) => {
		flatpickr(element, {
			mode: 'range',
			dateFormat: 'Y-m-d',
		});
	});

	document.querySelectorAll('[data-flatpickr-date]').forEach((element) => {
		flatpickr(element, {
			dateFormat: 'Y-m-d',
		});
	});

	document.querySelectorAll('[data-flatpickr-datetime]').forEach((element) => {
		flatpickr(element, {
			enableTime: true,
			dateFormat: 'Y-m-d H:i',
			time_24hr: true,
		});
	});

	document.querySelectorAll('[data-flatpickr-time]').forEach((element) => {
		flatpickr(element, {
			enableTime: true,
			noCalendar: true,
			dateFormat: 'H:i',
			time_24hr: true,
		});
	});

	document.querySelectorAll('[data-flatpickr-time-range]').forEach((element) => {
		flatpickr(element, {
			enableTime: true,
			noCalendar: true,
			mode: 'range',
			dateFormat: 'H:i',
			time_24hr: true,
		});
	});

	document.querySelectorAll('[data-flatpickr-date-range]').forEach((element) => {
		flatpickr(element, {
			mode: 'range',
			dateFormat: 'Y-m-d',
		});
	});
};

const initSelect2 = () => {
	if (!window.jQuery || !window.jQuery.fn || !window.jQuery.fn.select2) {
		return;
	}

	window.jQuery('.js-select2').select2({
		width: '100%',
	});
};

const initSummernote = () => {
	if (!window.jQuery || !window.jQuery.fn || !window.jQuery.fn.summernote) {
		return;
	}

	window.jQuery('.summernote').summernote({
		height: 220,
	});
};

const initServiceTabs = () => {
	const buttons = document.querySelectorAll('[data-service-tab-button]');
	const panels = document.querySelectorAll('[data-service-tab-panel]');

	if (!buttons.length || !panels.length) {
		return;
	}

	const activateTab = (tabKey) => {
		buttons.forEach((button) => {
			const active = button.dataset.serviceTabButton === tabKey;
			button.classList.toggle('bg-indigo-600', active);
			button.classList.toggle('text-white', active);
			button.classList.toggle('bg-slate-100', !active);
			button.classList.toggle('text-slate-600', !active);
			button.classList.toggle('dark:bg-slate-800', !active);
			button.classList.toggle('dark:text-slate-300', !active);
		});

		panels.forEach((panel) => {
			panel.classList.toggle('hidden', panel.dataset.serviceTabPanel !== tabKey);
		});
	};

	buttons.forEach((button) => {
		button.addEventListener('click', () => activateTab(button.dataset.serviceTabButton || 'overview'));
	});

	activateTab('overview');
};

const initQuickCreate = () => {
	const selector = document.querySelector('[data-quick-create-resource]');
	const form = document.querySelector('[data-quick-create-form]');

	if (!selector || !form) {
		return;
	}

	const contactAction = form.dataset.contactAction;
	const leadAction = form.dataset.leadAction;

	const updateAction = () => {
		form.action = selector.value === 'leads' ? (leadAction || form.action) : (contactAction || form.action);
	};

	selector.addEventListener('change', updateAction);
	updateAction();
};

const initThemeControls = () => {
	const getCookie = (name) => {
		const match = document.cookie.match(new RegExp('(?:^|;\\s*)' + name + '=([^;]+)'));
		return match ? decodeURIComponent(match[1]) : null;
	};
	const savedTheme = localStorage.getItem(themeStorageKey) || getCookie('crm_theme') || 'light';
	setTheme(savedTheme);

	document.querySelectorAll('[data-theme-choice]').forEach((radio) => {
		radio.addEventListener('change', () => setTheme(radio.value));
	});
};

const initFlashToast = () => {
 document.querySelectorAll('[data-toast-message]').forEach((toastMessage) => {
  if (!toastMessage?.dataset.toastMessage) {
   return;
  }

  showToast(toastMessage.dataset.toastMessage, toastMessage.dataset.toastType || 'success');
 });
};

const detectAlertNature = (text, element) => {
	if (element && element.dataset && element.dataset.confirmNature) {
		return element.dataset.confirmNature;
	}
	const lower = (text || '').toLowerCase();
	if (lower.includes('delete') || lower.includes('trash') || lower.includes('remove') || lower.includes('destroy') || lower.includes('wipe') || lower.includes('purge') || lower.includes('permanently')) {
		return 'danger';
	}
	if (lower.includes('archive') || lower.includes('reset') || lower.includes('clear') || lower.includes('warning') || lower.includes('revert') || lower.includes('unsubscribe') || lower.includes('merge')) {
		return 'warning';
	}
	if (lower.includes('promote') || lower.includes('publish') || lower.includes('restore') || lower.includes('toggle') || lower.includes('info')) {
		return 'info';
	}
	if (lower.includes('success') || lower.includes('approve') || lower.includes('accept')) {
		return 'success';
	}
	return 'danger';
};

const getDefaultConfirmTitle = (nature, text) => {
	const lower = (text || '').toLowerCase();
	if (lower.includes('post')) return nature === 'danger' ? 'Delete Post' : 'Post Action';
	if (lower.includes('page')) return nature === 'danger' ? 'Delete Page' : 'Page Action';
	if (lower.includes('tag')) return nature === 'danger' ? 'Delete Tag' : 'Tag Action';
	if (lower.includes('comment')) return nature === 'danger' ? 'Delete Comment' : 'Comment Action';
	if (lower.includes('media') || lower.includes('file')) return nature === 'danger' ? 'Delete Media' : 'Media Action';
	if (lower.includes('role')) return nature === 'danger' ? 'Delete Role' : 'Role Action';
	if (lower.includes('cache')) return 'Clear System Cache';
	if (lower.includes('reset')) return 'Reset Defaults';
	switch (nature) {
		case 'danger': return 'Delete Confirmation';
		case 'warning': return 'Warning & Confirmation';
		case 'info': return 'Confirm Action';
		case 'success': return 'Confirm Action';
		default: return 'Confirmation';
	}
};

const getDefaultConfirmBtn = (nature) => {
	switch (nature) {
		case 'danger': return 'Yes, Delete';
		case 'warning': return 'Yes, Proceed';
		case 'info': return 'Confirm';
		case 'success': return 'Accept';
		default: return 'Confirm';
	}
};

const initCustomConfirmSystem = () => {
	// 1. Programmatic API: window.$confirm({ nature, title, message, confirmText, cancelText })
	window.$confirm = (options = {}) => {
		return new Promise((resolve) => {
			const nature = options.nature || detectAlertNature(options.message || options.title || '');
			window.dispatchEvent(new CustomEvent('system-confirm', {
				detail: {
					nature,
					title: options.title || getDefaultConfirmTitle(nature, options.message),
					message: options.message || 'Are you sure you want to proceed?',
					confirmText: options.confirmText || getDefaultConfirmBtn(nature),
					cancelText: options.cancelText || 'Cancel',
					badge: options.badge,
					onConfirm: () => resolve(true),
					onCancel: () => resolve(false),
				}
			}));
		});
	};

	// 2. Intercept Livewire's __livewire_confirm on elements with wire:confirm
	const wrapWireConfirmElement = (el) => {
		if (!el || el.__livewire_confirm_wrapped) return;

		el.__livewire_confirm = (action, instead) => {
			const currentMsg = el.getAttribute('wire:confirm') || 'Are you sure?';
			const nature = el.dataset.confirmNature || detectAlertNature(currentMsg, el);
			const title = el.dataset.confirmTitle || getDefaultConfirmTitle(nature, currentMsg);
			const confirmBtn = el.dataset.confirmBtn || getDefaultConfirmBtn(nature);
			const cancelBtn = el.dataset.cancelBtn || 'Cancel';

			window.dispatchEvent(new CustomEvent('system-confirm', {
				detail: {
					nature,
					title,
					message: currentMsg,
					confirmText: confirmBtn,
					cancelText: cancelBtn,
					badge: el.dataset.confirmBadge,
					onConfirm: () => {
						try { action(); } catch (e) { console.error(e); }
					},
					onCancel: () => {
						try { instead(); } catch (e) { console.error(e); }
					},
				}
			}));
		};

		el.__livewire_confirm_wrapped = true;
	};

	// Capture phase click on any element with wire:confirm so Livewire's confirm() never fires
	document.addEventListener('click', (event) => {
		const target = event.target.closest('[wire\\:confirm]');
		if (target) {
			wrapWireConfirmElement(target);
		}
	}, true);

	// 3. Form submissions with [data-delete-confirm]
	document.addEventListener('submit', (event) => {
		const submitter = event.submitter;
		if (!submitter || !submitter.matches('[data-delete-confirm]')) {
			return;
		}

		event.preventDefault();
		const form = event.target;
		const message = submitter.dataset.confirmMessage || 'This action cannot be undone.';
		const nature = submitter.dataset.confirmNature || 'danger';
		const title = submitter.dataset.confirmTitle || 'Delete Record?';

		window.$confirm({
			nature,
			title,
			message,
			confirmText: submitter.dataset.confirmBtn || 'Yes, delete it',
		}).then((confirmed) => {
			if (confirmed) {
				form.submit();
			}
		});
	});
};

let buttonsInitialized = false;

const initButtons = () => {
	if (buttonsInitialized) {
		return;
	}
	buttonsInitialized = true;

	document.addEventListener('click', (event) => {
		const target = event.target;

		const sidebarToggle = target.closest('[data-sidebar-toggle]');
		if (sidebarToggle) {
			event.preventDefault();
			toggleSidebar();
			return;
		}

		const sidebarClose = target.closest('[data-sidebar-close]');
		if (sidebarClose) {
			event.preventDefault();
			closeSidebar();
			return;
		}

		const modalTrigger = target.closest('[data-open-modal]');
		if (modalTrigger) {
			event.preventDefault();
			openModal(modalTrigger.dataset.openModal || '');
			return;
		}

		const modalClose = target.closest('[data-modal-close]');
		if (modalClose) {
			event.preventDefault();
			closeModal(modalClose.dataset.modalClose || '');
			return;
		}

		const sidebarMenuToggle = target.closest('[data-sidebar-menu-toggle]');
		if (sidebarMenuToggle) {
			event.preventDefault();
			toggleSidebarSubmenu(sidebarMenuToggle);
			return;
		}

		const dropdownToggle = target.closest('[data-dropdown-toggle]');
		if (dropdownToggle) {
			event.preventDefault();
			toggleDropdown(dropdownToggle.dataset.dropdownToggle || '');
			return;
		}

		const saveButton = target.closest('[data-demo-save]');
		if (saveButton) {
			event.preventDefault();
			showToast('Saved successfully');
			return;
		}

		if (!target.closest('[data-dropdown-toggle]') && !target.closest('[data-dropdown]')) {
			closeAllDropdowns();
		}

		if (!target.closest('[data-modal]') && !target.closest('[data-open-modal]')) {
			document.querySelectorAll('[data-modal]').forEach((modal) => {
				if (!modal.classList.contains('hidden') && modal.dataset.modal !== 'quick-create-modal') {
					const panel = modal.querySelector('[data-modal-panel]');
					if (panel) {
						panel.classList.add('translate-x-full');
					}
				}
			});
		}
	});

	// Hover (mouseover) for dropdowns and sidebar submenus
	document.addEventListener('mouseover', (event) => {
		const target = event.target;

		const dropdownToggle = target.closest('[data-dropdown-toggle]');
		if (dropdownToggle) {
			clearTimeout(dropdownHoverTimer);
			const id = dropdownToggle.dataset.dropdownToggle;
			if (id) openDropdown(id);
			return;
		}

		const dropdownMenu = target.closest('[data-dropdown]');
		if (dropdownMenu) {
			clearTimeout(dropdownHoverTimer);
			return;
		}

		const sidebarToggle = target.closest('[data-sidebar-menu-toggle]');
		if (sidebarToggle) {
			clearTimeout(sidebarHoverTimer);
			const id = sidebarToggle.dataset.sidebarMenuToggle;
			if (id) {
				const submenu = document.querySelector(`[data-sidebar-submenu="${id}"]`);
				if (submenu && submenu.classList.contains('hidden')) {
					closeSidebarSubmenus(id);
					submenu.classList.remove('hidden');
					sidebarToggle.setAttribute('aria-expanded', 'true');
				}
			}
			return;
		}

		const sidebarSubmenu = target.closest('[data-sidebar-submenu]');
		if (sidebarSubmenu) {
			clearTimeout(sidebarHoverTimer);
			return;
		}
	});

	// Hover leave (mouseout) with graceful delay
	document.addEventListener('mouseout', (event) => {
		const target = event.target;
		const related = event.relatedTarget;

		const dropdownToggle = target.closest('[data-dropdown-toggle]');
		const dropdownMenu = target.closest('[data-dropdown]');

		if (dropdownToggle || dropdownMenu) {
			const id = dropdownToggle?.dataset.dropdownToggle || dropdownMenu?.dataset.dropdown;
			const toggleEl = document.querySelector(`[data-dropdown-toggle="${id}"]`);
			const menuEl = document.querySelector(`[data-dropdown="${id}"]`);

			if (related && ((toggleEl && toggleEl.contains(related)) || (menuEl && menuEl.contains(related)))) {
				return;
			}

			clearTimeout(dropdownHoverTimer);
			dropdownHoverTimer = setTimeout(() => {
				if (menuEl) closeDropdown(id);
			}, 250);
		}

		const sidebarToggle = target.closest('[data-sidebar-menu-toggle]');
		const sidebarSubmenu = target.closest('[data-sidebar-submenu]');

		if (sidebarToggle || sidebarSubmenu) {
			const id = sidebarToggle?.dataset.sidebarMenuToggle || sidebarSubmenu?.dataset.sidebarSubmenu;
			const toggleEl = document.querySelector(`[data-sidebar-menu-toggle="${id}"]`);
			const menuEl = document.querySelector(`[data-sidebar-submenu="${id}"]`);

			if (related && ((toggleEl && toggleEl.contains(related)) || (menuEl && menuEl.contains(related)))) {
				return;
			}

			if (menuEl?.dataset.pinned === 'true' || toggleEl?.classList.contains('sidebar-menu-item-active')) {
				return;
			}

			clearTimeout(sidebarHoverTimer);
			sidebarHoverTimer = setTimeout(() => {
				if (menuEl && menuEl.dataset.pinned !== 'true' && !toggleEl?.classList.contains('sidebar-menu-item-active')) {
					menuEl.classList.add('hidden');
					if (toggleEl) toggleEl.setAttribute('aria-expanded', 'false');
				}
			}, 300);
		}
	});

	document.addEventListener('click', (event) => {
		if (event.target.closest('[data-sidebar-backdrop]')) {
			closeSidebar();
		}
	});
};

const initPlugins = () => {
	if (window.lucide) {
		window.lucide.createIcons();
	}

	initCharts();
	initPickers();
	initSelect2();
	initSummernote();
	initServiceTabs();
	initQuickCreate();
	initFlashToast();
};

const initSidebarState = () => {
	const saved = localStorage.getItem(sidebarStorageKey);

	if (saved === '1' && isDesktop()) {
		applySidebarState(true);
		return;
	}

	applySidebarState(false);
};

const initLivewireToastBridge = () => {
	if (!window.Livewire) {
		return;
	}

	window.Livewire.on('toast', (event) => {
		if (!event || typeof event !== 'object') {
			return;
		}

		showToast(event.message || '', event.type || 'success');
	});

	window.Livewire.on('toast.success', (event) => {
		if (!event || typeof event !== 'object') {
			return;
		}

		showToast(event.message || '', 'success');
	});

	window.Livewire.on('toast.danger', (event) => {
		if (!event || typeof event !== 'object') {
			return;
		}

		showToast(event.message || '', 'danger');
	});
};

window.addEventListener('DOMContentLoaded', () => {
	initSidebarState();
	initSidebarMenus();
	initThemeControls();
	initButtons();
	initCustomConfirmSystem();
	initPlugins();
});

document.addEventListener('livewire:init', () => {
	initLivewireToastBridge();
});

document.addEventListener('livewire:navigated', () => {
	initSidebarState();
	initSidebarMenus();
	initThemeControls();
	initButtons();
	initCustomConfirmSystem();
	initPlugins();
});

window.addEventListener('resize', () => {
	const sidebar = document.querySelector('[data-sidebar]');
	if (!sidebar) {
		return;
	}

	if (isDesktop()) {
		const backdrop = document.querySelector('[data-sidebar-backdrop]');
		if (backdrop) {
			backdrop.classList.add('hidden');
		}

		sidebar.classList.remove('-translate-x-full');
		sidebar.classList.add('translate-x-0');
		initSidebarState();
	}
});

window.crmShowToast = showToast;


