---
tipo: "manual_usuario"
modulo: "personas"
flujos: 8
estado_revision: "revisado_parcial"
---

# Manual De Usuario - personas

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## Ver cabecera de persona

### Para Que Sirve

Consultar datos básicos y acceder a la ficha completa o dossiers sin pasar por el listado.

### Donde Entrar

- Cabecera de persona (frontend/personas/controller/home_persona.php)
- sin entrada de menú en el índice.

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `No se encuentra la persona`
- `Aviso: persona no válida`

### Permisos

- Sin control en el caso de uso; autorización en frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `personas.home_persona.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/personas/flujos/home_persona.md`

## Guardar o eliminar persona

### Para Que Sirve

Guardar cambios en la ficha o eliminar un registro de la propia delegación.

### Donde Entrar

- Ficha de persona (frontend/personas/controller/personas_editar.php)
- sin entrada de menú en el índice.

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `No se ha pasado el id_nom`
- `No se ha eliminado, porque no es de mi dl`
- `hay un error, no se ha guardado / no se ha eliminado`

### Permisos

- Sin `perm_*` en el caso de uso. El frontend solo muestra botón guardar si `ok=1` según
- Implícito: solo personas de `mi_delef`. Botón eliminar en ficha solo si `ok=1` (permiso oficina).

### Referencias Internas

- Flujo: `personas.persona.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/personas/flujos/persona.md`

## Publicar persona hacia otra DL

### Para Que Sirve

Compartir temporalmente una persona con otra delegación sin trasladarla.

### Donde Entrar

- Publicar persona (frontend/personas/controller/persona_publicar_form.php)
- sin entrada de menú en el índice.

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `No se encuentra la persona`
- `No se puede determinar el esquema de la persona`
- `Datos de persona no válidos`
- `Debe indicar al menos una delegación destino`
- `No se puede publicar hacia la propia delegación`
- `No se ha podido publicar la persona`
- `FE: Debe elegir una delegación (alert si dl vacío)`

### Permisos

- Sin control en el caso de uso. El botón «publicar» del listado solo aparece con
- Sin `perm_*` en el caso de uso. Acceso FE: listado con permiso oficina `est`/`sm`/`agd`.

### Referencias Internas

- Flujo: `personas.persona_publicar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/personas/flujos/persona_publicar.md`

## Abrir ficha de persona

### Para Que Sirve

Crear una persona nueva o editar la ficha existente con los campos del colectivo correspondiente.

### Donde Entrar

- Ficha de persona (frontend/personas/controller/personas_editar.php)
- sin entrada de menú en el índice.

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `No se ha pasado el id_nom`
- `No se encuentra la persona`

### Permisos

- Sin control en el caso de uso. El frontend activa edición/guardado con `have_perm_oficina`

### Referencias Internas

- Flujo: `personas.personas_editar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/personas/flujos/personas_editar.md`

## Buscar y listar personas

### Para Que Sirve

Encontrar personas del colectivo indicado por el menú, revisar resultados y lanzar acciones (ficha, dossiers, STGR, traslado, módulos satélite).

### Donde Entrar

- Buscar personas (frontend/personas/controller/personas_que.php)
- Resultado búsqueda personas (frontend/personas/controller/personas_select.php)
- Herencia de `personas_que` — ver variantes vsm/PERSONAS en `_referencia_menus.md`
- (p. ej. `vsm > buscar n > n r/dl` / `PERSONAS > Numerarios > Buscar n de la r/dl`).

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `No se encuentra ningún centro con esta condición`
- `Avisos suaves región/persona no válida (listado vacío con mensaje)`

### Permisos

- `permiso=3` si `$_SESSION['oPerm']->have_perm_oficina(...)` según colectivo: `sm` (n),
- Resto: `permiso=1`. Sin `perm_*` propio en el caso de uso.

### Referencias Internas

- Flujo: `personas.personas_select.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/personas/flujos/personas_select.md`

## Guardar nivel STGR

### Para Que Sirve

Actualizar el nivel STGR de una persona del listado.

### Donde Entrar

- Cambio nivel STGR (frontend/personas/controller/stgr_cambio.php)
- sin entrada de menú en el índice.

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `No existe la clase de la persona`
- `No se encuentra la persona`
- `hay un error, no se ha guardado`

### Permisos

- Frontend: acción «modificar stgr» requiere `have_perm_oficina('est')` en el listado.

### Referencias Internas

- Flujo: `personas.stgr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/personas/flujos/stgr.md`

## Cambiar nivel STGR (formulario)

### Para Que Sirve

Ver el nivel actual y las opciones disponibles antes de guardar el cambio.

### Donde Entrar

- Cambio nivel STGR (frontend/personas/controller/stgr_cambio.php)
- sin entrada de menú en el índice.

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `No existe la clase de la persona`
- `No se encuentra la persona`

### Permisos

- Acceso al formulario controlado en frontend: botón «modificar stgr» requiere `have_perm_oficina('est')`

### Referencias Internas

- Flujo: `personas.stgr_cambio.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/personas/flujos/stgr_cambio.md`

## Trasladar persona

### Para Que Sirve

Mover una persona a otro centro o delegación, documentando fechas y situación.

### Donde Entrar

- Traslado de persona (frontend/personas/controller/traslado_form.php)
- sin entrada de menú en el índice.

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `con las personas de paso no tiene sentido.`
- `Falta una situación válida`
- `Faltan id_pau u obj_pau`

### Permisos

- Sin control en el caso de uso. Enlace «traslado» solo en ficha edición (`personas_editar`)
- Sin control en caso de uso; acceso desde ficha con permiso de edición.

### Referencias Internas

- Flujo: `personas.traslado.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/personas/flujos/traslado.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/personas/flujos/`.
