# BoxerHouse

Tienda web en PHP, HTML, CSS y JavaScript con base de datos MySQL.

## Organización

```text
public/                  Raíz pública del sitio: páginas y acciones PHP
  assets/
    css/style.css        Estilos generales existentes
    css/pages/           Estilos específicos extraídos de páginas PHP
    js/main.js           JavaScript compartido
    js/pages/            JavaScript específico sin dependencias PHP
    images/branding/     Logotipos
  imagenes/              Fotografías de productos
  img/                   Imágenes adicionales existentes
config/
  database.php           Conexión local (excluida de Git)
  database.example.php   Plantilla de conexión
docs/                    Versión estática generada para GitHub Pages
documentation/           Notas de mantenimiento
site/                    Catálogo y comportamiento de la versión estática
 tools/                  Comprobaciones del proyecto
```

## Ejecutar en desarrollo

1. Instalar PHP con la extensión `mysqli` y disponer de MySQL.
2. En esta copia ya se conserva la conexión original en `config/database.php`. Para otra instalación, copiar `config/database.example.php` a `config/database.php` y ajustar la conexión. La plantilla acepta `DB_HOST`, `DB_USER`, `DB_PASSWORD` y `DB_NAME` desde el entorno; no carga archivos `.env` automáticamente.
3. Importar la base de datos existente. El proyecto original no incluye un archivo SQL ni migraciones.
4. Desde la raíz del proyecto ejecutar:

```sh
php -S localhost:8000 -t public
```

Abrir http://localhost:8000. Abrir directamente los HTML no permite utilizar las páginas PHP ni el carrito.

En Apache/Nginx configurar la raíz del sitio en `public/`; `config/` debe quedar fuera de la carpeta pública.

## Comprobar cambios

```sh
python3 tools/check_structure.py
find public config -name '*.php' -exec php -l {} \;
```

Consultar [las notas de mantenimiento](documentation/ORGANIZACION.md) antes de mover imágenes o modificar estilos compartidos.

## Publicar en GitHub Pages

```sh
python3 tools/build_pages.py
```

En GitHub → Settings → Pages elegir **Deploy from a branch**, rama `main`, carpeta `/docs`. La versión estática incluye catálogo y selección para consultar por WhatsApp. No consulta MySQL ni registra pedidos. Los precios y existencias se confirman con la marca. El código PHP permanece en `public/` para un servidor compatible.

Después de modificar las páginas o sus recursos, volver a generar `docs/` y subir los cambios. No copiar `config/database.php` a GitHub.
