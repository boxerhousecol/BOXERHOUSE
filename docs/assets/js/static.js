document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('contactForm');
  if (form) form.addEventListener('submit', event => {
    event.preventDefault();
    if (!form.reportValidity()) return;
    const data = new FormData(form);
    const message = `Hola BoxerHouse, soy ${data.get('nombre')}.\nCorreo: ${data.get('correo')}\n${data.get('mensaje')}`;
    window.open('https://wa.me/573009407144?text=' + encodeURIComponent(message), '_blank', 'noopener,noreferrer');
  });
});
