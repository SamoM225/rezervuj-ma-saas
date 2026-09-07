<template>
  <div class="cal">
    <div class="cal-toolbar">
      <div class="cal-toolbar-group">
        <button type="button" class="btn btn-secondary btn-sm" @click="goToday">{{ t('today') }}</button>
        <h2 class="cal-title">{{ title }}</h2>
      </div>

      <div class="cal-toolbar-group">
        <label class="search cal-search">
          <span class="sr-only">{{ t('search') }}</span>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m20 20-3.5-3.5"/></svg>
          <input v-model="searchInput" type="search" class="input input-sm" :placeholder="t('search_placeholder')">
        </label>
        <div class="cal-views" role="tablist">
          <button
            v-for="view in views"
            :key="view.value"
            type="button"
            role="tab"
            :aria-selected="currentView === view.value"
            :class="['cal-view', { 'is-active': currentView === view.value }]"
            @click="changeView(view.value)"
          >{{ view.label }}</button>
        </div>
        <button type="button" class="btn btn-secondary btn-sm cal-rail-toggle" @click="railOpen = !railOpen">{{ t('filters') }}</button>
        <button type="button" class="btn btn-primary btn-sm" @click="openQuickBooking">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
          {{ t('booking') }}
        </button>
      </div>
    </div>

    <div class="cal-body">
      <aside class="cal-rail" :class="{ 'is-open': railOpen }">
        <section class="cal-rail-section cal-mini">
          <div class="mini-head">
            <button type="button" class="icon-btn" :aria-label="t('prev_month')" @click="shiftMini(-1)">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6 6 6"/></svg>
            </button>
            <span class="mini-title">{{ miniTitle }}</span>
            <button type="button" class="icon-btn" :aria-label="t('next_month')" @click="shiftMini(1)">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6l6 6-6 6"/></svg>
            </button>
          </div>
          <div class="mini-weekdays" aria-hidden="true">
            <span v-for="day in weekdays" :key="day">{{ day }}</span>
          </div>
          <div class="mini-grid">
            <button
              v-for="day in miniDays"
              :key="day.key"
              type="button"
              :class="['mini-day', { 'is-outside': !day.inMonth, 'is-today': day.key === todayKey, 'is-selected': day.key === focusedDate }]"
              :aria-label="day.label"
              :aria-pressed="day.key === focusedDate"
              @click="goToDate(day.key)"
            >
              <span>{{ day.date.getDate() }}</span>
              <i v-if="eventCountByDay[day.key]" class="mini-marker"></i>
            </button>
          </div>
        </section>

        <section v-if="canManageAll && workers.length" class="cal-rail-section">
          <div class="cal-rail-head">
            <h3>{{ t('team') }}</h3>
            <button v-if="selectedWorkers.length" type="button" class="link" style="font-size:.78rem" @click="selectedWorkers = []">{{ t('everyone') }}</button>
          </div>
          <label v-for="worker in workers" :key="worker.id" class="cal-check">
            <input type="checkbox" :value="worker.id" v-model="selectedWorkers">
            <span class="dot" :style="{ background: worker.calendar_color || '#c19a3e' }"></span>
            <span class="cal-check-label">{{ worker.name }}</span>
          </label>
        </section>

        <section class="cal-rail-section">
          <div class="cal-rail-head"><h3>{{ t('booking_status') }}</h3></div>
          <div class="cal-chips">
            <button
              v-for="chip in statusOptions"
              :key="chip.value"
              type="button"
              :class="['chip cal-chip', { 'is-active': activeStatuses.includes(chip.value) }]"
              :aria-pressed="activeStatuses.includes(chip.value)"
              @click="toggleStatus(chip.value)"
            >
              <span class="dot" :style="{ background: chip.color }"></span>
              {{ chip.label }}
            </button>
          </div>
        </section>

        <section class="cal-rail-section">
          <label class="cal-check">
            <input type="checkbox" v-model="showWeekends">
            <span class="cal-check-label">{{ t('show_weekends') }}</span>
          </label>
          <div class="cal-legend">
            <span><i class="legend-swatch is-block"></i> {{ t('blocked_time') }}</span>
            <span><i class="legend-swatch is-holiday"></i> {{ t('closed') }}</span>
            <span v-if="hasScopedHours"><i class="legend-swatch is-off"></i> Mimo pracovnej doby</span>
          </div>
        </section>
      </aside>

      <div class="cal-main">
        <FullCalendar ref="calendarRef" :options="calendarOptions" />
      </div>
    </div>

    <transition name="sheet">
      <div v-if="slotDecisionVisible" class="slot-sheet" role="dialog" :aria-label="t('selected_time')">
        <div class="slot-sheet-info">
          <strong class="tabular" v-if="pendingSlot.allDay">{{ pendingSlot.date === pendingSlot.endDate ? t('all_day') : rangeDays + ' ' + t('days') }}</strong>
          <strong class="tabular" v-else>{{ pendingSlot.startTime }} – {{ pendingSlot.endTime }}</strong>
          <span>{{ longDate(pendingSlot.date) }}<template v-if="pendingSlot.endDate !== pendingSlot.date"> – {{ longDate(pendingSlot.endDate) }}</template></span>
        </div>
        <div class="slot-sheet-actions">
          <button v-if="!pendingSlot.allDay" type="button" class="btn btn-primary btn-sm" :disabled="!services.length" @click="showBookingModal = true">{{ t('new_booking') }}</button>
          <button v-if="canManageBlocks" type="button" class="btn btn-secondary btn-sm" @click="showBlockModal = true">{{ t('block_time') }}</button>
          <button type="button" class="btn btn-ghost btn-sm" @click="clearSelection">{{ t('cancel') }}</button>
        </div>
      </div>
    </transition>

    <div class="cal-toasts" aria-live="polite">
      <transition-group name="toast">
        <div v-for="toast in toasts" :key="toast.id" :class="['cal-toast', `is-${toast.type}`]">{{ toast.message }}</div>
      </transition-group>
    </div>

    <BookingDetailsModal
      v-if="activeBooking"
      :booking="activeBooking"
      :can-update="canUpdateBookings"
      :can-delete="canDeleteBookings"
      :require-confirmation="requireConfirmation"
      @close="activeBooking = null"
      @status="updateBookingStatus"
      @delete="confirmDeleteBooking"
    />

    <NewBookingModal
      v-if="showBookingModal && pendingSlot"
      :selected-date="pendingSlot.date"
      :selected-time="pendingSlot.startTime"
      :default-worker-id="slotWorkerId"
      :workers="workers"
      :services="services"
      :require-confirmation="requireConfirmation"
      :saving="saving"
      @close="handleModalClose"
      @create="createBookingFromModal"
    />

    <BlockSlotModal
      v-if="showBlockModal && pendingSlot"
      :slot="pendingSlot"
      :workers="workers"
      :default-worker-id="slotWorkerId"
      :can-pick-worker="canManageAll"
      :saving="saving"
      @close="handleModalClose"
      @create="createBlockFromModal"
    />

    <Teleport to="body">
      <div v-if="confirmState" class="modal-backdrop" @click.self="confirmState = null">
        <div class="modal" style="max-width:26rem" role="alertdialog" aria-modal="true">
          <div class="modal-body">
            <h3 class="modal-title" style="margin-bottom:.4rem">{{ confirmState.title }}</h3>
            <p class="card-sub">{{ confirmState.text }}</p>
          </div>
          <div class="modal-foot">
            <button type="button" class="btn btn-secondary" @click="confirmState = null">{{ t('back') }}</button>
            <button type="button" :class="['btn', confirmState.danger ? 'btn-danger' : 'btn-primary']" @click="runConfirm">{{ confirmState.action }}</button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, reactive, ref, watch } from 'vue'
import axios from 'axios'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'
import skLocale from '@fullcalendar/core/locales/sk'
import csLocale from '@fullcalendar/core/locales/cs'
import enLocale from '@fullcalendar/core/locales/en-gb'
import { t, pageLocale } from '../calendar/i18n'

import NewBookingModal from './NewBookingModal.vue'
import BookingDetailsModal from './BookingDetailsModal.vue'
import BlockSlotModal from './BlockSlotModal.vue'
import { STATUS, formatDate, formatTime, hexToRgba, hm, hms, longDate, parseDate } from '../calendar/utils'

const props = defineProps({
  userId: { type: Number, default: null },
  userRole: { type: String, default: 'admin' },
  canManageAll: { type: Boolean, default: false },
  canManageBlocks: { type: Boolean, default: false },
  canUpdateBookings: { type: Boolean, default: false },
  canDeleteBookings: { type: Boolean, default: false },
  requireConfirmation: { type: Boolean, default: false },
  feedUrl: { type: String, required: true },
  bookingUrl: { type: String, required: true },
  blockStoreUrl: { type: String, default: '' },
  blockDeleteUrlTemplate: { type: String, default: '' },
  blockUpdateUrlTemplate: { type: String, default: '' },
  statusUrlTemplate: { type: String, default: '' },
  rescheduleUrlTemplate: { type: String, default: '' },
  deleteUrlTemplate: { type: String, default: '' },
  slotStart: { type: String, default: '08:00' },
  slotEnd: { type: String, default: '18:00' },
  workers: { type: Array, default: () => [] },
  services: { type: Array, default: () => [] },
})

axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
axios.defaults.headers.common['Accept'] = 'application/json'

// Phones get a 3-day view instead of a 7-column week: the columns stay wide enough to read names.
const isPhone = window.innerWidth < 768
const views = [
  { label: t('view_day'), value: 'timeGridDay' },
  isPhone ? { label: t('view_3days'), value: 'timeGrid3' } : { label: t('view_week'), value: 'timeGridWeek' },
  { label: t('view_month'), value: 'dayGridMonth' },
]
const statusOptions = Object.entries(STATUS)
  .filter(([value]) => value !== 'pending' || props.requireConfirmation)
  .map(([value, meta]) => ({ value, ...meta }))
const weekdays = t('weekdays')
const todayKey = formatDate(new Date())

const workers = ref(props.workers)
const services = ref(props.services)
const selectedWorkers = ref([])
const activeStatuses = ref(statusOptions.map(o => o.value).filter(v => v !== 'cancelled'))
const showWeekends = ref(true)
const searchInput = ref('')
const searchTerm = ref('')
const currentView = ref(window.innerWidth < 768 ? 'timeGridDay' : 'timeGridWeek')
const title = ref('')
const focusedDate = ref(todayKey)
const miniMonth = ref(new Date(new Date().getFullYear(), new Date().getMonth(), 1))
const railOpen = ref(false)
const calendarRef = ref(null)
const pendingSlot = ref(null)
const slotWorkerId = ref(null)
const showBookingModal = ref(false)
const showBlockModal = ref(false)
const activeBooking = ref(null)
const confirmState = ref(null)
const saving = ref(false)
const toasts = ref([])
const eventCountByDay = ref({})
const hasScopedHours = ref(false)

const canManageAll = computed(() => props.canManageAll)
const slotDecisionVisible = computed(() => Boolean(pendingSlot.value) && !showBookingModal.value && !showBlockModal.value)
const rangeDays = computed(() => pendingSlot.value ? Math.round((parseDate(pendingSlot.value.endDate) - parseDate(pendingSlot.value.date)) / 86400000) + 1 : 0)
const miniTitle = computed(() => miniMonth.value.toLocaleDateString('sk-SK', { month: 'long', year: 'numeric' }))
const miniDays = computed(() => {
  const base = miniMonth.value
  const first = new Date(base.getFullYear(), base.getMonth(), 1)
  const offset = (first.getDay() + 6) % 7
  const start = new Date(base.getFullYear(), base.getMonth(), 1 - offset)
  return Array.from({ length: 42 }, (_, index) => {
    const date = new Date(start.getFullYear(), start.getMonth(), start.getDate() + index)
    return {
      date,
      key: formatDate(date),
      inMonth: date.getMonth() === base.getMonth(),
      label: date.toLocaleDateString('sk-SK', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }),
    }
  })
})

function padTime(time, minutes) {
  const [h, m] = hm(time).split(':').map(Number)
  const total = Math.min(24 * 60, Math.max(0, h * 60 + m + minutes))
  return `${String(Math.floor(total / 60)).padStart(2, '0')}:${String(total % 60).padStart(2, '0')}:00`
}

const calendarOptions = reactive({
  plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
  locale: { 'sk-SK': skLocale, 'cs-CZ': csLocale, 'en-GB': enLocale }[pageLocale()] || skLocale,
  firstDay: 1,
  initialView: currentView.value,
  headerToolbar: false,
  height: 'auto',
  expandRows: true,
  nowIndicator: true,
  slotDuration: '00:30:00',
  slotLabelInterval: '01:00',
  snapDuration: '00:15:00',
  slotMinTime: padTime(props.slotStart, -60),
  slotMaxTime: padTime(props.slotEnd, 60),
  scrollTime: hms(props.slotStart),
  allDaySlot: false,
  weekends: showWeekends.value,
  // Appointments at the same time (several staff) sit side by side instead of overlapping.
  slotEventOverlap: false,
  eventOrder: 'workerOrder,start,title',
  eventMinHeight: 22,
  businessHours: { daysOfWeek: [1, 2, 3, 4, 5, 6], startTime: hm(props.slotStart), endTime: hm(props.slotEnd) },
  selectable: true,
  selectMirror: true,
  longPressDelay: 160,
  selectMinDistance: 4,
  editable: props.canUpdateBookings || props.canManageBlocks,
  eventDurationEditable: false,
  eventStartEditable: true,
  eventResizableFromStart: false,
  eventAllow: (dropInfo, draggedEvent) => {
    const { type, status } = draggedEvent?.extendedProps || {}
    if (type === 'booking') return props.canUpdateBookings && status !== 'cancelled'
    if (type === 'block') return props.canManageBlocks
    return false
  },
  eventDrop: handleEventDrop,
  eventResize: handleEventDrop,
  events: fetchEvents,
  select: handleSlotSelect,
  eventClick: handleEventClick,
  eventContent: renderEventContent,
  eventClassNames: (arg) => arg.event.extendedProps.type === 'booking'
    ? ['ev-booking', `is-${arg.event.extendedProps.status}`]
    : arg.event.extendedProps.type === 'block' ? ['ev-block'] : ['ev-holiday'],
  datesSet(info) {
    title.value = info.view.title
    currentView.value = info.view.type
    const current = info.view.calendar.getDate()
    focusedDate.value = formatDate(current)
    miniMonth.value = new Date(current.getFullYear(), current.getMonth(), 1)
  },
  dayHeaderFormat: { weekday: 'short', day: 'numeric' },
  eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
  slotLabelFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
  dayMaxEvents: 4,
  moreLinkText: (n) => `+${n} ${t('more')}`,
  // A week column is narrow: at most 3 appointments side by side, the rest behind a "+N ďalšie" link.
  views: {
    timeGridWeek: { eventMaxStack: 2 },
    timeGridDay: { eventMaxStack: 8 },
    // one readable appointment per column on a phone, the rest behind "+N ďalšie"
    timeGrid3: { type: 'timeGrid', duration: { days: 3 }, eventMaxStack: 1 },
  },
})

let searchDebounce
let refetchTimer

watch(searchInput, value => {
  clearTimeout(searchDebounce)
  searchDebounce = setTimeout(() => {
    searchTerm.value = value.trim()
    requestRefetch()
  }, 250)
})
watch(showWeekends, value => { calendarOptions.weekends = value })
watch(selectedWorkers, requestRefetch, { deep: true })
watch(activeStatuses, requestRefetch, { deep: true })

function api() {
  return calendarRef.value?.getApi()
}

function requestRefetch() {
  clearTimeout(refetchTimer)
  refetchTimer = setTimeout(() => api()?.refetchEvents(), 150)
}

function notify(type, message) {
  const id = Date.now() + Math.random()
  toasts.value.push({ id, type, message })
  setTimeout(() => { toasts.value = toasts.value.filter(t => t.id !== id) }, 4200)
}

// ---- data --------------------------------------------------------------
async function fetchEvents(fetchInfo, successCallback, failureCallback) {
  try {
    const params = {
      start: formatDate(fetchInfo.start),
      end: formatDate(new Date(fetchInfo.end.getTime() - 1)),
      search: searchTerm.value || undefined,
    }
    if (selectedWorkers.value.length) params.worker_ids = selectedWorkers.value
    if (activeStatuses.value.length && activeStatuses.value.length < Object.keys(STATUS).length) params.statuses = activeStatuses.value

    const { data } = await axios.get(props.feedUrl, { params })
    const events = [
      ...(data.bookings || []).map(mapBookingEvent),
      ...(data.blocks || []).map(mapBlockEvent),
      ...(data.holidays || []).map(mapHolidayEvent),
    ]

    const counts = {}
    events.filter(e => e.extendedProps.type !== 'holiday').forEach(e => { const k = e.start.slice(0, 10); counts[k] = (counts[k] || 0) + 1 })
    eventCountByDay.value = counts

    if (Array.isArray(data.business_hours) && data.business_hours.length) {
      calendarOptions.businessHours = data.business_hours
      hasScopedHours.value = true
    } else {
      calendarOptions.businessHours = { daysOfWeek: [1, 2, 3, 4, 5, 6], startTime: hm(props.slotStart), endTime: hm(props.slotEnd) }
      hasScopedHours.value = false
    }

    successCallback(events)
  } catch (error) {
    failureCallback(error)
    notify('error', t('load_failed'))
  }
}

function mapBookingEvent(booking) {
  const color = booking.worker?.calendar_color || '#c19a3e'
  const cancelled = booking.status === 'cancelled'
  return {
    id: `booking-${booking.id}`,
    title: booking.customer_name,
    start: `${booking.date}T${hms(booking.start_time)}`,
    end: `${booking.date}T${hms(booking.end_time)}`,
    backgroundColor: hexToRgba(color, cancelled ? 0.08 : 0.16),
    borderColor: color,
    textColor: '#1f1a14',
    editable: props.canUpdateBookings && !cancelled,
    extendedProps: { type: 'booking', status: booking.status, booking, color, workerOrder: workerIndex(booking.user_id) },
  }
}

function mapBlockEvent(block) {
  return {
    id: `block-${block.id}`,
    title: block.title || t('blocked'),
    start: `${block.date}T${hms(block.start_time)}`,
    end: `${block.date}T${hms(block.end_time)}`,
    backgroundColor: '#efe9df',
    borderColor: '#b8ad9c',
    textColor: '#4a4136',
    editable: props.canManageBlocks && Boolean(props.blockUpdateUrlTemplate),
    durationEditable: props.canManageBlocks && Boolean(props.blockUpdateUrlTemplate),
    extendedProps: { type: 'block', block, workerOrder: workerIndex(block.user_id) },
  }
}

function mapHolidayEvent(date) {
  return {
    id: `holiday-${date}`,
    start: date,
    allDay: true,
    display: 'background',
    backgroundColor: 'rgba(179, 38, 30, 0.07)',
    extendedProps: { type: 'holiday' },
  }
}

function workerIndex(userId) {
  const index = workers.value.findIndex(w => w.id === userId)
  return index === -1 ? 99 : index
}

function escapeHtml(text = '') {
  return String(text).replace(/[&<>"']/g, ch => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch]))
}

function renderEventContent(arg) {
  const { type } = arg.event.extendedProps
  if (!type) {
    // Selection mirror while dragging: no booking data yet.
    return { html: `<div class="ev"><span class="ev-time">${escapeHtml(arg.timeText || '')}</span><span class="ev-name">${t('new_slot')}</span></div>` }
  }
  if (type === 'holiday') return { html: '' }
  if (type === 'block') {
    return { html: `<div class="ev"><span class="ev-time">${escapeHtml(arg.timeText)}</span><span class="ev-name">${escapeHtml(arg.event.title)}</span></div>` }
  }
  const { booking, color } = arg.event.extendedProps
  const month = arg.view.type === 'dayGridMonth'
  const worker = props.canManageAll && !month ? `<span class="ev-worker"><i style="background:${color}"></i>${escapeHtml(booking.worker?.name || '')}</span>` : ''
  return {
    html: `<div class="ev">
      ${month ? `<i class="ev-dot" style="background:${color}"></i>` : ''}
      <span class="ev-time">${escapeHtml(arg.timeText)}</span>
      <span class="ev-name">${escapeHtml(booking.customer_name)}</span>
      ${month ? '' : `<span class="ev-service">${escapeHtml(booking.service?.name || '')}</span>${worker}`}
    </div>`,
  }
}

// ---- interactions ------------------------------------------------------
function handleSlotSelect(selection) {
  const lastDay = new Date(selection.end.getTime() - 1)
  const endDate = formatDate(lastDay)
  const startDate = formatDate(selection.start)
  const allDay = selection.allDay || startDate !== endDate
  pendingSlot.value = {
    date: startDate,
    endDate,
    allDay,
    startTime: allDay ? hm(props.slotStart) : formatTime(selection.start),
    endTime: allDay ? hm(props.slotEnd) : formatTime(selection.end),
  }
  slotWorkerId.value = canManageAll.value
    ? (selectedWorkers.value.length === 1 ? selectedWorkers.value[0] : null)
    : props.userId
}

function openQuickBooking() {
  const now = new Date()
  const rounded = new Date(now)
  rounded.setMinutes(Math.ceil(now.getMinutes() / 15) * 15, 0, 0)
  pendingSlot.value = { date: focusedDate.value, endDate: focusedDate.value, allDay: false, startTime: formatTime(rounded), endTime: formatTime(new Date(rounded.getTime() + 60 * 60000)) }
  slotWorkerId.value = canManageAll.value ? (selectedWorkers.value.length === 1 ? selectedWorkers.value[0] : null) : props.userId
  showBookingModal.value = true
}

function handleEventClick(info) {
  const { type } = info.event.extendedProps
  if (type === 'booking') {
    activeBooking.value = info.event.extendedProps.booking
    return
  }
  if (type === 'block' && props.canManageBlocks) {
    const block = info.event.extendedProps.block
    confirmState.value = {
      title: t('remove_block_title'),
      text: `${block.title || t('blocked')} · ${longDate(block.date)} ${hm(block.start_time)} – ${hm(block.end_time)}`,
      action: t('remove'),
      danger: true,
      run: () => removeBlock(block.id),
    }
  }
}

async function handleEventDrop(info) {
  if (info.event.extendedProps.type === 'block') {
    return moveBlock(info)
  }
  if (!props.rescheduleUrlTemplate) { info.revert(); return }
  const booking = info.event.extendedProps.booking
  try {
    await axios.patch(props.rescheduleUrlTemplate.replace('__BOOKING__', booking.id), {
      date: formatDate(info.event.start),
      start_time: formatTime(info.event.start),
    })
    notify('success', t('moved_to', { when: longDate(formatDate(info.event.start)) + ' ' + formatTime(info.event.start) }))
    requestRefetch()
  } catch (error) {
    info.revert()
    notify('error', error.response?.data?.message || t('move_failed'))
  }
}

async function moveBlock(info) {
  if (!props.blockUpdateUrlTemplate) { info.revert(); return }
  const block = info.event.extendedProps.block
  try {
    await axios.patch(props.blockUpdateUrlTemplate.replace('__BLOCK__', block.id), {
      date: formatDate(info.event.start),
      start_time: formatTime(info.event.start),
      end_time: info.event.end ? formatTime(info.event.end) : undefined,
    })
    notify('success', t('block_moved'))
    requestRefetch()
  } catch (error) {
    info.revert()
    notify('error', error.response?.data?.message || t('block_move_failed'))
  }
}

function runConfirm() {
  const state = confirmState.value
  confirmState.value = null
  state?.run?.()
}

async function removeBlock(blockId) {
  if (!props.blockDeleteUrlTemplate) return
  try {
    await axios.delete(props.blockDeleteUrlTemplate.replace('__BLOCK__', blockId))
    notify('success', t('block_removed'))
    requestRefetch()
  } catch (error) {
    notify('error', error.response?.data?.message || t('block_remove_failed'))
  }
}

function toggleStatus(status) {
  activeStatuses.value = activeStatuses.value.includes(status)
    ? activeStatuses.value.filter(item => item !== status)
    : [...activeStatuses.value, status]
}

function goToday() { api()?.today() }
function goPrev() { api()?.prev() }
function goNext() { api()?.next() }
function goToDate(key) {
  api()?.gotoDate(key)
  focusedDate.value = key
  railOpen.value = false
}
function shiftMini(direction) {
  miniMonth.value = new Date(miniMonth.value.getFullYear(), miniMonth.value.getMonth() + direction, 1)
}
function changeView(view) {
  currentView.value = view
  api()?.changeView(view)
}

function clearSelection() {
  pendingSlot.value = null
  slotWorkerId.value = null
  showBookingModal.value = false
  showBlockModal.value = false
  api()?.unselect()
}

function handleModalClose() {
  clearSelection()
}

async function createBookingFromModal(payload) {
  saving.value = true
  try {
    await axios.post(props.bookingUrl, payload)
    notify('success', props.requireConfirmation ? t('created_pending') : t('created'))
    handleModalClose()
    requestRefetch()
  } catch (error) {
    const errors = error.response?.data?.errors
    notify('error', errors ? Object.values(errors).flat()[0] : (error.response?.data?.message || t('create_failed')))
  } finally {
    saving.value = false
  }
}

async function createBlockFromModal(payload) {
  if (!props.canManageBlocks || !props.blockStoreUrl) return
  saving.value = true
  try {
    await axios.post(props.blockStoreUrl, payload)
    notify('success', payload.end_date && payload.end_date !== payload.date ? t('days_blocked') : t('time_blocked'))
    handleModalClose()
    requestRefetch()
  } catch (error) {
    const errors = error.response?.data?.errors
    notify('error', errors ? Object.values(errors).flat()[0] : (error.response?.data?.message || t('block_save_failed')))
  } finally {
    saving.value = false
  }
}

async function updateBookingStatus(id, status) {
  if (!props.canUpdateBookings || !props.statusUrlTemplate) return
  try {
    const { data } = await axios.patch(props.statusUrlTemplate.replace('__BOOKING__', id), { status })
    if (activeBooking.value?.id === id) activeBooking.value = { ...activeBooking.value, ...(data.booking || {}), status }
    notify('success', t('status_changed', { status: STATUS[status]?.label || status }))
    requestRefetch()
  } catch (error) {
    notify('error', error.response?.data?.message || t('status_change_failed'))
  }
}

function confirmDeleteBooking(id) {
  if (!props.canDeleteBookings || !props.deleteUrlTemplate) return
  const booking = activeBooking.value
  activeBooking.value = null // the details dialog gives way to the confirmation
  confirmState.value = {
    title: t('delete_booking_title'),
    text: `${booking?.customer_name || ''} · ${booking ? longDate(booking.date) : ''} ${booking ? hm(booking.start_time) : ''}. ${t('delete_booking_text')}`,
    action: t('remove'),
    danger: true,
    run: async () => {
      try {
        await axios.delete(props.deleteUrlTemplate.replace('__BOOKING__', id))
        notify('success', t('booking_deleted'))
        requestRefetch()
      } catch (error) {
        notify('error', error.response?.data?.message || t('booking_delete_failed'))
      }
    },
  }
}

function handleEscape(event) {
  if (event.key === 'Escape') {
    if (confirmState.value) confirmState.value = null
    else if (pendingSlot.value && !showBookingModal.value && !showBlockModal.value) clearSelection()
  }
}

onMounted(() => document.addEventListener('keydown', handleEscape))
onBeforeUnmount(() => {
  document.removeEventListener('keydown', handleEscape)
  clearTimeout(searchDebounce)
  clearTimeout(refetchTimer)
})
</script>

<style>
/* Calendar layout (unscoped: FullCalendar renders outside Vue's scope attrs) */
.cal { display: flex; flex-direction: column; gap: 1rem; }
.cal-toolbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .75rem; }
.cal-toolbar-group { display: flex; flex-wrap: wrap; align-items: center; gap: .6rem; }
.cal-nav { display: inline-flex; border: 1px solid var(--line); border-radius: .6rem; background: var(--card); }
.cal-nav .icon-btn { width: 2rem; height: 2rem; color: var(--ink-2); }
.cal-nav .icon-btn:hover { background: var(--paper-2); }
.cal-title { margin: 0; font-size: 1.15rem; font-weight: 700; letter-spacing: -.01em; text-transform: capitalize; }
.cal-search .input { min-width: 15rem; }
.cal-views { display: inline-flex; padding: .2rem; border: 1px solid var(--line); border-radius: .65rem; background: var(--card); }
.cal-view { border: 0; background: transparent; padding: .35rem .75rem; border-radius: .45rem; font: inherit; font-size: .82rem; font-weight: 600; color: var(--muted); cursor: pointer; }
.cal-view.is-active { background: var(--ink); color: #fff; }
.cal-rail-toggle { display: none; }
.cal-body { display: grid; grid-template-columns: 16rem minmax(0, 1fr); gap: 1.25rem; align-items: start; }
.cal-rail { display: flex; flex-direction: column; gap: .75rem; position: sticky; top: 4.75rem; }
.cal-rail-section { background: var(--card); border: 1px solid var(--line); border-radius: var(--radius-lg); padding: .9rem 1rem; }
.cal-rail-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: .5rem; }
.cal-rail-head h3 { margin: 0; font-size: .8rem; font-weight: 700; color: var(--ink-2); }
.cal-check { display: flex; align-items: center; gap: .55rem; padding: .3rem 0; font-size: .85rem; color: var(--ink-2); cursor: pointer; }
.cal-check input { width: 1rem; height: 1rem; accent-color: var(--gold); }
.cal-check-label { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.cal-chips { display: flex; flex-wrap: wrap; gap: .4rem; }
.cal-chip { cursor: pointer; opacity: .55; }
.cal-chip.is-active { opacity: 1; background: var(--card); border-color: #d9d0c2; }
.cal-legend { display: flex; flex-direction: column; gap: .35rem; margin-top: .6rem; font-size: .78rem; color: var(--muted); }
.legend-swatch { display: inline-block; width: .9rem; height: .9rem; border-radius: .25rem; vertical-align: -2px; margin-right: .4rem; border: 1px solid var(--line); }
.legend-swatch.is-block { background: repeating-linear-gradient(135deg, #efe9df 0 4px, #e2dacb 4px 6px); }
.legend-swatch.is-holiday { background: rgba(179, 38, 30, .1); }
.legend-swatch.is-off { background: #f3efe8; }
.cal-main { min-width: 0; background: var(--card); border: 1px solid var(--line); border-radius: var(--radius-lg); padding: .75rem; }

/* mini calendar */
.mini-head { display: grid; grid-template-columns: 2rem 1fr 2rem; align-items: center; margin-bottom: .4rem; }
.mini-head .icon-btn { width: 2rem; height: 2rem; color: var(--muted); }
.mini-head .icon-btn:hover { background: var(--paper-2); color: var(--ink); }
.mini-title { text-align: center; font-size: .82rem; font-weight: 600; text-transform: capitalize; }
.mini-weekdays, .mini-grid { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); }
.mini-weekdays span { text-align: center; font-size: .66rem; color: var(--muted-2); padding-bottom: .25rem; }
.mini-day { position: relative; display: grid; place-items: center; height: 1.9rem; border: 0; border-radius: 999px; background: transparent; font: inherit; font-size: .74rem; color: var(--ink); cursor: pointer; font-variant-numeric: tabular-nums; }
.mini-day:hover { background: var(--gold-soft); }
.mini-day.is-outside { color: var(--muted-2); }
.mini-day.is-today > span { font-weight: 700; color: var(--gold-ink); }
.mini-day.is-selected { background: var(--ink); color: #fff; }
.mini-day.is-selected > span { color: #fff; }
.mini-marker { position: absolute; bottom: .18rem; width: .25rem; height: .25rem; border-radius: 999px; background: var(--gold); }
.mini-day.is-selected .mini-marker { background: #fff; }

/* slot sheet + toasts */
.slot-sheet { position: fixed; left: 50%; bottom: 1.25rem; transform: translateX(-50%); z-index: 45; display: flex; align-items: center; gap: 1.25rem; padding: .8rem 1rem .8rem 1.2rem; background: var(--ink); color: #fff; border-radius: 1rem; box-shadow: var(--shadow-pop); max-width: calc(100vw - 2rem); }
.slot-sheet-info { display: flex; flex-direction: column; line-height: 1.25; }
.slot-sheet-info strong { font-size: 1rem; }
.slot-sheet-info span { font-size: .78rem; color: rgba(255,255,255,.65); text-transform: capitalize; }
.slot-sheet-actions { display: flex; gap: .4rem; }
.slot-sheet .btn-secondary { background: rgba(255,255,255,.1); border-color: transparent; color: #fff; }
.slot-sheet .btn-ghost { color: rgba(255,255,255,.7); }
.slot-sheet .btn-ghost:hover { background: rgba(255,255,255,.1); color: #fff; }
.sheet-enter-active, .sheet-leave-active { transition: transform .2s ease, opacity .2s ease; }
.sheet-enter-from, .sheet-leave-to { transform: translate(-50%, 10px); opacity: 0; }
.cal-toasts { position: fixed; right: 1rem; bottom: 1rem; z-index: 70; display: flex; flex-direction: column; gap: .5rem; max-width: min(24rem, 90vw); }
.cal-toast { padding: .7rem 1rem; border-radius: .75rem; font-size: .85rem; font-weight: 600; color: #fff; background: var(--ink); box-shadow: var(--shadow-pop); }
.cal-toast.is-success { background: var(--ok); }
.cal-toast.is-error { background: var(--bad); }
.toast-enter-active, .toast-leave-active { transition: transform .2s ease, opacity .2s ease; }
.toast-enter-from, .toast-leave-to { transform: translateY(8px); opacity: 0; }

/* FullCalendar theme */
.cal .fc { font-family: var(--font); --fc-border-color: var(--line-2); --fc-page-bg-color: var(--card); --fc-neutral-bg-color: var(--paper); --fc-today-bg-color: var(--gold-tint); --fc-now-indicator-color: var(--bad); --fc-non-business-color: rgba(244, 239, 230, .7); --fc-highlight-color: rgba(var(--gold-rgb), .18); --fc-event-border-color: transparent; }
.cal .fc .fc-col-header-cell-cushion { padding: .55rem .25rem; font-size: .78rem; font-weight: 600; color: var(--muted); text-decoration: none; text-transform: capitalize; }
.cal .fc .fc-day-today .fc-col-header-cell-cushion { color: var(--gold-ink); }
.cal .fc .fc-timegrid-slot { height: 2.1rem; }
.cal .fc .fc-timegrid-event-harness { margin-right: 2px; container-type: inline-size; }
/* Narrow (side-by-side) appointments show just the client's name; details live in the popover. */
@container (max-width: 90px) {
  .ev .ev-time, .ev .ev-service, .ev .ev-worker { display: none; }
  .ev .ev-name { font-size: .7rem; line-height: 1.15; -webkit-line-clamp: 3; text-overflow: clip; overflow-wrap: anywhere; }
}
.cal .fc .fc-timegrid-more-link { font-size: .72rem; font-weight: 600; color: var(--ink); background: var(--paper); border: 1px solid var(--line); border-radius: .5rem; padding: .15rem .35rem; }
.cal .fc .fc-timegrid-event .ev-name { white-space: normal; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
.cal .fc .fc-timegrid-slot-label-cushion, .cal .fc .fc-daygrid-day-number { font-size: .74rem; color: var(--muted); font-variant-numeric: tabular-nums; text-decoration: none; }
.cal .fc .fc-daygrid-day-number { padding: .4rem .5rem; font-weight: 600; color: var(--ink-2); }
.cal .fc .fc-day-other .fc-daygrid-day-number { color: var(--muted-2); }
.cal .fc .fc-timegrid-axis-cushion { font-size: .72rem; color: var(--muted-2); }
.cal .fc .fc-scrollgrid { border-radius: .75rem; overflow: hidden; }
.cal .fc .fc-event { border-radius: .55rem; border: 0; border-left: 3px solid var(--fc-event-border-color); padding: .15rem .45rem; font-size: .76rem; box-shadow: none; cursor: pointer; }
.cal .fc .fc-event.fc-event-draggable { cursor: grab; }
.cal .fc .fc-event.fc-event-dragging { cursor: grabbing; box-shadow: var(--shadow-pop); }
.cal .fc .fc-daygrid-event { border-left-width: 3px; margin: .1rem .25rem; }
/* Month rows carry their colour in the dot, not in a left stripe. */
.cal .fc .fc-daygrid-dot-event { border-left: 0; padding: .1rem .35rem; }
.cal .fc .fc-timegrid-event .fc-event-main { padding: 0; }
.cal .fc .fc-event.ev-booking { border-left-color: inherit; }
.cal .fc .fc-event.is-pending { border-left-style: dashed; }
.cal .fc .fc-event.is-cancelled { opacity: .6; }
.cal .fc .fc-event.is-cancelled .ev-name { text-decoration: line-through; }
.cal .fc .fc-event.is-completed .ev-name::after { content: ' ✓'; color: var(--ok); }
.cal .fc .fc-event.ev-block { background: repeating-linear-gradient(135deg, #efe9df 0 6px, #e6dfd2 6px 8px) !important; border-left-color: #b8ad9c; cursor: pointer; }
.cal .fc .fc-bg-event.ev-holiday { opacity: 1; }
.cal .fc .fc-highlight { border-radius: .5rem; }
.cal .ev { display: flex; flex-direction: column; gap: .05rem; line-height: 1.25; overflow: hidden; }
.cal .ev-time { font-size: .68rem; font-weight: 600; opacity: .75; font-variant-numeric: tabular-nums; }
.cal .ev-name { font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.cal .ev-service { font-size: .7rem; opacity: .8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.cal .ev-worker { display: inline-flex; align-items: center; gap: .3rem; font-size: .68rem; opacity: .8; margin-top: .1rem; }
.cal .ev-worker i { width: .45rem; height: .45rem; border-radius: 999px; display: inline-block; }
.cal .fc .fc-daygrid-event .ev { flex-direction: row; gap: .35rem; align-items: baseline; }
.cal .ev-dot { width: .45rem; height: .45rem; border-radius: 999px; flex-shrink: 0; align-self: center; }
.cal .fc .fc-more-link { font-size: .72rem; color: var(--gold-ink); font-weight: 600; }
.cal .fc .fc-timegrid-now-indicator-line { border-width: 2px 0 0; }
.cal .fc .fc-popover { border-radius: .75rem; border-color: var(--line); box-shadow: var(--shadow-pop); }
.cal .fc .fc-popover-header { background: var(--paper); font-size: .8rem; }

@media (max-width: 1023px) {
  .cal-body { grid-template-columns: 1fr; }
  .cal-rail { display: none; position: static; }
  .cal-rail.is-open { display: flex; }
  .cal-rail-toggle { display: inline-flex; }
  .cal-mini { display: none; }
  .cal-search .input { min-width: 0; width: 100%; }
  .slot-sheet { flex-direction: column; align-items: stretch; left: 1rem; right: 1rem; transform: none; gap: .75rem; }
  .slot-sheet-actions { flex-wrap: wrap; }
  .slot-sheet-actions .btn { flex: 1; }
  .sheet-enter-from, .sheet-leave-to { transform: translateY(10px); }
}

/* Phones: the grid is narrow, so appointments show only what fits and the toolbar stacks. */
@media (max-width: 767px) {
  .cal-toolbar-group { width: 100%; }
  .cal-toolbar .btn-primary { width: 100%; }
  .cal-title { font-size: 1.05rem; }
  .cal .fc .fc-col-header-cell-cushion { font-size: .72rem; padding: .4rem .1rem; }
  .cal .fc .fc-timegrid-slot-label-cushion { font-size: .68rem; }
  .cal .fc .fc-timegrid-event .ev-service,
  .cal .fc .fc-timegrid-event .ev-worker { display: none; }
  .cal .fc .fc-timeGridWeek-view .ev-time,
  .cal .fc .fc-timeGrid3-view .ev-time { display: none; }
  .cal .fc .fc-timeGridWeek-view .ev-name,
  .cal .fc .fc-timeGrid3-view .ev-name { font-size: .7rem; line-height: 1.15; white-space: normal; }
  /* Month: one dot per appointment in the worker's colour, details on tap. */
  .cal .fc .fc-daygrid-event { padding: 0; margin: .1rem .15rem; border: 0; background: transparent !important; }
  .cal .fc .fc-daygrid-event .ev > :not(.ev-dot) { display: none; }
  .cal .fc .fc-daygrid-event .ev-dot { width: .6rem; height: .6rem; }
  .cal .fc .fc-daygrid-day-events { display: flex; flex-wrap: wrap; justify-content: center; gap: .1rem; padding: .1rem; }
  .cal .fc .fc-daygrid-day-bottom { width: 100%; text-align: center; }
  .cal .fc .fc-more-link { font-size: .62rem; }
}
</style>
