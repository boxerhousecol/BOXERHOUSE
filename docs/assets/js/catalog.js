(() => {
  'use strict';
  const key = 'boxerhouse-seleccion-v1';
  const products = JSON.parse(document.getElementById('catalog-data').textContent);
  let cart = [];
  try {
    const stored = JSON.parse(localStorage.getItem(key) || '[]');
    if (Array.isArray(stored)) cart = stored.filter(item => products.some(p => p.id === item.id) && Number.isInteger(item.quantity) && item.quantity > 0 && item.quantity <= 99);
  } catch (_) { /* El catálogo funciona aunque el almacenamiento esté bloqueado. */ }
  const list = document.getElementById('selection');
  const send = document.getElementById('send-order');
  function render() {
    try { localStorage.setItem(key, JSON.stringify(cart)); } catch (_) { /* Memoria de esta página. */ }
    list.replaceChildren();
    for (const item of cart) {
      const product = products.find(p => p.id === item.id);
      const row = document.createElement('li');
      const text = document.createElement('span');
      text.textContent = `${product.name} × ${item.quantity}`;
      const remove = document.createElement('button');
      remove.type = 'button';
      remove.textContent = 'Quitar';
      remove.setAttribute('aria-label', `Quitar ${product.name}`);
      remove.addEventListener('click', () => { cart = cart.filter(p => p.id !== item.id); render(); });
      row.append(text, remove);
      list.append(row);
    }
    if (!cart.length) list.textContent = 'Todavía no has seleccionado productos.';
    const count = cart.reduce((sum, item) => sum + item.quantity, 0);
    document.querySelectorAll('#cartCount').forEach(el => { el.textContent = count; });
    send.hidden = !cart.length;
    const message = 'Hola BoxerHouse, quiero consultar precio y disponibilidad de:\n' + cart.map(item => `${products.find(p => p.id === item.id).name} × ${item.quantity}`).join('\n') + '\nPor favor, confirmen tallas, colores y envío.';
    send.href = 'https://wa.me/573009407144?text=' + encodeURIComponent(message);
  }
  document.querySelectorAll('[data-add]').forEach(button => {
    button.addEventListener('click', () => {
      const id = button.dataset.add;
      const item = cart.find(p => p.id === id);
      if (item) item.quantity = Math.min(99, item.quantity + 1);
      else cart.push({id, quantity: 1});
      render();
      document.getElementById('selection-status').textContent = `${products.find(p => p.id === id).name} añadido a tu selección.`;
    });
  });
  render();
})();
