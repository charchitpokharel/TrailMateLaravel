(() => {
  'use strict';
  const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content || '';
  const headers = (extra = {}) => ({'Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':csrf(), ...extra});
  const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  const showMessage = (el, message, type='info') => {
    if (!el) return;
    el.className = `alert alert-${type} mt-3`;
    el.textContent = message;
    el.hidden = false;
  };
  document.addEventListener('submit', async event => {
    const form = event.target.closest('[data-ajax-form]');
    if (!form) return;
    event.preventDefault();
    const message = form.querySelector('[data-form-message]') || document.querySelector('[data-global-message]');
    const submit = form.querySelector('[type="submit"]');
    if (submit) { submit.disabled = true; submit.dataset.originalText ||= submit.textContent; submit.textContent = 'Please wait…'; }
    try {
      const method = (form.dataset.method || form.method || 'POST').toUpperCase();
      const response = await fetch(form.action, {method, headers: headers(), body: new FormData(form)});
      const data = await response.json().catch(() => ({}));
      if (!response.ok) {
        const errors = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Request failed. Please check your input.');
        showMessage(message, errors, 'danger');
        return;
      }
      if (data.redirect) { window.location.assign(data.redirect); return; }
      showMessage(message, data.message || 'Saved successfully.', 'success');
      if (form.dataset.resetOnSuccess === 'true') form.reset();
      if (data.htmlTarget && data.html) document.querySelector(data.htmlTarget).innerHTML = data.html;
      if (data.refreshTarget) {
        const target = document.querySelector(data.refreshTarget);
        if (target) target.dispatchEvent(new CustomEvent('trailmate:refresh'));
      }
      form.dispatchEvent(new CustomEvent('trailmate:success', {detail:data}));
    } catch (_) {
      showMessage(message, 'Network error. Please check your connection and try again.', 'danger');
    } finally {
      if (submit) { submit.disabled = false; submit.textContent = submit.dataset.originalText || 'Submit'; }
    }
  });

  document.addEventListener('click', async event => {
    const button = event.target.closest('[data-ajax-delete]');
    if (!button) return;
    event.preventDefault();
    if (!window.confirm(button.dataset.confirm || 'Are you sure you want to delete this item?')) return;
    button.disabled = true;
    try {
      const response = await fetch(button.dataset.url, {method:'DELETE',headers:headers({'Content-Type':'application/json'})});
      const data = await response.json().catch(() => ({}));
      if (!response.ok) throw new Error(data.message || 'Could not delete item.');
      const card = button.closest('[data-item-card]');
      if (card) card.remove();
      showMessage(document.querySelector('[data-global-message]'), data.message || 'Deleted.', 'success');
      if (data.redirect) window.location.assign(data.redirect);
    } catch (error) {
      showMessage(document.querySelector('[data-global-message]'), error.message, 'danger');
    } finally { button.disabled = false; }
  });

  // All AJAX-rendered text is escaped before insertion; links are built from route prefixes and numeric IDs.
  window.TrailMate = {escapeHtml, headers, showMessage};
})();
