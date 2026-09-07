<template>
  <Teleport to="body">
    <div class="modal-backdrop" @click.self="$emit('close')">
      <div class="modal modal-lg" role="dialog" aria-modal="true" aria-labelledby="nb-title">
        <div class="modal-head">
          <div>
            <h3 class="modal-title" id="nb-title">{{ t('new_booking') }}</h3>
            <p class="card-sub">{{ t('customer_gets', { what: requireConfirmation ? t('pending_notice') : t('confirmation_email') }) }}</p>
          </div>
          <button type="button" class="icon-btn" :aria-label="t('close')" @click="$emit('close')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/></svg>
          </button>
        </div>

        <form @submit.prevent="handleSubmit">
          <div class="modal-body">
            <div class="form-grid">
              <div class="field">
                <label for="nb-service">{{ t('service') }}</label>
                <select id="nb-service" v-model="form.service_id" class="select" required @change="onServiceChange">
                  <option value="">{{ t('select_service') }}</option>
                  <option v-for="service in services" :key="service.id" :value="service.id">
                    {{ service.name }} · {{ service.duration }} min{{ priceLabel(service) }}
                  </option>
                </select>
              </div>

              <div class="field">
                <label for="nb-worker">{{ t('worker') }}</label>
                <select id="nb-worker" v-model="form.worker_id" class="select" required :disabled="workers.length === 1">
                  <option value="">{{ t('select_worker') }}</option>
                  <option v-for="worker in availableWorkers" :key="worker.id" :value="worker.id">{{ worker.name }}</option>
                </select>
                <p v-if="form.service_id && availableWorkers.length === 0" class="hint is-error">{{ t('nobody_provides') }}</p>
              </div>

              <div class="field">
                <label for="nb-date">{{ t('date') }}</label>
                <input id="nb-date" v-model="form.date" type="date" class="input" required :min="today">
              </div>

              <div class="field">
                <label for="nb-start">{{ t('start') }}</label>
                <div class="flex items-center gap-2">
                  <input id="nb-start" v-model="form.start_time" type="time" class="input" required step="300" style="max-width:9rem">
                  <span class="hint" style="white-space:nowrap">{{ t('until') }} <strong class="tabular">{{ endTime || '–' }}</strong></span>
                </div>
              </div>

              <div class="field">
                <label for="nb-name">{{ t('full_name') }}</label>
                <input id="nb-name" v-model.trim="form.customer_name" type="text" class="input" required autocomplete="off">
              </div>

              <div class="field">
                <label for="nb-phone">{{ t('phone') }}</label>
                <input id="nb-phone" v-model.trim="form.customer_phone" type="tel" class="input" required autocomplete="off">
              </div>

              <div class="field span-2">
                <label for="nb-email">{{ t('email') }}</label>
                <input id="nb-email" v-model.trim="form.customer_email" type="email" class="input" required autocomplete="off">
              </div>

              <div class="field span-2">
                <label for="nb-notes">{{ t('team_note') }}</label>
                <textarea id="nb-notes" v-model="form.notes" rows="2" class="textarea" style="min-height:3.5rem" :placeholder="t('optional')"></textarea>
              </div>
            </div>
          </div>

          <div class="modal-foot">
            <button type="button" class="btn btn-secondary" @click="$emit('close')">{{ t('cancel') }}</button>
            <button type="submit" class="btn btn-primary" :disabled="!isFormValid || saving">{{ saving ? t('saving') : t('create_booking') }}</button>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { computed, reactive, watch } from 'vue'
import { t } from '../calendar/i18n'
import { addMinutes, formatDate } from '../calendar/utils'

const props = defineProps({
  selectedDate: { type: String, required: true },
  selectedTime: { type: String, default: '09:00' },
  workers: { type: Array, default: () => [] },
  services: { type: Array, default: () => [] },
  defaultWorkerId: { type: [String, Number], default: '' },
  requireConfirmation: { type: Boolean, default: false },
  saving: { type: Boolean, default: false },
})

const emit = defineEmits(['close', 'create'])

const today = formatDate(new Date())

const form = reactive({
  date: props.selectedDate,
  start_time: props.selectedTime,
  service_id: props.services.length === 1 ? props.services[0].id : '',
  worker_id: props.defaultWorkerId || (props.workers.length === 1 ? props.workers[0].id : ''),
  customer_name: '',
  customer_email: '',
  customer_phone: '',
  notes: '',
})

const selectedService = computed(() => props.services.find(s => String(s.id) === String(form.service_id)))
const endTime = computed(() => selectedService.value && form.start_time ? addMinutes(form.start_time, selectedService.value.duration) : '')

/** Only people who actually offer the chosen service can be booked for it. */
const availableWorkers = computed(() => {
  if (!form.service_id) return props.workers
  return props.workers.filter(w => !Array.isArray(w.service_ids) || w.service_ids.map(String).includes(String(form.service_id)))
})

const isFormValid = computed(() => form.date && form.start_time && form.service_id && form.worker_id
  && form.customer_name && form.customer_email && form.customer_phone)

watch(() => props.selectedDate, value => { form.date = value })
watch(() => props.selectedTime, value => { form.start_time = value })
watch(() => props.defaultWorkerId, value => { if (value) form.worker_id = value })

function onServiceChange() {
  if (form.worker_id && !availableWorkers.value.some(w => String(w.id) === String(form.worker_id))) {
    form.worker_id = availableWorkers.value.length === 1 ? availableWorkers.value[0].id : ''
  } else if (!form.worker_id && availableWorkers.value.length === 1) {
    form.worker_id = availableWorkers.value[0].id
  }
}

function priceLabel(service) {
  const price = Number(service.price)
  return Number.isFinite(price) && price > 0 ? ` · ${price.toLocaleString('sk-SK', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} €` : ''
}

function handleSubmit() {
  if (!isFormValid.value) return
  emit('create', {
    date: form.date,
    start_time: form.start_time,
    service_id: form.service_id,
    worker_id: form.worker_id,
    customer_name: form.customer_name,
    customer_email: form.customer_email,
    customer_phone: form.customer_phone,
    notes: form.notes,
  })
}
</script>
