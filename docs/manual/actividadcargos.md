---
tipo: "manual_usuario"
modulo: "actividadcargos"
flujos: 4
estado_revision: "revisado_parcial"
---

# Manual De Usuario - actividadcargos

Manual revisado. Sin entrada de menú propia: acceso vía dossiers de actividad o persona.

## Como Usar Este Manual

Cada apartado corresponde a un flujo del catálogo. Las operaciones de **alta, edición y baja** se ejecutan desde los dossiers de actividad o persona; no hay pantalla de menú independiente para cargos.

## Cargo

> Sección revisada manualmente (`estado_revision: revisado`).

### Para Que Sirve

Gestionar la **relación de cargos de una actividad**: ver quién tiene qué cargo, dar de alta un cargo nuevo (vía formulario, ver *Form Cargos De Actividad*) y **quitar** un cargo a una persona. En algunos casos, al quitar el cargo también se elimina a la persona de la lista de asistentes.

### Donde Entrar

- Dossier **Cargos de actividad** (tipo `3102`), dentro de la ficha de una actividad.
- Widget **Relación de cargos** (`select_cargos_de_actividad.phtml`).
- Ruta de menú exacta: pendiente de confirmar (entrada habitual vía ficha de actividad).

### Tareas Habituales

#### Consultar cargos de la actividad

1. Abrir la actividad y el dossier de cargos.
2. Revisar la tabla: cargo, nombre y apellidos, ¿Puede ser agd?, observaciones.
3. Usar los enlaces **dl** al pie para añadir cargo según tipo de persona permitido (p. ej. *añadir monitor*).

#### Dar de alta un cargo

1. Pulsar el enlace **añadir …** correspondiente al tipo de persona (según permisos del tipo de actividad).
2. Completar el formulario *Cargo de una actividad* (ver sección **Form Cargos De Actividad**).
3. Tras guardar, comprobar que la fila aparece en la relación de cargos.

#### Quitar un cargo

1. Marcar **una sola fila** en la tabla (selección obligatoria).
2. Pulsar **quitar cargo**.
3. Leer el aviso de confirmación; si el usuario tiene permiso `des` o `vcsd` y el tipo de actividad gestiona asistentes (`s`/`sg`), el mensaje indica que **también se borrará de la lista de asistentes**.
4. Confirmar.
5. Verificar que la fila desaparece tras refrescar el dossier.

### Errores O Avisos Frecuentes

| Mensaje | Qué significa / qué hacer |
|---------|---------------------------|
| `faltan parametros id_activ / id_nom / id_cargo` | Datos incompletos al crear cargo (formulario). Completar actividad, persona y tipo de cargo. |
| `ya existe este cargo para esta actividad` | Esa persona ya tiene ese cargo en la actividad. |
| `falta id_item` | No se identificó el cargo a eliminar; volver a seleccionar la fila. |
| `no encuentro el cargo` | El registro ya no existe; refrescar el dossier. |
| `hay un error, no se ha eliminado` | Fallo al borrar el cargo; reintentar o contactar soporte. |
| `hay un error, no se ha eliminado el asistente` | El cargo se quitó pero falló el borrado del asistente asociado. |
| `hay un error, no se ha guardado el asistente` | En alta con **¿asiste?** marcado, falló crear el asistente. |
| `error de comunicación con el servidor` | Problema de red o sesión. |

### Permisos

- Botones **modificar cargo** / **quitar cargo**: no disponibles en ámbito `rstgr`.
- Enlaces de alta: según `perm_pers_activ` del tipo de actividad (tipos de persona con permiso).
- Aviso de borrado de asistente al eliminar: usuarios con permiso oficina **`des`** o **`vcsd`**.

### Referencias Internas

- Flujo: `actividadcargos.cargo.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadcargos/flujos/cargo.md`
- Endpoints: `/src/actividadcargos/cargo_nuevo`, `/src/actividadcargos/cargo_eliminar`

## Cargo Editar

> Sección revisada manualmente (`estado_revision: revisado`).

### Para Que Sirve

**Guardar cambios** en un cargo ya existente: tipo de cargo, flag AGD, observaciones y, cuando aplica, sincronizar si la persona **asiste** o no a la actividad (alta/baja del registro de asistente).

No es una pantalla aparte: se invoca al pulsar **Guardar datos del cargo** en el formulario en modo `editar`.

### Donde Entrar

- Mismo formulario **Cargo de una actividad**, abierto con **modificar cargo** desde la relación de cargos (actividad o persona).
- Endpoint: `/src/actividadcargos/cargo_editar`.

### Tareas Habituales

#### Modificar un cargo

1. En la relación de cargos, seleccionar una fila y pulsar **modificar cargo**.
2. Ajustar **Cargo**, **¿Puede ser agd?** u **Observaciones** (asistente/actividad suelen venir fijos).
3. Pulsar **Guardar datos del cargo**.
4. Si la operación es correcta, el panel se cierra y el listado muestra los cambios.

#### Efecto sobre asistentes (edición)

- Si el formulario incluye **¿asiste?** y se desmarca, Orbix intenta **eliminar** el asistente asociado.
- Si se marca asistencia donde no había asistente, lo **crea**.

### Errores O Avisos Frecuentes

| Mensaje | Qué significa / qué hacer |
|---------|---------------------------|
| `faltan parametros id_activ / id_nom / id_cargo` | Campos obligatorios vacíos o inválidos. |
| `no encuentro el cargo` | El `id_item` ya no existe; volver al listado. |
| `ya existe este cargo para esta actividad` | Conflicto al cambiar a un tipo de cargo que esa persona ya tiene en la actividad. |
| `hay un error, no se ha guardado` | Error al persistir el cargo. |
| `hay un error, no se ha guardado el asistente` | Cargo guardado pero falló crear asistente. |
| `hay un error, no se ha eliminado el asistente` | Falló borrar asistente al desmarcar **¿asiste?**. |

### Permisos

- Heredados del dossier y del permiso de modificación sobre la fila (según tipo de actividad y persona).

### Referencias Internas

- Flujo: `actividadcargos.cargo_editar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadcargos/flujos/cargo_editar.md`

## Form Cargos De Actividad

> Sección revisada manualmente (`estado_revision: revisado`).

### Para Que Sirve

Asignar o editar el **cargo de una persona en una actividad** (monitor, coordinador, etc.): elegir asistente y tipo de cargo, indicar si puede ser AGD, añadir observaciones y, en altas, marcar si la persona **asiste** (puede dar de alta también el asistente).

### Donde Entrar

- Formulario **Cargo de una actividad**, abierto desde el **dossier de cargos de la actividad** (`3102`) o desde el contexto de una persona en actividad.
- Controlador: `frontend/actividadcargos/controller/form_cargos_de_actividad.php`.
- Panel deslizante dentro del flujo de la actividad; no es menú independiente.

### Tareas Habituales

#### Dar de alta un cargo

1. Desde la actividad, pulsar el enlace **añadir …** (modo `nuevo`).
2. Si no viene persona fijada: elegir **Asistente** en el desplegable.
3. Elegir **Cargo** en el desplegable.
4. Marcar **¿Puede ser agd?** si aplica.
5. Rellenar **Observaciones** si hace falta.
6. En altas sin persona preseleccionada, revisar **¿asiste?** (marcado por defecto); desmarcar si tiene cargo pero no asiste.
7. Pulsar **Guardar datos del cargo**.
8. Si todo es correcto, el panel se cierra y vuelve a la pantalla anterior.

#### Modificar un cargo existente

1. Desde la relación de cargos, **modificar cargo** (modo `editar`).
2. El **Asistente** aparece fijo; cambiar **Cargo**, **¿Puede ser agd?** u **Observaciones**.
3. Pulsar **Guardar datos del cargo** o **Cancelar**.

### Errores O Avisos Frecuentes

| Mensaje | Qué significa / qué hacer |
|---------|---------------------------|
| `faltan datos obligatorios: actividad, asistente y tipo de cargo` | Validación en pantalla. Completar los tres campos. |
| `faltan parametros id_activ / id_nom / id_cargo` | Misma causa en servidor. |
| `ya existe este cargo para esta actividad` | Persona con ese cargo ya en la actividad. |
| `no encuentro el cargo` | Registro borrado; volver al listado. |
| `no encuentro a nadie con id_nom: …` | Persona inexistente; recargar ficha de actividad. |
| `hay un error, no se ha guardado` / `...guardado el asistente` | Error al persistir; reintentar o contactar soporte. |
| `error de comunicación con el servidor` | Reintentar; comprobar sesión. |

### Permisos

- Heredados del dossier de actividad (`permiso` en la petición). Confirmar roles con el equipo funcional.

### Referencias Internas

- Flujo: `actividadcargos.form_cargos_de_actividad.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadcargos/flujos/form_cargos_de_actividad.md`
- Endpoints de guardado: `/src/actividadcargos/cargo_nuevo`, `/src/actividadcargos/cargo_editar`

## Form Cargos Personas En Actividad

> Sección revisada manualmente (`estado_revision: revisado`).

### Para Que Sirve

Gestionar los **cargos de una persona** en distintas actividades (vista inversa a la del dossier de actividad): consultar en qué actividades tiene cargo, añadir cargo en una actividad concreta o modificar/eliminar cargos existentes.

### Donde Entrar

- Dossier **Cargos personas en actividad** (tipo `1302`), dentro de la ficha de una **persona**.
- Listado: `select_cargos_personas_en_actividad.phtml`.
- Formulario: `form_cargos_personas_en_actividad.phtml` (mismo título *Cargo de una actividad*).

### Tareas Habituales

#### Consultar cargos de la persona

1. Abrir la ficha de la persona y el dossier de cargos en actividades.
2. Filtrar con los botones de curso (**actuales** / **curso** / **todos**) si están visibles.
3. Revisar la tabla: cargo, actividad, ¿Puede ser agd?, observaciones.

#### Añadir cargo en una actividad

1. Pulsar el enlace del tipo de actividad permitido (filas **dl** u **otros** al pie del listado).
2. En el formulario, elegir **Actividad** (desplegable) si no viene fijada.
3. Elegir **Cargo**, **¿Puede ser agd?**, **Observaciones** y, en altas, **¿asiste?** si aparece.
4. Pulsar **Guardar datos del cargo** (no hay botón Cancelar en esta vista; usar navegación atrás del dossier si hace falta).

#### Modificar o quitar un cargo

1. Seleccionar **una fila** en la tabla.
2. **Modificar cargo** → formulario con actividad fija (solo lectura) → guardar cambios.
3. **Quitar cargo** → confirmar (mismo aviso que en vista por actividad sobre asistentes, para usuarios `des`/`vcsd`).

### Errores O Avisos Frecuentes

| Mensaje | Qué significa / qué hacer |
|---------|---------------------------|
| `faltan datos obligatorios: actividad, persona y tipo de cargo` | Validación cliente del formulario por persona. |
| `faltan parametros id_activ / id_nom / id_cargo` | Misma causa en servidor. |
| `ya existe este cargo para esta actividad` | Duplicado persona+cargo+actividad. |
| `no encuentro el cargo` / `falta id_item` | Registro inexistente al editar/eliminar. |
| `No encuentro a ninguna persona con id_nom: …` | Ficha de persona inválida; recargar dossier. |
| Errores de guardado/eliminación de asistente | Ver tablas de **Cargo** y **Cargo Editar**. |
| `error de comunicación con el servidor` | Reintentar. |

### Permisos

- Enlaces de alta: según `perm_activ_pers` del tipo de tabla de la persona.
- Eliminación con aviso de asistente: permisos **`des`** o **`vcsd`**.

### Referencias Internas

- Flujo: `actividadcargos.form_cargos_personas_en_actividad.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadcargos/flujos/form_cargos_personas_en_actividad.md`
- Datos del formulario: `/src/actividadcargos/form_cargos_personas_en_actividad_data`

## Revision Pendiente

- Validar permisos concretos por tipo de actividad y rol con el equipo funcional.
- Añadir capturas si se publica para usuarios finales.
- Las cuatro secciones están revisadas.
