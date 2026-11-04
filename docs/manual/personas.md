---
tipo: "manual_usuario"
modulo: "personas"
flujos: 8
estado_revision: "revisado"
---

# Manual De Usuario - personas

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## Ver la ficha de una persona

### Para Que Sirve

Ver la cabecera y los dossiers de una persona.

### Donde Entrar

- Cabecera de persona (frontend/personas/controller/home_persona.php)
- sin entrada de menú en el índice.

### Tareas Habituales

#### Ver la ficha de una persona

1. Se muestra el nombre, la delegación y el centro (por ejemplo «dlb - Diagonal»), nacimiento, santo, celebra, profesión, stgr, teléfonos y e-mail, con el enlace **editar**.
2. Debajo, «Relación de dossiers»: actividades a las que asiste, asignaturas que cursa, cargos en actividades, notas del stgr (tessera), stgr (docencia, certificados, títulos, etc.), traslados y última asistencia. Pulsar uno lo abre.

### Errores O Avisos Frecuentes

- `No se encuentra la persona`
- `Aviso: persona no válida`

### Permisos

- Sin control en el caso de uso; autorización en frontend + `$_SESSION['oPerm']`.

### Referencias Internas

- Flujo: `personas.home_persona.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/personas/flujos/home_persona.md`

## Guardar los datos personales

### Para Que Sirve

Guardar o eliminar los datos personales de una persona.

### Donde Entrar

- Ficha de persona (frontend/personas/controller/personas_editar.php)
- sin entrada de menú en el índice.

### Tareas Habituales

#### Guardar los datos personales

1. En «DATOS PERSONALES» cambiar los campos necesarios.
2. Pulsar **Guardar Cambios** (solo si tu rol lo permite).
3. Si hay un problema el servidor lo avisa con un mensaje.

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

## Publicar una persona hacia otra dl

### Para Que Sirve

Compartir temporalmente una persona con otra delegación, sin trasladarla.

### Donde Entrar

- Publicar persona (frontend/personas/controller/persona_publicar_form.php)
- sin entrada de menú en el índice.

### Tareas Habituales

#### Publicar una persona hacia otra dl

1. En la lista de personas, seleccionar a la persona y pulsar **publicar**.
2. Se abre un formulario con su nombre y un desplegable de delegaciones; elegir la delegación destino (es obligatorio).
3. Guardar. Sale el aviso «persona publicada» y se vuelve a la lista.

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

## Abrir los datos personales de una persona

### Para Que Sirve

Abrir los datos personales de una persona para consultarlos o cambiarlos, o dar de alta una persona.

### Donde Entrar

- Ficha de persona (frontend/personas/controller/personas_editar.php)
- sin entrada de menú en el índice.

### Tareas Habituales

#### Abrir los datos personales de una persona

1. En la ficha pulsar **editar**. Se abre «DATOS PERSONALES» con **Delegación**, **Trato**, **Nombre**, **Apelativo**, **nx1**, **Primer apellido**, **nx2**, **Segundo Apellido**, **Lugar de nacimiento**, **Fecha Nacimiento**, **Situacion**, **Fecha situación**, **Idioma preferido**, **Profesión**, **sacd**, **Stgr**, **eap**, **inc**, **Fecha inc**, **ce**, **Lugar ce**, **Año inicio ce**, **Año fin ce** y **Observaciones**.
2. Al lado aparece el **Centro-sede**.

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

Buscar personas y trabajar con las que salgan: abrir su ficha, ver sus actividades, notas, tessera, trasladarlas, etc.

### Donde Entrar

- Buscar personas (frontend/personas/controller/personas_que.php)
- Resultado búsqueda personas (frontend/personas/controller/personas_select.php)
- Herencia de `personas_que` — ver variantes vsm/PERSONAS en `_referencia_menus.md`
- (p. ej. `vsm > buscar n > n r/dl` / `PERSONAS > Numerarios > Buscar n de la r/dl`).

### Tareas Habituales

#### Buscar y listar personas

1. Abrir «Búsqueda de personas» (por ejemplo «numerarios»). Elegir **realizar una búsqueda exacta** (no / sí) y escribir **Nombre**, **Primer apellido**, **Segundo apellido** o **centro**.
2. Pulsar **Buscar**: sale «N personas encontradas» con una tabla.
3. Pulsar el nombre de una persona abre su ficha. Para actuar sobre una o varias, marcarlas y pulsar un botón: **cambio de ctr**, **ver dossiers**, **ficha**, **ver actividades**, **ver tessera**, **modificar stgr**, **imprimir tessera**, **ver notas**, **posibles ca**, **petición ca**, **petición crt**, **plan estudios**, **ficha profesor stgr**, **copiar tessera**, **publicar** o **atención actividades**.

### Errores O Avisos Frecuentes

- `No se encuentra ningún centro con esta condición`
- `Avisos suaves región/persona no válida (listado vacío con mensaje)`

### Permisos

- `permiso=3` si `$_SESSION['oPerm']->have_perm_oficina(...)` según colectivo: `sm` (n),
- Resto: `permiso=1`. Sin `perm_*` propio en el caso de uso.

### Referencias Internas

- Flujo: `personas.personas_select.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/personas/flujos/personas_select.md`

## Guardar el nivel stgr

### Para Que Sirve

Guardar el nivel de estudios (stgr) elegido para una persona.

### Donde Entrar

- Cambio nivel STGR (frontend/personas/controller/stgr_cambio.php)
- sin entrada de menú en el índice.

### Tareas Habituales

#### Guardar el nivel stgr

1. Elegir el nivel en el desplegable y pulsar **Guardar**.
2. No sale mensaje: se vuelve a la lista de personas. El nuevo nivel se ve en la ficha de la persona (campo «stgr»).

### Errores O Avisos Frecuentes

- `No existe la clase de la persona`
- `No se encuentra la persona`
- `hay un error, no se ha guardado`

### Permisos

- Frontend: acción «modificar stgr» requiere `have_perm_oficina('est')` en el listado.

### Referencias Internas

- Flujo: `personas.stgr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/personas/flujos/stgr.md`

## Cambiar el nivel stgr de una persona

### Para Que Sirve

Abrir el formulario para cambiar el nivel de estudios (stgr) de una persona.

### Donde Entrar

- Cambio nivel STGR (frontend/personas/controller/stgr_cambio.php)
- sin entrada de menú en el índice.

### Tareas Habituales

#### Cambiar el nivel stgr de una persona

1. Marcar exactamente una persona en la lista y pulsar **modificar stgr**.
2. Se abre «Cambiar el stgr» con su nombre y el desplegable de niveles: Bienio, Cuadrienio Año I, Cuadrienio Año II-IV, Repaso, centro estudios, Baja temporal, ap, pa, o ad, sin estudios, est. Ecles., bienio-cuadrienio.
3. Elegir el nivel y pulsar **Guardar** (sigue en el flujo «Guardar nivel stgr»).

### Errores O Avisos Frecuentes

- `No existe la clase de la persona`
- `No se encuentra la persona`

### Permisos

- Acceso al formulario controlado en frontend: botón «modificar stgr» requiere `have_perm_oficina('est')`

### Referencias Internas

- Flujo: `personas.stgr_cambio.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/personas/flujos/stgr_cambio.md`

## Trasladar a una persona

### Para Que Sirve

Cambiar a una persona de centro o de delegación, con fecha y situación.

### Donde Entrar

- Traslado de persona (frontend/personas/controller/traslado_form.php)
- sin entrada de menú en el índice.

### Tareas Habituales

#### Trasladar a una persona

1. Marcar una persona y pulsar **cambio de ctr**.
2. Se muestra una tabla Tipo / Origen / Destino / Fecha: en la fila **Centro** (origen, por ejemplo «Diagonal») elegir el centro de destino y la fecha; en la fila **Delegación** (origen, por ejemplo «dlb») elegir la delegación de destino y la fecha.
3. Elegir la **Situación** (por ejemplo «Alta») y pulsar **Actualizar cambios**.
4. Las personas de paso publicadas no se pueden trasladar.

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
