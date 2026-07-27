---
tipo: "manual_usuario"
modulo: "cartaspresentacion"
flujos: 6
estado_revision: "revisado_parcial"
---

# Manual De Usuario - cartaspresentacion

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## Carta Presentacion

### Para Que Sirve

Dar de alta, modificar o quitar los datos de presentación de un centro concreto.

### Donde Entrar

- Cartas Presentacion Form (frontend/cartaspresentacion/controller/cartas_presentacion_form.php)
- sin entrada de menú en el índice (subflujo de `cartas_presentacion` > modificar).

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `Faltan id_ubi o id_direccion`
- `No puede modificar datos de otra dl`
- `Carta de presentacion no encontrada`
- `Hay un error, no se ha guardado. / Hay un error, no se ha borrado.`

### Permisos

- Sin control de permisos propio en el caso de uso; la acción solo se ofrece en el listado para
- Validación en el caso de uso: solo centros de la propia delegación (`ConfigGlobal::mi_delef()`) o
- Al crear: solo centros de la propia dl o `tipo_ctr=cr` (`resolveWriteRepo`). Al actualizar una carta

### Referencias Internas

- Flujo: `cartaspresentacion.carta_presentacion.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/cartaspresentacion/flujos/carta_presentacion.md`

## Cartas Presentacion

### Para Que Sirve

Consultar todas las cartas de presentación organizadas por tipo de labor, delegación y población.

### Donde Entrar

- Cartas Presentacion Lista (frontend/cartaspresentacion/controller/cartas_presentacion_lista.php)
- **Legacy:** scdl > direcciones > cartas presentacion > lista dl / lista todo
- **Pills2:** scdl > direcciones > cartas presentacion > lista dl / lista todo

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `Centros con tipo_labor mal configurado aparecen en aviso al pie (html_errores), no como error AJAX.`

### Permisos

- Sin control de permisos propio en el caso de uso; autorización en frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `cartaspresentacion.cartas_presentacion.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/cartaspresentacion/flujos/cartas_presentacion.md`

## Cartas Presentacion Buscar

### Para Que Sirve

Encontrar cartas de presentación que cumplan criterios geográficos o de delegación.

### Donde Entrar

- Cartas Presentacion Buscar (frontend/cartaspresentacion/controller/cartas_presentacion_buscar.php)
- **Legacy:** scdl > direcciones > cartas presentacion > buscar
- **Pills2:** scdl > direcciones > cartas presentacion > buscar

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio; autorización en frontend + `$_SESSION['oPerm']`.
- Sin control de permisos propio en el caso de uso; autorización en frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `cartaspresentacion.cartas_presentacion_buscar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/cartaspresentacion/flujos/cartas_presentacion_buscar.md`

## Cartas Presentacion Shell

### Para Que Sirve

Mantener los datos de presentación (director, contacto, zona) de los centros de la delegación o de regiones extranjeras.

### Donde Entrar

- Cartas Presentacion (frontend/cartaspresentacion/controller/cartas_presentacion.php)
- **Legacy:** scdl > direcciones > cartas presentacion > modificar
- **Pills2:** scdl > direcciones > cartas presentacion > modificar

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `Formulario: No puede modificar datos de otra dl, Centro no encontrado.`
- `Update: Hay un error, no se ha guardado.`
- `Eliminar: Carta de presentacion no encontrada, Hay un error, no se ha borrado.`

### Permisos

- Sin control de permisos propio; solo compone rutas. La autorización la ejercen los endpoints destino
- Sin control de permisos propio; autorización en frontend + `$_SESSION['oPerm']`.
- Validación en el caso de uso: solo centros de la propia delegación (`ConfigGlobal::mi_delef()`) o
- Al crear: solo centros de la propia dl o `tipo_ctr=cr` (`resolveWriteRepo`). Al actualizar una carta
- Sin control de permisos propio en el caso de uso; la acción solo se ofrece en el listado para

### Referencias Internas

- Flujo: `cartaspresentacion.cartas_presentacion_shell.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/cartaspresentacion/flujos/cartas_presentacion_shell.md`

## Poblaciones

### Para Que Sirve

Filtrar el listado de centros por población dentro de la delegación (modo `get_dl`).

### Donde Entrar

- Cartas Presentacion (frontend/cartaspresentacion/controller/cartas_presentacion.php)
- sin entrada de menú en el índice (auxiliar de `cartas_presentacion` > modificar).

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio; autorización en frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `cartaspresentacion.poblaciones.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/cartaspresentacion/flujos/poblaciones.md`

## Ubis

### Para Que Sirve

Ver qué centros tienen carta de presentación y acceder a modificar, ver ficha o quitar.

### Donde Entrar

- Cartas Presentacion Ubis Lista (frontend/cartaspresentacion/controller/cartas_presentacion_ubis_lista.php)
- sin entrada de menú en el índice (fragmento de `cartas_presentacion` > modificar).

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio; autorización en frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `cartaspresentacion.ubis.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/cartaspresentacion/flujos/ubis.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/cartaspresentacion/flujos/`.
