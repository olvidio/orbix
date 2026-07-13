---
tipo: "manual_usuario"
modulo: "actividadcargos"
flujos: 4
estado_revision: "generado"
---

# Manual De Usuario - actividadcargos

Este manual es un borrador generado desde `docs/catalogo`. Debe revisarse para ajustar nombres de menu, permisos, validaciones y lenguaje final de usuario.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas de menu y nombres visibles pueden necesitar revision manual.

## Cargo

### Para Que Sirve

- Ver, añadir y quitar cargos de personas en actividades.
- La consulta y los enlaces de alta se hacen en el widget; la alta concreta pasa por el formulario (`cargo_nuevo`); la baja es directa vía `cargo_eliminar`.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Crear

1. Abrir el dossier de cargos (actividad o persona).
2. Pulsar el enlace **añadir …** del colectivo o tipo de actividad permitido.
3. Completar el formulario **Cargo de una actividad** (persona/actividad, tipo de cargo, AGD, observaciones; en altas, **¿asiste?** si aplica).
4. Pulsar **Guardar datos del cargo**.
5. Comprobar que la fila aparece en la relación de cargos (y en asistentes si marcó **¿asiste?**).

#### Eliminar

1. En la relación de cargos, marcar **una sola fila**.
2. Pulsar **quitar cargo**.
3. Leer el aviso de confirmación (puede indicar borrado del asistente si `des`/`vcsd` y tipo `s`/`sg`).
4. Confirmar.
5. El listado se refresca automáticamente (`fnjs_actualizar`).

### Errores O Avisos Frecuentes

- `falta id_item`
- `faltan parametros id_activ / id_nom / id_cargo`
- `hay un error, no se ha eliminado`
- `hay un error, no se ha eliminado el asistente`
- `hay un error, no se ha guardado el asistente`
- `no encuentro el cargo`
- `ya existe este cargo para esta actividad`

### Permisos

- El caso de uso comprueba `perm_modificar()` del `Asistente` antes de eliminarlo; el resto de
- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadcargos.cargo.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadcargos/flujos/cargo.md`

## Cargo Editar

### Para Que Sirve

Guardar cambios en un cargo existente: tipo de cargo, flag AGD, observaciones y, cuando el formulario incluye **¿asiste?**, sincronizar el registro de asistente (alta/baja).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. En la relación de cargos, seleccionar una fila y pulsar **modificar cargo**.
2. Ajustar **Cargo**, **¿Puede ser agd?** u **Observaciones** (persona y actividad suelen venir fijas).
3. Si aparece **¿asiste?**, marcar o desmarcar según corresponda.
4. Pulsar **Guardar datos del cargo**.
5. En éxito, el panel se cierra y el listado refleja los cambios.

### Errores O Avisos Frecuentes

- `faltan parametros id_activ / id_nom / id_cargo`
- `hay un error, no se ha eliminado el asistente`
- `hay un error, no se ha guardado`
- `hay un error, no se ha guardado el asistente`
- `no encuentro el cargo`
- `ya existe este cargo para esta actividad`

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el

### Referencias Internas

- Flujo: `actividadcargos.cargo_editar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadcargos/flujos/cargo_editar.md`

## Form Cargos De Actividad

### Para Que Sirve

Asignar o editar el cargo de una persona en una actividad: el sistema carga desplegables, valores actuales, hash de campos y URLs de mutación antes de mostrar el formulario.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. El usuario abre el formulario desde el widget de relación de cargos (modo `nuevo` o `editar`).
2. El controller POSTea a `form_cargos_de_actividad_data` con contexto del dossier (`pau`, `id_pau`, `obj_pau`, `sel`, `mod`, `permiso`).
3. El backend devuelve payload con desplegables (`personas_select`, `cargos_select`), valores (`observ`, `chk`), flags (`show_asis`, `id_nom_real`) y `hash_form_config`.
4. El front compone HTML de desplegables y hash; pinta el formulario en el bloque AJAX.
5. Si falta contexto válido, puede devolver `redir: go_atras` o `error`.

### Errores O Avisos Frecuentes

- `no encuentro el cargo (edición con sel inválido)`
- `Mensajes HTML de persona no encontrada (No encuentro a nadie con id_nom: …)`
- `redir: go_atras cuando falta obj_pau en altas`

### Permisos

- El caso de uso no aplica un control de permisos propio: la autorización de oficina se resuelve

### Referencias Internas

- Flujo: `actividadcargos.form_cargos_de_actividad.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadcargos/flujos/form_cargos_de_actividad.md`

## Form Cargos Personas En Actividad

### Para Que Sirve

Gestionar los cargos de una persona en distintas actividades: el sistema carga el listado de actividades candidatas (en altas), valores del cargo en edición y URLs de mutación.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. El usuario abre el formulario desde el widget de relación de cargos de la persona.
2. El controller POSTea a `form_cargos_personas_en_actividad_data` con `id_pau` (persona), `sel`, `mod`, `que_dl`, `id_tipo` según el enlace de alta.
3. En modo `editar`, carga datos del `ActividadCargo` y fija actividad en solo lectura.
4. En modo `nuevo`, filtra actividades por tipo y delegación (`que_dl` vacío = otras delegaciones).
5. El front pinta desplegables y hash; el usuario completa y guarda vía `cargo_nuevo`/`cargo_editar`.

### Errores O Avisos Frecuentes

- `no encuentro el cargo (edición)`
- `actividad no encontrada`

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización se resuelve en frontend y

### Referencias Internas

- Flujo: `actividadcargos.form_cargos_personas_en_actividad.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadcargos/flujos/form_cargos_personas_en_actividad.md`

## Revision Pendiente

- Sustituir nombres tecnicos por nombres visibles en la aplicacion.
- Completar rutas de menu.
- Confirmar permisos necesarios.
- Anadir capturas o ejemplos si se quiere publicar para usuarios finales.
