/**
 * Opens `a[data-remote]` in the modal and submits the form it contains without leaving the page.
 *
 * The server serves the same routes either way: an `X-Requested-With` request renders the bare
 * fragment, so nothing here needs a dedicated endpoint.
 */
const AJAX = { 'X-Requested-With': 'XMLHttpRequest' };

const modal = document.getElementById('modal');
const modalBody = document.getElementById('modal-body');
const pageContent = document.getElementById('page-content');

document.addEventListener('click', async (event) => {
    if (event.target.closest('[data-close-modal]')) {
        modal.close();

        return;
    }

    const link = event.target.closest('a[data-remote]');

    if (!link || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey) {
        return;
    }

    event.preventDefault();

    const response = await fetch(link.href, { headers: AJAX });

    modalBody.innerHTML = await response.text();
    modal.showModal();
});

// Clicking the backdrop lands on the dialog itself, never on anything inside it.
modal.addEventListener('click', (event) => {
    if (event.target === modal) {
        modal.close();
    }
});

document.addEventListener('submit', async (event) => {
    const form = event.target;

    if (!modal.contains(form)) {
        return;
    }

    event.preventDefault();

    const response = await fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: { ...AJAX, Accept: 'application/json' },
    });

    if (response.status === 422) {
        showErrors(form, Object.values((await response.json()).errors).flat());

        return;
    }

    modal.close();
    pageContent.innerHTML = await (await fetch(window.location.href, { headers: AJAX })).text();
});

function showErrors(form, messages) {
    const box = form.querySelector('#alert-box');

    box.querySelector('ul').replaceChildren(...messages.map((message) => {
        const item = document.createElement('li');
        item.textContent = message;

        return item;
    }));

    box.classList.remove('hidden');
}
