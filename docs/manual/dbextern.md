---
tipo: "manual_usuario"
modulo: "dbextern"
flujos: 16
estado_revision: "revisado_parcial"
---

# Manual De Usuario - dbextern

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## Refrescar copia BDU

### Para Que Sirve

Si los datos de listas cambiaron después de la fecha mostrada, refrescar la copia local antes de sincronizar (operación de varios minutos).

### Donde Entrar

- Sincronizar con los datos de Listas (frontend/dbextern/controller/sincro_index.php)
- sin entrada de menú en el índice (acción embebida en `sincro_index`)

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `Error al refrescar la BDU: …`

### Permisos

- Sin control propio; HashFront en `sincro_index.phtml` (`h2`).

### Referencias Internas

- Flujo: `dbextern.refrescar_bdu.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dbextern/flujos/refrescar_bdu.md`

## Crear persona desde BDU

### Para Que Sirve

Cuando no hay coincidencia Orbix, crear una ficha nueva y vincularla automáticamente.

### Donde Entrar

- Personas BDU no unidas (frontend/dbextern/controller/ver_listas.php)
- sin entrada de menú en el índice

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `no se encontró la persona en la BDU`
- `no se pudo resolver la delegación de listas`
- `hay un error, no se ha guardado`

### Permisos

- HashFront en `ver_listas.phtml` (`h_crear`).

### Referencias Internas

- Flujo: `dbextern.sincro.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dbextern/flujos/sincro.md`

## Baja ficha Aquinate

### Para Que Sirve

Cerrar la ficha Aquinate cuando la persona ya no está en la BDU.

### Donde Entrar

- Aquinate con BDU desaparecida (frontend/dbextern/controller/ver_desaparecidos_de_listas.php)
- sin entrada de menú en el índice

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `OJO: Debería cambiar el campo situación. No se ha hecho ningún cambio.`

### Permisos

- HashFront en `ver_desaparecidos_de_listas.phtml`.

### Referencias Internas

- Flujo: `dbextern.sincro_baja.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dbextern/flujos/sincro_baja.md`

## Crear todas desde BDU

### Para Que Sirve

Crear en bloque todas las fichas pendientes del punto 4 sin revisar una a una.

### Donde Entrar

- Personas BDU no unidas (frontend/dbextern/controller/ver_listas.php)
- sin entrada de menú en el índice

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `Errores de sincro_crear por cada persona fallida`

### Permisos

- HashFront en `ver_listas.phtml` (`h_crear_todos`).

### Referencias Internas

- Flujo: `dbextern.sincro_crear_todos.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dbextern/flujos/sincro_crear_todos.md`

## Desunir vínculo BDU

### Para Que Sirve

Romper el vínculo incorrecto para poder re-unir o crear la ficha después.

### Donde Entrar

- BDU desaparecidas en Aquinate (frontend/dbextern/controller/ver_desaparecidos_de_orbix.php)
- sin entrada de menú en el índice

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `no se encontró el registro a desunir`
- `hay un error, no se ha eliminado`

### Permisos

- HashFront en `ver_desaparecidos_de_orbix.phtml`.

### Referencias Internas

- Flujo: `dbextern.sincro_desunir.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dbextern/flujos/sincro_desunir.md`

## Dashboard sincronización BDU

### Para Que Sirve

Al abrir la pantalla de sincronización, el sistema calcula los contadores de situación BDU↔Aquinate y prepara enlaces firmados a las subpantallas de resolución.

### Donde Entrar

- Sincronizar con los datos de Listas (frontend/dbextern/controller/sincro_index.php)
- **Legacy:** vsm/dagd/vsg/dre > … > Actualizar datos desde BDU (según `tipo`)
- **Pills2:** PERSONAS > Numerarios / Agregados / Supernumerarios > Actualizar datos desde BDU

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `No se encontró la delegación en listas`
- `no tiene permisos`
- `No existe la clase de la persona`

### Permisos

- tipo=n` → `have_perm_oficina('sm')
- tipo=a` → `have_perm_oficina('agd')
- tipo=s` → `have_perm_oficina('sg')
- tipo=sssc` → `have_perm_oficina('des')
- Vía `$_SESSION['oPerm']` (`XPermisos`).

### Referencias Internas

- Flujo: `dbextern.sincro_index.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dbextern/flujos/sincro_index.md`

## Sincronizar fichas unidas

### Para Que Sirve

Actualizar en Aquinate los datos de todas las personas ya vinculadas a la BDU en la DL actual.

### Donde Entrar

- Sincronizar con los datos de Listas (frontend/dbextern/controller/sincro_index.php)
- sin entrada de menú en el índice (acción embebida en `sincro_index`)

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `Mensajes de syncro por persona (dentro de mensaje, no siempre success: false)`

### Permisos

- HashFront (`h1`) en `sincro_index`; permisos de colectivo ya validados en bootstrap.

### Referencias Internas

- Flujo: `dbextern.sincro_syncro.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dbextern/flujos/sincro_syncro.md`

## Trasladar a esta DL

### Para Que Sirve

Traer la ficha a la DL actual; la fecha de traslado queda en hoy (aviso en pantalla).

### Donde Entrar

- Traslados desde otra DL (frontend/dbextern/controller/ver_traslados.php)
- sin entrada de menú en el índice

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `Mensajes del dominio Trasladar / Error al trasladar`

### Permisos

- HashFront en `ver_traslados.phtml`.

### Referencias Internas

- Flujo: `dbextern.sincro_trasladar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dbextern/flujos/sincro_trasladar.md`

## Trasladar a otra DL

### Para Que Sirve

Mover la ficha Aquinate a la delegación donde está su correspondencia en listas.

### Donde Entrar

- Trasladar a otra DL (frontend/dbextern/controller/ver_orbix_otradl.php)
- sin entrada de menú en el índice

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `No se encontró la delegación destino`
- `Este traslado debe hacerse desde el dossier de traslados`

### Permisos

- HashFront en `ver_orbix_otradl.phtml`.

### Referencias Internas

- Flujo: `dbextern.sincro_trasladar_a.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dbextern/flujos/sincro_trasladar_a.md`

## Unir BDU con Aquinate

### Para Que Sirve

Confirmar la correspondencia cuando el sistema sugiere candidatos (puntos 4 y 9).

### Donde Entrar

- Personas BDU no unidas (frontend/dbextern/controller/ver_listas.php)
- Personas Aquinate sin BDU (frontend/dbextern/controller/ver_orbix.php)
- sin entrada de menú en el índice

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado`

### Permisos

- HashFront en pantallas `ver_listas` / `ver_orbix`.

### Referencias Internas

- Flujo: `dbextern.sincro_unir.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dbextern/flujos/sincro_unir.md`

## Aquinate con BDU vacía

### Para Que Sirve

Revisar fichas Aquinate cuya correspondencia BDU ya no existe.

### Donde Entrar

- Aquinate con BDU desaparecida (frontend/dbextern/controller/ver_desaparecidos_de_listas.php)
- sin entrada de menú en el índice

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio.

### Referencias Internas

- Flujo: `dbextern.ver_desaparecidos_de_listas.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dbextern/flujos/ver_desaparecidos_de_listas.md`

## BDU sin ficha Aquinate

### Para Que Sirve

Revisar personas BDU con vínculo pero sin ficha activa en esta DL.

### Donde Entrar

- BDU desaparecidas en Aquinate (frontend/dbextern/controller/ver_desaparecidos_de_orbix.php)
- sin entrada de menú en el índice

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio.

### Referencias Internas

- Flujo: `dbextern.ver_desaparecidos_de_orbix.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dbextern/flujos/ver_desaparecidos_de_orbix.md`

## Revisar BDU no unidas

### Para Que Sirve

Revisar una a una las personas de la BDU no unidas, viendo posibles coincidencias en Aquinate.

### Donde Entrar

- Personas BDU no unidas (frontend/dbextern/controller/ver_listas.php)
- sin entrada de menú en el índice

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio; acceso desde pantalla `ver_listas` abierta desde `sincro_index` (permisos ya

### Referencias Internas

- Flujo: `dbextern.ver_listas.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dbextern/flujos/ver_listas.md`

## Revisar Aquinate sin BDU

### Para Que Sirve

Revisar personas Aquinate activas sin correspondencia BDU y unir si hay candidato.

### Donde Entrar

- Personas Aquinate sin BDU (frontend/dbextern/controller/ver_orbix.php)
- sin entrada de menú en el índice

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `No existe la clase de la persona`

### Permisos

- Sin control propio en el caso de uso.

### Referencias Internas

- Flujo: `dbextern.ver_orbix.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dbextern/flujos/ver_orbix.md`

## Revisar traslados punto 7

### Para Que Sirve

Ver quién está activo aquí pero su correspondencia BDU pertenece a otra DL.

### Donde Entrar

- Trasladar a otra DL (frontend/dbextern/controller/ver_orbix_otradl.php)
- sin entrada de menú en el índice

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control propio.

### Referencias Internas

- Flujo: `dbextern.ver_orbix_otradl.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dbextern/flujos/ver_orbix_otradl.md`

## Revisar traslados punto 2

### Para Que Sirve

Identificar quién debe trasladarse a esta DL desde otra delegación.

### Donde Entrar

- Traslados desde otra DL (frontend/dbextern/controller/ver_traslados.php)
- sin entrada de menú en el índice

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `No existe la clase de la persona`

### Permisos

- Sin control propio.

### Referencias Internas

- Flujo: `dbextern.ver_traslados.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/dbextern/flujos/ver_traslados.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/dbextern/flujos/`.
