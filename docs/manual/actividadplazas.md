---
tipo: "manual_usuario"
modulo: "actividadplazas"
flujos: 9
estado_revision: "revisado"
---

# Manual De Usuario - actividadplazas

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## Ver el reparto de plazas de un periodo

### Para Que Sirve

Ver, para un periodo y un tipo de actividad, cuántas plazas tiene cada actividad y cómo se reparten (concedidas y pedidas) entre las delegaciones del grupo, y ajustar esos números desde la propia tabla.

### Donde Entrar

- Gestion Plazas (frontend/actividadplazas/controller/gestion_plazas.php)
- **Legacy:** vsm > ca > Gestión de plazas (y variantes por perfil/tipo: dagd, vsg, vest…)
- **Pills2:** ACTIVIDADES > Gestión de plazas y peticiones > Distribución plazas ca n entre r/dl (y variantes por tipo/colectivo)

### Tareas Habituales

#### Ver el reparto de plazas de un periodo

1. Abrir **Gestión de plazas**.
2. Elegir el periodo (año y periodo, o un rango de fechas) y pulsar **Buscar**.
3. Aparece un cuadro con las actividades en filas y las delegaciones en columnas.

#### Cambiar las plazas de una actividad

1. Localizar la actividad en la tabla.
2. Hacer doble clic en una celda editable: el total, las concedidas (**-c**) o las pedidas (**-p**) de la propia delegación.
3. Escribir el nuevo valor; se guarda al momento.
4. Si la actividad no tiene plazas en el calendario común, sale un aviso: hay que darlas de alta antes.

### Errores O Avisos Frecuentes

- `no se encuentra la actividad`
- `hay un error, no se ha guardado`
- `Aviso de calendario (la actividad aún no tiene plazas en el calendario común).`

### Permisos

- El caso de uso no aplica un control de permisos propio: la edición por celda se decide con los flags
- Sin control de permisos propio; la editabilidad de la celda la decide `GestionPlazasData` (flags

### Referencias Internas

- Flujo: `actividadplazas.gestion_plazas.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadplazas/flujos/gestion_plazas.md`

## Guardar las peticiones de una persona

### Para Que Sirve

Definir, o borrar, la lista ordenada de actividades que pide una persona como plaza, para un tipo de actividad y un colectivo (n, a o agd).

### Donde Entrar

- Peticiones Activ (frontend/actividadplazas/controller/peticiones_activ.php)
- Sin entrada de menú en el índice: se abre desde la selección de una persona (colectivos n / a / agd),
- no directamente desde un menú.

### Tareas Habituales

#### Guardar las peticiones de una persona

1. En la pantalla de peticiones, ordenar las actividades con los desplegables: la primera es la preferida.
2. Si hacen falta más filas, pulsar **más actividades**.
3. Pulsar **guardar**. Las peticiones anteriores de esa persona se sustituyen por las nuevas, en ese orden.
4. Al terminar, la pantalla vuelve a la anterior.

#### Borrar las peticiones de una persona

1. En la misma pantalla, pulsar **Borrar**.
2. Se eliminan todas las peticiones de esa persona para ese tipo de actividad y la pantalla se refresca.

### Errores O Avisos Frecuentes

- `faltan parametros id_nom / sactividad`
- `hay un error, no se ha podido eliminar`
- `hay un error, no se han guardado todas las peticiones`

### Permisos

- Sin control de permisos propio; la autorización de oficina se resuelve en frontend

### Referencias Internas

- Flujo: `actividadplazas.peticiones.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadplazas/flujos/peticiones.md`

## Abrir las peticiones de una persona

### Para Que Sirve

Ver las peticiones de plaza de una persona: su nombre, las actividades disponibles del tipo elegido y las peticiones ya guardadas, listas para reordenar o ampliar.

### Donde Entrar

- Peticiones Activ (frontend/actividadplazas/controller/peticiones_activ.php)
- Sin entrada de menú en el índice: se abre desde la selección de una persona (colectivos n / a / agd),
- no directamente desde un menú.

### Tareas Habituales

#### Abrir las peticiones de una persona

1. Desde un listado de personas (n, a o agd), seleccionar a la persona.
2. Aparecen las actividades que puede pedir y las peticiones que ya tenía; las que ya no están disponibles se descartan.
3. Los desplegables salen con el orden de prioridad guardado. Se pueden cambiar y guardar, o borrar todo (ver «Guardar las peticiones de una persona»).

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio; la autorización de oficina se resuelve en frontend

### Referencias Internas

- Flujo: `actividadplazas.peticiones_activ.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadplazas/flujos/peticiones_activ.md`

## Incorporar las primeras peticiones

### Para Que Sirve

- Convertir de golpe las primeras peticiones de plaza de cada persona en asistencias propias con plaza, para un tipo de actividad y un colectivo.
- No se incorpora a quien ya tiene una actividad propia en ese periodo.

### Donde Entrar

- Incorporar Peticion (frontend/actividadplazas/controller/incorporar_peticion.php)
- **Legacy:** vsm > ca > Incorporar 1ª petición (y variantes por perfil/tipo: dagd, crt…)
- **Pills2:** ACTIVIDADES > Gestión de plazas y peticiones > Incorporar 1ª petición > ca n (y variantes por tipo/colectivo)

### Tareas Habituales

#### Incorporar las primeras peticiones

1. Abrir **Incorporar peticiones de plazas** desde el menú (según el tipo de actividad y el colectivo).
2. Leer el texto explicativo y pulsar **Continuar**. El botón se desactiva mientras se ejecuta.
3. Al terminar se muestra cuántas peticiones se han incorporado, con el recordatorio de que no se incorpora a quien ya tiene actividad propia.

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado`

### Permisos

- Sin control de permisos propio; la operación es sobre mi dl (`ConfigGlobal::mi_delef()`) y la

### Referencias Internas

- Flujo: `actividadplazas.peticiones_incorporar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadplazas/flujos/peticiones_incorporar.md`

## Comparar con otra delegación

### Para Que Sirve

Comparar, para un tipo de actividad, las plazas concedidas y libres de cada actividad en la propia delegación frente a otra delegación elegida.

### Donde Entrar

- Plazas Balance Dl (frontend/actividadplazas/controller/plazas_balance_dl.php)
- Sin entrada de menú en el índice: fragmento invocado desde **Balance de plazas** (`plazas_balance_que`):
- **Legacy:** vsm > ca > Balance de plazas (y variantes por perfil/tipo: dagd, vsg…)
- **Pills2:** ACTIVIDADES > Gestión de plazas y peticiones > Balance plazas ca n entre r/dl (y variantes por tipo/colectivo)

### Tareas Habituales

#### Comparar con otra delegación

1. En **Balance de plazas**, elegir en el desplegable la delegación con la que comparar.
2. Debajo aparece la tabla: arriba, las plazas que se conceden mutuamente las dos delegaciones, y por actividad las concedidas y las libres.
3. Las celdas de la propia delegación se pueden cambiar con doble clic; se guarda al momento.

### Errores O Avisos Frecuentes

- `falta parametro dl`
- `no se puede comparar una dl consigo misma`

### Permisos

- Sin control de permisos propio; la editabilidad de celdas depende de que la dl sea la mía y la

### Referencias Internas

- Flujo: `actividadplazas.plazas_balance.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadplazas/flujos/plazas_balance.md`

## Abrir el balance de plazas

### Para Que Sirve

Entrar en el balance de plazas entre delegaciones y elegir con qué delegación comparar la propia.

### Donde Entrar

- Plazas Balance Que (frontend/actividadplazas/controller/plazas_balance_que.php)
- **Legacy:** vsm > ca > Balance de plazas (y variantes por perfil/tipo: dagd, vsg…)
- **Pills2:** ACTIVIDADES > Gestión de plazas y peticiones > Balance plazas ca n entre r/dl (y variantes por tipo/colectivo)

### Tareas Habituales

#### Abrir el balance de plazas

1. Abrir **Balance de plazas** desde el menú (según el tipo de actividad y el colectivo).
2. Elegir la delegación en el desplegable.
3. La comparación aparece debajo, sin salir de la pantalla (ver «Comparar con otra delegación»).

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio; la autorización de oficina se resuelve en frontend

### Referencias Internas

- Flujo: `actividadplazas.plazas_balance_que.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadplazas/flujos/plazas_balance_que.md`

## Ceder plazas a otra delegación

### Para Que Sirve

Desde el resumen de plazas de una actividad, indicar cuántas plazas se ceden a otra delegación y confirmar el cambio.

### Donde Entrar

- Resumen Plazas (frontend/actividadplazas/controller/resumen_plazas.php)
- Sin entrada de menú en el índice: se ejecuta desde el resumen de plazas de una actividad (menú
- «Plazas» de la actividad), no directamente desde un menú.

### Tareas Habituales

#### Ceder plazas a otra delegación

1. Abrir el resumen de plazas de la actividad.
2. En el bloque **Ceder**, escribir el número de plazas y elegir la delegación que las recibe.
3. Pulsar **Guardar**.
4. La pantalla se recarga y muestra el nuevo reparto.

### Errores O Avisos Frecuentes

- `faltan parametros id_activ / region_dl`
- `hay un error, no se ha guardado`

### Permisos

- Sin control de permisos propio; solo se ceden plazas de mi dl (`ConfigGlobal::mi_delef()`) y la

### Referencias Internas

- Flujo: `actividadplazas.plazas_ceder.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadplazas/flujos/plazas_ceder.md`

## Elegir la delegación propietaria de la plaza

### Para Que Sirve

Al editar la asistencia de una persona a una actividad, elegir qué delegación es la propietaria de la plaza entre las opciones válidas para esa persona y esa actividad.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- Sin entrada de menú en el índice: se usa desde formularios de **asistentes**, no desde un menú de plazas.

### Tareas Habituales

#### Elegir la delegación propietaria de la plaza

1. Abrir el formulario de asistencia, bien desde una persona o bien desde una actividad.
2. En **propiedad de** aparecen solo las delegaciones posibles para esa combinación; por defecto sale preseleccionada la propia delegación.
3. Elegir la que corresponda y guardar el formulario.

### Errores O Avisos Frecuentes

- `faltan parametros id_nom / id_activ`
- `No se encuentra persona con id_nom <id>`

### Permisos

- Sin control de permisos propio; se invoca desde los formularios de asistentes al asignar plaza y la

### Referencias Internas

- Flujo: `actividadplazas.posibles_propietarios.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadplazas/flujos/posibles_propietarios.md`

## Ver el resumen de plazas de una actividad

### Para Que Sirve

Ver cuántas plazas tiene cada delegación en una actividad y ceder plazas a otra.

### Donde Entrar

- Resumen Plazas (frontend/actividadplazas/controller/resumen_plazas.php)
- Sin entrada de menú en el índice: se abre desde una actividad concreta (menú «Plazas» de la
- actividad), no directamente desde un menú.

### Tareas Habituales

#### Ver el resumen de plazas de una actividad

1. Marcar la actividad y pulsar **plazas**.
2. Si solo se ven las plazas de la propia dl, avisa «Sólo se ven las ocupadas por la propia dl».
3. La tabla tiene una fila por delegación con **calendario**, **cedidas**, **conseguidas**, **total**, **ocupadas** y **libres**; debajo de cada una se indica a quién cedió o de quién consiguió plazas (por ejemplo «1 a dlb», «3 de dlmE») y una fila de **totales**.
4. Debajo hay un bloque «ceder [número] plazas a [delegación] **Guardar**» (ver «Ceder plazas»).

### Errores O Avisos Frecuentes

- `falta parametro id_activ`

### Permisos

- Sin control de permisos propio; la autorización de oficina se resuelve en frontend

### Referencias Internas

- Flujo: `actividadplazas.resumen_plazas.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadplazas/flujos/resumen_plazas.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/actividadplazas/flujos/`.
