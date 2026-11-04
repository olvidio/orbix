---
tipo: "manual_usuario"
modulo: "actividadcargos"
flujos: 4
estado_revision: "revisado"
---

# Manual De Usuario - actividadcargos

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## Añadir un cargo

### Para Que Sirve

- Ver, añadir y quitar cargos de personas en actividades.
- Se consulta y se pulsa el enlace de alta en la relación de cargos; el alta se hace en un formulario y la baja es directa.

### Donde Entrar

- actividadcargos.pantalla.select_cargos_de_actividad
- actividadcargos.pantalla.select_cargos_personas_en_actividad
- sin entrada de menú en el índice (acceso vía dossiers 3102/1302; actividad: buscador p. ej. **Legacy:** vsm > ca > buscar ca · **Pills2:** ACTIVIDADES > Buscar actividad > ca n; persona: ficha de persona).

### Tareas Habituales

#### Añadir un cargo

1. Abrir el dossier de cargos, de la actividad o de la persona.
2. Pulsar el enlace **añadir …** del colectivo o tipo de actividad que corresponda.
3. Completar el formulario **Cargo de una actividad**: persona o actividad, tipo de cargo, agd, observaciones y, en las altas, **¿asiste?** si procede.
4. Pulsar **Guardar datos del cargo**.
5. Comprobar que la fila aparece en la relación de cargos (y en asistentes, si se marcó **¿asiste?**).

#### Quitar un cargo

1. En la relación de cargos, marcar **una sola fila**.
2. Pulsar **quitar cargo**.
3. Leer el aviso de confirmación; puede avisar de que también se borra al asistente si se trata del tipo s o sg y el usuario es de des o vcsd.
4. Confirmar. El listado se actualiza solo.

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

- Form Cargos De Actividad (frontend/actividadcargos/controller/form_cargos_de_actividad.php)
- Form Cargos Personas En Actividad (frontend/actividadcargos/controller/form_cargos_personas_en_actividad.php)
- sin entrada de menú en el índice (subflujo del formulario de cargo, accesible desde dossiers 3102/1302).

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

## Añadir o modificar un cargo en una actividad

### Para Que Sirve

Añadir o modificar un cargo de una persona en una actividad.

### Donde Entrar

- Form Cargos De Actividad (frontend/actividadcargos/controller/form_cargos_de_actividad.php)
- sin entrada de menú en el índice (fragmento AJAX del dossier 3102; entrada habitual vía ficha de actividad).

### Tareas Habituales

#### Añadir o modificar un cargo en una actividad

1. Pulsar **añadir n** / **añadir agd** en «Relación de cargos», o marcar a un asistente y pulsar **añadir cargo** / **modificar cargo**.
2. Se abre «Cargo de una actividad» con el nombre del asistente y los campos **Cargo** (desplegable, por ejemplo «d»), **¿Puede ser agd?** y **Observaciones**.
3. Pulsar **Guardar datos del cargo**, o **Cancelar** para salir sin guardar.

#### Ver los cargos de una actividad

1. Marcar la actividad en la lista y pulsar **cargos**.
2. Se abre «Relación de cargos» con el enlace **( dossiers )**; sin cargos indica «no hay ninguna fila».
3. Los botones **añadir n** y **añadir agd** abren el formulario de cargo.

### Errores O Avisos Frecuentes

- `no encuentro el cargo (edición con sel inválido)`
- `Mensajes HTML de persona no encontrada (No encuentro a nadie con id_nom: …)`
- `redir: go_atras cuando falta obj_pau en altas`

### Permisos

- El caso de uso no aplica un control de permisos propio: la autorización de oficina se resuelve

### Referencias Internas

- Flujo: `actividadcargos.form_cargos_de_actividad.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadcargos/flujos/form_cargos_de_actividad.md`

## Abrir el formulario de cargo de una persona

### Para Que Sirve

Abrir el formulario para dar de alta o modificar un cargo de una persona en una actividad, con las actividades candidatas en las altas.

### Donde Entrar

- Form Cargos Personas En Actividad (frontend/actividadcargos/controller/form_cargos_personas_en_actividad.php)
- sin entrada de menú en el índice (fragmento AJAX del dossier 1302; entrada habitual vía ficha de persona).

### Tareas Habituales

#### Abrir el formulario de cargo de una persona

1. Desde la relación de cargos de la persona, pulsar **añadir cargo de la dl**, **añadir cargo de otra dl** o seleccionar un cargo y pulsar **modificar cargo**.
2. Al modificar, aparecen los datos del cargo y la actividad no se puede cambiar.
3. Al añadir, el desplegable de actividades solo ofrece las del tipo elegido y de la propia dl (o de otras delegaciones, según el enlace).
4. Completar el formulario y guardar.

### Errores O Avisos Frecuentes

- `no encuentro el cargo (edición)`
- `actividad no encontrada`

### Permisos

- Sin control de permisos propio en el caso de uso; la autorización se resuelve en frontend y

### Referencias Internas

- Flujo: `actividadcargos.form_cargos_personas_en_actividad.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadcargos/flujos/form_cargos_personas_en_actividad.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/actividadcargos/flujos/`.
