import { t, pageLocale } from './i18n'

export const pad = (n) => String(n).padStart(2, '0')

export function formatDate(date) {
  return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`
}

export function formatTime(date) {
  return `${pad(date.getHours())}:${pad(date.getMinutes())}`
}

export function parseDate(iso) {
  const [y, m, d] = iso.split('-').map(Number)
  return new Date(y, m - 1, d)
}

/** "9:00" | "09:00" | "09:00:00" -> "09:00" */
export function hm(time = '') {
  const [h = '0', m = '0'] = String(time).split(':')
  return `${pad(Number(h))}:${pad(Number(m))}`
}

/** "09:00" -> "09:00:00" (FullCalendar wants seconds) */
export function hms(time = '') {
  return `${hm(time)}:00`
}

export function addMinutes(time, minutes) {
  const [h, m] = hm(time).split(':').map(Number)
  const total = h * 60 + m + Number(minutes || 0)
  return `${pad(Math.floor(total / 60) % 24)}:${pad(total % 60)}`
}

export function hexToRgba(hex, alpha = 1) {
  const clean = String(hex || '').replace('#', '')
  const full = clean.length === 3 ? clean.split('').map(c => c + c).join('') : clean
  const int = parseInt(full || 'c19a3e', 16)
  return `rgba(${(int >> 16) & 255}, ${(int >> 8) & 255}, ${int & 255}, ${alpha})`
}

export function longDate(iso, locale = pageLocale()) {
  return parseDate(iso).toLocaleDateString(locale, { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })
}

export const STATUS = {
  pending: { label: t('status_pending'), color: '#b7791f' },
  confirmed: { label: t('status_confirmed'), color: '#2e7d5b' },
  completed: { label: t('status_completed'), color: '#4a4136' },
  cancelled: { label: t('status_cancelled'), color: '#b3261e' },
}
