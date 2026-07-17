import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

/**
 * Live notification bell — polls feed endpoint.
 * Interval slows when tab hidden to cut load.
 */
document.addEventListener('alpine:init', () => {
    Alpine.data('notificationBell', (feedUrl, markAllUrl, csrf) => ({
        open: false,
        loading: false,
        marking: false,
        items: [],
        unreadCount: 0,
        feedUrl,
        markAllUrl,
        csrf,
        timer: null,
        intervalMs: 8000,

        init() {
            this.refresh();
            this.timer = setInterval(() => {
                if (document.hidden) return;
                this.refresh();
            }, this.intervalMs);

            document.addEventListener('visibilitychange', () => {
                if (! document.hidden) this.refresh();
            });
        },

        async refresh() {
            if (! this.feedUrl || ! window.axios) return;
            this.loading = this.items.length === 0;
            try {
                const { data } = await window.axios.get(this.feedUrl, {
                    headers: { Accept: 'application/json' },
                });
                this.items = Array.isArray(data.items) ? data.items : [];
                this.unreadCount = Number(data.unread_count || 0);
            } catch {
                // keep last good state
            } finally {
                this.loading = false;
            }
        },

        async markAllRead() {
            if (this.unreadCount === 0 || this.marking) return;
            this.marking = true;
            try {
                await window.axios.post(this.markAllUrl, {}, {
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                    },
                });
                this.items = this.items.map((n) => ({ ...n, is_unread: false }));
                this.unreadCount = 0;
            } catch {
                // ignore; user can retry
            } finally {
                this.marking = false;
            }
        },
    }));
});

Alpine.start();
