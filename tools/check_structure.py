"""Comprueba referencias HTML locales sin ejecutar PHP ni acceder a MySQL."""
from pathlib import Path
import re
import sys
from urllib.parse import unquote, urlsplit

ROOT = Path(__file__).resolve().parents[1]
PUBLIC = ROOT / 'public'
errors = []
checked = 0
for page in sorted(PUBLIC.iterdir()):
    if page.suffix not in {'.html', '.php'}:
        continue
    source = page.read_text()
    for value in re.findall(r'''(?:src|href|action)\s*=\s*["']([^"']*)["']''', source):
        if not value or '<?' in value or value.startswith(('#', '//')):
            continue
        url = urlsplit(value)
        if url.scheme or not url.path:
            continue
        target = PUBLIC / unquote(url.path.lstrip('/'))
        checked += 1
        if not target.exists():
            errors.append(f'{page.name}: {value}')
if not (ROOT / 'config/database.php').exists():
    errors.append('Falta config/database.php; copiar config/database.example.php y configurar MySQL.')
for error in errors:
    print(f'ERROR: {error}')
print(f'{checked} referencias estáticas comprobadas; {len(errors)} errores.')
sys.exit(bool(errors))
