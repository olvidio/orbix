---
id: "shared.pantalla.tablaDB_lista_ver"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "shared"
nombre: "Mantenimiento genérico de tablas (listado)"
controller: "frontend/shared/controller/tablaDB_lista_ver.php"
vistas: ["frontend/shared/view/tablaDB_busqueda.phtml", "frontend/shared/view/tablaDB_lista_ver.phtml"]
fragmentos_frontend: []
endpoints: ["/src/shared/tablaDB_buscar_datos", "/src/shared/tablaDB_lista_datos"]
capacidades: ["shared.tablaDB_buscar.gestionar", "shared.tablaDB_lista.gestionar"]
campos: ["form.sel", "html.btn_new", "html.k_buscar", "html.mod", "post.aSerieBuscar", "post.clase_info", "post.id_pau", "post.id_sel", "post.k_buscar", "post.mod", "post.obj_pau", "post.pau", "post.permiso", "post.refresh", "post.scroll_id", "post.sel"]
acciones: ["fnjs_enviar", "fnjs_enviar_formulario", "fnjs_nuevo"]
estado_revision: "revisado"
---

# Mantenimiento genérico de tablas (listado)

Shell transversal de listado CRUD: formulario de búsqueda (si aplica) + tabla editable con selección,
botón «nuevo» (solo `permiso === 3`) y acciones de fila definidas por cada `Info*`.

## Tipo

- Subtipo: `pantalla_principal` (destino de muchas entradas de menú con distinto `clase_info`)
- Controller: `frontend/shared/controller/tablaDB_lista_ver.php`

## Flujo en pantalla

1. Sin `k_buscar`/`aSerieBuscar` → carga `tablaDB_buscar_datos` y muestra `tablaDB_busqueda.phtml`
   (o vista custom del `Info*`).
2. Tras buscar → `tablaDB_lista_datos` y `tablaDB_lista_ver.phtml` con `Lista`.
3. «Nuevo» / editar fila → navega a `tablaDB_formulario_ver.php` con `mod` y `sel`.

Parámetro clave: `clase_info` (clase `Info*` del dominio, URL-encoded).

## Endpoints Usados

- `/src/shared/tablaDB_buscar_datos`
- `/src/shared/tablaDB_lista_datos`

## Manual De Usuario

Pantalla revisada contra `frontend/shared/`. El título y columnas dependen del `Info*` enlazado desde
el menú (asignaturas, ubis, inventario, procesos, etc.).

## Ruta de menú

Sin entrada única: cada mantenimiento de tabla apunta a esta URL con distinto `clase_info`. Índice en
`docs/guias/_referencia_menus.md` (filas `tablaDB_lista_ver.php`). Ejemplos:

- **Legacy:** `global > estudios > asignaturas` · `sistema > Configuración > aplicaciones` · `scdl > Inventario > colecciones`
- **Pills2:** `ADMIN GLOBAL > estudios > asignaturas` · `ADMIN GLOBAL > Configuración > aplicaciones` · (inventario scdl sin Pills2 en índice)

Ver ~25 variantes documentadas en la referencia de menús.
