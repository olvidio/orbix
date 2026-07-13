---
tipo: "manual_usuario"
modulo: "shared"
flujos: 6
estado_revision: "generado"
---

# Manual De Usuario - shared

Este manual es un borrador generado desde `docs/catalogo`. Debe revisarse para ajustar nombres de menu, permisos, validaciones y lenguaje final de usuario.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas de menu y nombres visibles pueden necesitar revision manual.

## Cargar locales/idiomas

### Para Que Sirve

Obtener la lista de idiomas activos para un desplegable en pantallas que lo necesitan (certificados, preferencias de usuario).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- `Ninguno documentado.`

### Permisos

- Sin control en el endpoint; la pantalla que lo invoca aplica sus permisos.

### Referencias Internas

- Flujo: `shared.locales_posibles.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/shared/flujos/locales_posibles.md`

## Persistir registro tabla genérica

### Para Que Sirve

Dar de alta, modificar o eliminar un registro en cualquier tabla mantenida con el patrón `Info*` + repositorio CRUD.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- `no se ha ejecutado la acción`
- `Errores de repositorio y validación de módulos (ver API tablaDB_update).`

### Permisos

- Sin `perm_*` en el controller; el nivel de edición lo fija el frontend (`permiso`, botón nuevo solo

### Referencias Internas

- Flujo: `shared.tablaDB.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/shared/flujos/tablaDB.md`

## Búsqueda previa al listado

### Para Que Sirve

Filtrar registros antes de mostrar la tabla en mantenimientos que definen criterios de búsqueda.

### Donde Entrar

- Mantenimiento genérico de tablas (listado) (frontend/shared/controller/tablaDB_lista_ver.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- `Ninguno documentado en el builder.`

### Permisos

- Sin control en el endpoint.

### Referencias Internas

- Flujo: `shared.tablaDB_buscar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/shared/flujos/tablaDB_buscar.md`

## Desplegable dependiente

### Para Que Sirve

- Actualizar las opciones de un campo hijo cuando cambia el valor del campo padre en un formulario `tablaDB` (p.
- ej.
- centro → lugar en inventario).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- `Error AJAX mostrado en alert con json.mensaje.`

### Permisos

- Sin control en el endpoint.

### Referencias Internas

- Flujo: `shared.tablaDB_depende.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/shared/flujos/tablaDB_depende.md`

## Formulario tabla genérica

### Para Que Sirve

Crear o modificar un registro en el mantenimiento genérico de tablas.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- `Mensajes de tablaDB_update en alert vía json.mensaje.`

### Permisos

- Sin control en el endpoint; edición condicionada en vista por `permiso` del listado.
- Sin `perm_*` en el controller; el nivel de edición lo fija el frontend (`permiso`, botón nuevo solo

### Referencias Internas

- Flujo: `shared.tablaDB_formulario.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/shared/flujos/tablaDB_formulario.md`

## Listar y mantener tabla genérica

### Para Que Sirve

Consultar y mantener registros de tablas de configuración enlazadas desde el menú (asignaturas, ubis, inventario, procesos, etc.) mediante el shell común `tablaDB`.

### Donde Entrar

- Mantenimiento genérico de tablas (listado) (frontend/shared/controller/tablaDB_lista_ver.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

Pendiente de revisar. No se han inferido tareas desde el flujo.

### Errores O Avisos Frecuentes

- `Errores de tablaDB_update (ver ficha API).`
- `Sin errores propios en builders de lista.`

### Permisos

- Sin control en el endpoint.
- Sin control en el endpoint; permisos y visibilidad del botón «nuevo» en frontend (`permiso`).
- Sin `perm_*` en el controller; el nivel de edición lo fija el frontend (`permiso`, botón nuevo solo

### Referencias Internas

- Flujo: `shared.tablaDB_lista.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/shared/flujos/tablaDB_lista.md`

## Revision Pendiente

- Sustituir nombres tecnicos por nombres visibles en la aplicacion.
- Completar rutas de menu.
- Confirmar permisos necesarios.
- Anadir capturas o ejemplos si se quiere publicar para usuarios finales.
