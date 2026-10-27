"""Genera docs/ para GitHub Pages sin publicar PHP ni configuración."""
from pathlib import Path
import html
import json
import re
import shutil

ROOT = Path(__file__).resolve().parents[1]
PUBLIC = ROOT / 'public'
OUTPUT = ROOT / 'docs'
OUTPUT.mkdir(exist_ok=True)
for folder in ('assets', 'imagenes', 'img'):
    shutil.copytree(PUBLIC / folder, OUTPUT / folder, dirs_exist_ok=True)

def adapt(text):
    text = text.replace('productos.php', 'productos.html').replace('carrito.php', 'productos.html#seleccion')
    text = text.replace('href="#inicio"', 'href="index.html#inicio"')
    return text

for page in PUBLIC.glob('*.html'):
    source = adapt(page.read_text())
    source = source.replace('</body>', '<script src="assets/js/static.js"></script>\n</body>')
    if page.name == 'contacto.html':
        source = source.replace('Enviar mensaje', 'Continuar en WhatsApp')
    (OUTPUT / page.name).write_text(source)
(OUTPUT / 'assets/js/main.js').write_text(adapt((PUBLIC / 'assets/js/main.js').read_text()))
for filename, target in [('catalog.js', 'assets/js/catalog.js'), ('static.js', 'assets/js/static.js'), ('catalog.css', 'assets/css/catalog.css')]:
    shutil.copy2(ROOT / 'site' / filename, OUTPUT / target)

index = (PUBLIC / 'index.html').read_text()
header = adapt(re.search(r'<header\b.*?</header>', index, re.S)[0])
products = json.loads((ROOT / 'site/catalog.json').read_text())
cards = []
for product in products:
    name, image, identifier = [html.escape(product[key], quote=True) for key in ('name', 'image', 'id')]
    cards.append(f'''<article class="catalog-card"><img src="imagenes/{image}" alt="{name}" loading="lazy">
    <div class="catalog-card-content"><h2>{name}</h2><p>Consulta precio, tallas y colores disponibles.</p>
    <button type="button" data-add="{identifier}">Añadir a mi selección</button></div></article>''')
(OUTPUT / 'productos.html').write_text('''<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Productos | BOXER HOUSE</title>
<link rel="stylesheet" href="assets/css/style.css"><link rel="stylesheet" href="assets/css/catalog.css"></head><body>'''
    + header + '''<main class="catalog-page"><h1>Nuestra colección</h1>
<p class="catalog-intro">Elige tus favoritos y consulta tu pedido por WhatsApp. Te confirmamos precios, disponibilidad, tallas, colores y envío antes de comprar.</p>
<div class="catalog-grid">''' + ''.join(cards) + '''</div><p id="selection-status" role="status"></p>
<section class="catalog-selection" id="seleccion"><h2>Tu selección</h2><ul id="selection"></ul>
<p>Esta selección es una consulta; el pedido se confirma directamente con BoxerHouse por WhatsApp.</p>
<a id="send-order" class="order-link" target="_blank" rel="noopener noreferrer" hidden>Consultar por WhatsApp</a></section>
<noscript>Activa JavaScript para crear una selección o <a href="https://wa.me/573009407144">contáctanos por WhatsApp</a>.</noscript>
</main><footer class="footer"><p>© 2026 BOXER HOUSE</p></footer>
<script id="catalog-data" type="application/json">''' + json.dumps(products, ensure_ascii=False).replace('<', '\\u003c') + '''</script>
<script src="assets/js/main.js"></script><script src="assets/js/catalog.js"></script></body></html>''')
(OUTPUT / '.nojekyll').touch()
print('GitHub Pages preparado en docs/. PHP conservado en public/.')
