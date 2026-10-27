# Organización y mantenimiento

La versión organizada está en esta carpeta de trabajo. Los archivos del Escritorio se conservaron sin cambios.

- Las páginas y acciones mantienen sus nombres y rutas entre sí dentro de `public/`.
- La conexión se carga con una ruta absoluta basada en `__DIR__`, desde `config/database.php`.
- Los logotipos, CSS y JavaScript compartido están en `public/assets/`.
- Los estilos insertados en carrito y detalles de producto se extrajeron a `assets/css/pages/`, conservando su posición de carga y el orden de las reglas.
- El JavaScript del detalle clásico se extrajo a `assets/js/pages/`. Los scripts de carrito y detalle general siguen junto a sus páginas porque contienen datos generados por PHP.
- Se corrigieron los enlaces a `productos.html`, que no existía, para apuntar a `productos.php`.
- Se conservaron `imagenes/` e `img/`: las rutas de fotografías pueden estar almacenadas en MySQL. Cambiarlas requiere revisar y migrar esos registros.
- Se conservó el orden del CSS general: contiene reglas repetidas y sobreescrituras cuya eliminación requiere una revisión visual de todas las páginas.

## Límites de verificación

El original no contiene la base de datos ni su esquema. La compra, el catálogo dinámico y los pedidos necesitan probarse con esa base y un servidor PHP. La comprobación de estructura revisa referencias estáticas locales; no evalúa PHP, JavaScript ni rutas procedentes de MySQL.
