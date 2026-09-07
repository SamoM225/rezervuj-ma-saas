/**
 * Translations for the staff calendar. The admin layout injects the current
 * language's strings as `window.__cal` (lang/<locale>/calendar.php); the key
 * itself is the fallback so the UI stays readable if the map is missing.
 *
 *   t('saving')                         -> 'Ukladám…'
 *   t('moved_to', { when: '5. 9. 10:00' }) -> 'Rezervácia presunutá na 5. 9. 10:00.'
 */
const strings = (typeof window !== 'undefined' && window.__cal) || {}

export function t(key, replacements = {}) {
  const text = strings[key] ?? key
  if (typeof text !== 'string') return text
  return Object.entries(replacements).reduce((out, [name, value]) => out.replaceAll(`:${name}`, String(value)), text)
}

/** BCP-47 tag of the page language, for date formatting and FullCalendar. */
export function pageLocale() {
  const lang = (typeof document !== 'undefined' && document.documentElement.lang) || 'sk'
  return { sk: 'sk-SK', cs: 'cs-CZ', en: 'en-GB' }[lang.slice(0, 2)] || lang
}
