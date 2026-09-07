<template>
  <Teleport to="body">
    <div class="modal-backdrop" @click.self="$emit('close')">
      <div class="modal" role="dialog" aria-modal="true" aria-labelledby="bl-title">
        <div class="modal-head">
          <div>
            <h3 class="modal-title" id="bl-title">{{ t('block_time') }}</h3>
            <p class="card-sub">{{ t('block_hint') }}</p>
          </div>
          <button type="button" class="icon-btn" :aria-label="t('close')" @click="$emit('close')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/></svg>
          </button>
        </div>

        <form @submit.prevent="handleSubmit">
          <div class="modal-body">
            <div class="form-grid">
              <div class="field span-2" v-if="canPickWorker">
                <label for="bl-worker">{{ t('worker') }}</label>
                <select id="bl-worker" v-model="form.worker_id" class="select" required>
                  <option value="">{{ t('select_worker') }}</option>
                  <option v-for="worker in workers" :key="worker.id" :value="worker.id">{{ worker.name }}</option>
                </select>
              </div>

              <div class="field">
                <label for="bl-date">{{ t('from_date') }}</label>
                <input id="bl-date" v-model="form.date" type="date" class="input" required>
              </div>

              <div class="field">
                <label for="bl-end-date">{{ t('to_date') }}</label>
                <input id="bl-end-date" v-model="form.end_date" type="date" class="input" :min="form.date" required>
                <p class="hint">{{ t('same_date_hint') }}</p>
              </div>

              <label class="switch span-2" style="border:0;padding:.25rem 0">
                <span>{{ t('all_day') }} <p class="hint">{{ t('all_day_hint') }}</p></span>
                <input type="checkbox" v-model="form.all_day">
              </label>

              <div class="field" v-show="!form.all_day">
                <label for="bl-start">{{ t('from') }}</label>
                <input id="bl-start" v-model="form.start_time" type="time" class="input" :required="!form.all_day" step="300">
              </div>

              <div class="field" v-show="!form.all_day">
                <label for="bl-end">{{ t('to') }}</label>
                <input id="bl-end" v-model="form.end_time" type="time" class="input" :required="!form.all_day" step="300">
                <p v-if="form.start_time && form.end_time && form.end_time <= form.start_time" class="hint is-error">{{ t('end_after_start') }}</p>
              </div>

              <div class="field span-2">
                <label for="bl-title">{{ t('reason') }}</label>
                <input id="bl-title" v-model.trim="form.title" type="text" class="input" :placeholder="t('reason_placeholder')">
              </div>

              <div class="field span-2">
                <label for="bl-notes">{{ t('note') }}</label>
                <textarea id="bl-notes" v-model="form.notes" rows="2" class="textarea" style="min-height:3.5rem" :placeholder="t('optional')"></textarea>
              </div>
            </div>
          </div>

          <div class="modal-foot">
            <button type="button" class="btn btn-secondary" @click="$emit('close')">{{ t('cancel') }}</button>
            <button type="submit" class="btn btn-primary" :disabled="!isValid || saving">{{ saving ? t('saving') : (form.end_date !== form.date ? t('block_days') : t('block_time')) }}</button>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { computed, reactive, watch } from 'vue'
import { t } from '../calendar/i18n'

const props = defineProps({
  slot: { type: Object, required: true },
  workers: { type: Array, default: () => [] },
  defaultWorkerId: { type: [String, Number], default: '' },
  canPickWorker: { type: Boolean, default: true },
  saving: { type: Boolean, default: false },
})

const emit = defineEmits(['close', 'create'])

const form = reactive({
  worker_id: props.defaultWorkerId || (props.workers.length === 1 ? props.workers[0].id : ''),
  date: props.slot.date,
  end_date: props.slot.endDate || props.slot.date,
  all_day: Boolean(props.slot.allDay),
  start_time: props.slot.startTime,
  end_time: props.slot.endTime,
  title: '',
  notes: '',
})

const isValid = computed(() => form.date && form.end_date && form.end_date >= form.date
  && (form.all_day || (form.start_time && form.end_time && form.end_time > form.start_time))
  && (props.canPickWorker ? form.worker_id : true))

watch(() => props.slot, slot => {
  form.date = slot.date
  form.end_date = slot.endDate || slot.date
  form.all_day = Boolean(slot.allDay)
  form.start_time = slot.startTime
  form.end_time = slot.endTime
})
watch(() => props.defaultWorkerId, id => { if (id) form.worker_id = id })

function handleSubmit() {
  if (!isValid.value) return
  emit('create', {
    worker_id: props.canPickWorker ? form.worker_id : props.defaultWorkerId,
    date: form.date,
    end_date: form.end_date,
    all_day: form.all_day,
    start_time: form.start_time,
    end_time: form.end_time,
    title: form.title || (form.all_day ? t('closed') : t('blocked')),
    notes: form.notes,
  })
}
</script>
