---
tipo: manual_usuario
modulo: shared
estado_revision: revisado_parcial
---

# Manual De Usuario - shared

CRUD generico **tablaDB**: mantenimiento tablas maestras via `frontend/shared/controller/tablaDB_lista_ver.php` + `tablaDB_formulario_ver.php`.

Parametro clave: `clase_info=src\<modulo>\domain\Info*` (ej. InfoZona, InfoEncargoTipo, InfoColeccion).

Usado desde menus de muchos modulos (inventario, encargossacd, profesores, menus…). No tiene flujo usuario unico: cada `Info*` es una pantalla distinta.

API: `/src/shared/tablaDB_*`.
