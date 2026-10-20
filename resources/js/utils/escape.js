const HTML_ESCAPES = {
   '&': '&amp;',
   '<': '&lt;',
   '>': '&gt;',
   '"': '&quot;',
   "'": '&#39;',
   '`': '&#96;',
};

/**
 * Escape a value before interpolating it into an HTML template string.
 */
export function escapeHtml(value) {
   if (value === null || value === undefined) {
      return '';
   }

   return String(value).replace(/[&<>"'`]/g, (char) => HTML_ESCAPES[char]);
}
