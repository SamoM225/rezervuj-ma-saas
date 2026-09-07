import './bootstrap';
import { createApp } from 'vue';
import Alpine from 'alpinejs';
import BookingCalendar from './components/BookingCalendar.vue';

window.Alpine = Alpine;
Alpine.start();

const calendarTarget = document.getElementById('booking-calendar-app');

if (calendarTarget) {
    const data = calendarTarget.dataset;
    const parseJson = (value, fallback = []) => {
        try {
            return value ? JSON.parse(value) : fallback;
        } catch {
            return fallback;
        }
    };
    const flag = (value) => value === 'true';

    const app = createApp(BookingCalendar, {
        userId: Number(data.userId) || null,
        userRole: data.userRole || 'worker',
        canManageAll: flag(data.canManageAll),
        canManageBlocks: flag(data.canManageBlocks),
        canUpdateBookings: flag(data.canUpdateBookings),
        canDeleteBookings: flag(data.canDeleteBookings),
        requireConfirmation: flag(data.requireConfirmation),
        feedUrl: data.feedUrl,
        bookingUrl: data.bookingUrl,
        blockStoreUrl: data.blockStoreUrl,
        blockDeleteUrlTemplate: data.blockDeleteUrlTemplate,
        blockUpdateUrlTemplate: data.blockUpdateUrlTemplate,
        statusUrlTemplate: data.statusUrlTemplate,
        rescheduleUrlTemplate: data.rescheduleUrlTemplate,
        deleteUrlTemplate: data.deleteUrlTemplate,
        slotStart: data.slotStart || '08:00',
        slotEnd: data.slotEnd || '18:00',
        workers: parseJson(data.workers),
        services: parseJson(data.services),
    });

    app.config.errorHandler = (err, instance, info) => {
        console.error('Calendar error:', err, info);
    };

    app.mount(calendarTarget);
}
