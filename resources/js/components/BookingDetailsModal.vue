<template>
  <Teleport to="body">
    <div class="modal-backdrop" @click.self="$emit('close')">
      <div class="modal" role="dialog" aria-modal="true" aria-labelledby="bd-title">
        <div class="modal-head">
          <div class="flex items-center gap-3" style="min-width:0">
            <span class="avatar" :style="{ background: tint, color: color }">{{ initial }}</span>
            <div style="min-width:0">
              <h3 class="modal-title" id="bd-title" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ booking.customer_name }}</h3>
              <p class="card-sub">{{ booking.service?.name || t('service_missing') }}</p>
            </div>
          </div>
          <button type="button" class="icon-btn" :aria-label="t('close')" @click="$emit('close')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/></svg>
          </button>
        </div>

        <div class="modal-body">
          <dl class="kv">
            <dt>{{ t('appointment') }}</dt>
            <dd>{{ longDate(booking.date) }}<br><span class="tabular">{{ hm(booking.start_time) }} – {{ hm(booking.end_time) }}</span></dd>

            <dt>{{ t('worker') }}</dt>
            <dd class="flex items-center gap-2"><span class="dot" :style="{ background: color }"></span>{{ booking.worker?.name || '–' }}</dd>

            <dt>{{ t('phone') }}</dt>
            <dd><a v-if="booking.customer_phone" class="link" :href="`tel:${booking.customer_phone.replace(/\s+/g, '')}`">{{ booking.customer_phone }}</a><span v-else>–</span></dd>

            <dt>{{ t('email') }}</dt>
            <dd><a v-if="booking.customer_email" class="link" :href="`mailto:${booking.customer_email}`">{{ booking.customer_email }}</a><span v-else>–</span></dd>

            <dt v-if="booking.service?.price">{{ t('price') }}</dt>
            <dd v-if="booking.service?.price" class="tabular">{{ price }}</dd>

            <dt v-if="booking.notes">{{ t('note') }}</dt>
            <dd v-if="booking.notes" style="white-space:pre-wrap">{{ booking.notes }}</dd>
          </dl>

          <div class="divider"></div>

          <div class="field">
            <label for="bd-status">{{ t('status') }}</label>
            <div v-if="canUpdate" class="flex flex-wrap gap-2">
              <button
                v-for="option in statusOptions"
                :key="option.value"
                type="button"
                :class="['chip', { 'is-current': option.value === booking.status }]"
                :style="option.value === booking.status ? { borderColor: option.color, color: option.color, background: '#fff' } : {}"
                :aria-pressed="option.value === booking.status"
                @click="option.value !== booking.status && $emit('status', booking.id, option.value)"
              >
                <span class="dot" :style="{ background: option.color }"></span>{{ option.label }}
              </button>
            </div>
            <span v-else class="badge" :class="badgeClass">{{ STATUS[booking.status]?.label || booking.status }}</span>
            <p class="hint">{{ t('drag_hint') }}</p>
          </div>
        </div>

        <div class="modal-foot">
          <button v-if="canDelete" type="button" class="btn btn-danger" style="margin-right:auto" @click="$emit('delete', booking.id)">{{ t('remove') }}</button>
          <button type="button" class="btn btn-secondary" @click="$emit('close')">{{ t('close') }}</button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { computed } from 'vue'
import { t } from '../calendar/i18n'
import { STATUS, hexToRgba, hm, longDate } from '../calendar/utils'

const props = defineProps({
  booking: { type: Object, required: true },
  canUpdate: { type: Boolean, default: false },
  canDelete: { type: Boolean, default: false },
  requireConfirmation: { type: Boolean, default: false },
})

defineEmits(['close', 'status', 'delete'])

const color = computed(() => props.booking.worker?.calendar_color || '#c19a3e')
const tint = computed(() => hexToRgba(color.value, 0.18))
const initial = computed(() => (props.booking.customer_name || '?').trim().charAt(0).toUpperCase())
const price = computed(() => {
  const value = Number(props.booking.service?.price)
  return Number.isFinite(value) ? `${value.toLocaleString('sk-SK', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} €` : props.booking.service?.price
})
const statusOptions = computed(() => Object.entries(STATUS)
  .filter(([value]) => value !== 'pending' || props.requireConfirmation || props.booking.status === 'pending')
  .map(([value, meta]) => ({ value, ...meta })))
const badgeClass = computed(() => ({ pending: 'badge-warn', confirmed: 'badge-ok', completed: 'badge-muted', cancelled: 'badge-bad' }[props.booking.status] || 'badge-muted'))
</script>
