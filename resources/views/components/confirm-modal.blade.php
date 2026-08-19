@props([
    'type' => 'danger',
    'title' => 'Apakah Anda yakin?',
    'message' => 'Tindakan ini tidak dapat dibatalkan.',
    'confirmText' => 'Ya',
    'cancelText' => 'Batal',
])

@php
$iconColor = match ($type) {
    'danger' => 'text-red-600 bg-red-100',
    'warning' => 'text-amber-600 bg-amber-100',
    'success' => 'text-emerald-600 bg-emerald-100',
};

$confirmClass = match ($type) {
    'danger' => 'bg-red-600 hover:bg-red-500 focus:ring-red-500 text-white',
    'warning' => 'bg-amber-500 hover:bg-amber-400 focus:ring-amber-500 text-white',
    'success' => 'bg-emerald-600 hover:bg-emerald-500 focus:ring-emerald-500 text-white',
};

$icon = match ($type) {
    'danger' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />',
    'warning' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />',
    'success' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />',
};
@endphp

<div
    id="global-confirm-modal"
    class="fixed inset-0 z-50 hidden overflow-y-auto px-4 py-6 sm:px-0"
    role="dialog"
    aria-modal="true"
    aria-labelledby="confirm-modal-title"
    aria-describedby="confirm-modal-message"
>
    <div class="fixed inset-0 bg-slate-500 opacity-75" onclick="globalConfirmModal.close()"></div>

    <div class="mb-6 bg-white rounded-2xl overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-md sm:mx-auto">
        <div class="p-6">
            <div class="flex items-start gap-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full" id="confirm-modal-icon-container">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" id="confirm-modal-icon">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-slate-900" id="confirm-modal-title">{{ $title }}</h3>
                    <p class="mt-2 text-sm text-slate-600" id="confirm-modal-message">{{ $message }}</p>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="globalConfirmModal.close()" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-5 text-sm font-semibold text-slate-700 transition duration-300 hover:border-slate-400 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C26A4A] focus-visible:ring-offset-2">
                    {{ $cancelText }}
                </button>
                <button type="button" onclick="globalConfirmModal.confirm()" id="confirm-modal-button" class="inline-flex min-h-11 items-center justify-center rounded-xl px-5 text-sm font-semibold text-white transition duration-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 {{ $confirmClass }}">
                    {{ $confirmText }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    window.globalConfirmModal = {
        currentForm: null,

        open(form, options = {}) {
            this.currentForm = form;

            const titleEl = document.getElementById('confirm-modal-title');
            const messageEl = document.getElementById('confirm-modal-message');
            const confirmBtn = document.getElementById('confirm-modal-button');
            const iconContainer = document.getElementById('confirm-modal-icon-container');
            const iconSvg = document.getElementById('confirm-modal-icon');
            const modal = document.getElementById('global-confirm-modal');

            if (titleEl) titleEl.textContent = options.title || 'Apakah Anda yakin?';
            if (messageEl) messageEl.textContent = options.message || 'Tindakan ini tidak dapat dibatalkan.';

            const type = options.type || 'danger';
            const confirmText = options.confirmText || 'Ya';
            const cancelText = options.cancelText || 'Batal';

            if (confirmBtn) {
                confirmBtn.textContent = confirmText;
                confirmBtn.className = 'inline-flex min-h-11 items-center justify-center rounded-xl px-5 text-sm font-semibold text-white transition duration-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 ' + this.getConfirmClass(type);
            }

            if (iconContainer) {
                iconContainer.className = 'flex h-10 w-10 shrink-0 items-center justify-center rounded-full ' + this.getIconColor(type);
            }

            if (iconSvg) {
                iconSvg.innerHTML = this.getIcon(type);
            }

            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-y-hidden');
            }
        },

        close() {
            const modal = document.getElementById('global-confirm-modal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-y-hidden');
            }
            this.currentForm = null;
        },

        confirm() {
            if (this.currentForm) {
                this.currentForm.submit();
            }
            this.close();
        },

        getConfirmClass(type) {
            const classes = {
                danger: 'bg-red-600 hover:bg-red-500 focus:ring-red-500 text-white',
                warning: 'bg-amber-500 hover:bg-amber-400 focus:ring-amber-500 text-white',
                success: 'bg-emerald-600 hover:bg-emerald-500 focus:ring-emerald-500 text-white',
            };
            return classes[type] || classes.danger;
        },

        getIconColor(type) {
            const colors = {
                danger: 'text-red-600 bg-red-100',
                warning: 'text-amber-600 bg-amber-100',
                success: 'text-emerald-600 bg-emerald-100',
            };
            return colors[type] || colors.danger;
        },

        getIcon(type) {
            const icons = {
                danger: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />',
                warning: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />',
                success: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />',
            };
            return icons[type] || icons.danger;
        }
    };

    window.confirmAction = function(form, title, message, type = 'danger', confirmText = 'Ya', cancelText = 'Batal') {
        if (typeof globalConfirmModal !== 'undefined') {
            globalConfirmModal.open(form, {
                title: title,
                message: message,
                type: type,
                confirmText: confirmText,
                cancelText: cancelText,
            });
        } else {
            form.submit();
        }
    };
</script>
