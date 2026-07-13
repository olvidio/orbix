---
tipo: "manual_usuario"
modulo: "actividadplazas"
flujos: 9
estado_revision: "generado"
---

# Manual De Usuario - actividadplazas

Este manual es un borrador generado desde `docs/catalogo`. Debe revisarse para ajustar nombres de menu, permisos, validaciones y lenguaje final de usuario.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas de menu y nombres visibles pueden necesitar revision manual.

## Gestion Plazas

### Para Que Sirve

Ver, para un periodo y tipo de actividad, cuántas plazas tiene cada actividad y cómo se reparten (concedidas/pedidas) entre las delegaciones del grupo, y ajustar esos valores desde la propia tabla.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Elegir el periodo (año + periodo, o rango de fechas) y pulsar **Buscar**.
2. El sistema carga el cuadro desde `gestion_plazas_data` (actividades × delegaciones).

#### Crear o modificar

1. Localizar la actividad en la tabla.
2. Doble clic en una celda editable (total, concedidas `-c` o pedidas `-p` de mi delegación).
3. Escribir el nuevo valor; se guarda al instante vía `gestion_plazas_update`.
4. Si la actividad no tiene plazas en el calendario común, se muestra el aviso para darlas de alta antes.

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

## Peticiones

### Para Que Sirve

Definir (o borrar) la lista priorizada de actividades que una persona solicita como petición de plaza para un tipo y colectivo (`n`, `a`, `agd`).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Guardar

1. En la pantalla de peticiones, ordenar las actividades con los desplegables (`DesplegableArray`).
2. Añadir filas con **más actividades** (`fnjs_mas_actividades`) si hace falta.
3. Pulsar el botón de guardar (`fnjs_guardar`): envía `id_nom`, `sactividad` y la lista ordenada a
4. Si tiene éxito, vuelve atrás (`fnjs_nav_atras`).

#### Eliminar

1. En la misma pantalla, pulsar **Borrar** (`fnjs_borrar`).
2. El sistema elimina todas las peticiones de esa persona+tipo vía `peticiones_eliminar`.
3. Si tiene éxito, refresca la pantalla (`fnjs_actualizar`).

### Errores O Avisos Frecuentes

- `faltan parametros id_nom / sactividad`
- `hay un error, no se ha podido eliminar`
- `hay un error, no se han guardado todas las peticiones`

### Permisos

- Sin control de permisos propio; la autorización de oficina se resuelve en frontend

### Referencias Internas

- Flujo: `actividadplazas.peticiones.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadplazas/flujos/peticiones.md`

## Peticiones Activ

### Para Que Sirve

Consultar y preparar la edición de las peticiones de plaza de una persona: ver su nombre, las actividades disponibles del tipo y las peticiones ya guardadas, listas para reordenar o ampliar.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Desde un listado de personas (n / a / agd), abrir las peticiones de plaza de una persona.
2. El sistema carga `peticiones_activ_data` con `id_nom` y `sactividad`.
3. Devuelve las actividades candidatas y las peticiones actuales; limpia peticiones antiguas ya no
4. Pinta los desplegables (`DesplegableArray`) precargados con el orden de prioridad; el usuario

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio; la autorización de oficina se resuelve en frontend

### Referencias Internas

- Flujo: `actividadplazas.peticiones_activ.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadplazas/flujos/peticiones_activ.md`

## Peticiones Incorporar

### Para Que Sirve

Ejecutar el proceso masivo que convierte las primeras peticiones de plaza (orden = 1) en asistencias propias con plaza, para un tipo y colectivo, sin incorporar personas que ya tienen actividad propia en el periodo.

### Donde Entrar

- Incorporar Peticion (frontend/actividadplazas/controller/incorporar_peticion.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Abrir **Incorporar peticiones de plazas** desde el menú (según tipo y colectivo).
2. Leer el texto explicativo y pulsar **Continuar** (`fnjs_incorporar_peticiones`).
3. El botón se deshabilita mientras se ejecuta; el sistema envía `sactividad` y `sasistentes` a
4. Muestra en `#resultado` cuántas peticiones se incorporaron (`incorporadas`) y el aviso de que no

### Errores O Avisos Frecuentes

- `hay un error, no se ha guardado`

### Permisos

- Sin control de permisos propio; la operación es sobre mi dl (`ConfigGlobal::mi_delef()`) y la

### Referencias Internas

- Flujo: `actividadplazas.peticiones_incorporar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadplazas/flujos/peticiones_incorporar.md`

## Plazas Balance

### Para Que Sirve

Comparar, para un tipo de actividad, cuántas plazas concedidas y libres tiene cada actividad en mi delegación frente a otra delegación elegida en el desplegable.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. En **Balance de plazas**, elegir la delegación a comparar en el desplegable.
2. El sistema carga el HTML del grid en `#comparativa` vía `plazas_balance_dl.php`.
3. Ese fragmento obtiene los datos de `plazas_balance_data` (`dlA` = mi dl, `dlB` = la elegida):
4. Las celdas de mi dl son editables (doble clic → `gestion_plazas_update`).

### Errores O Avisos Frecuentes

- `falta parametro dl`
- `no se puede comparar una dl consigo misma`

### Permisos

- Sin control de permisos propio; la editabilidad de celdas depende de que la dl sea la mía y la

### Referencias Internas

- Flujo: `actividadplazas.plazas_balance.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadplazas/flujos/plazas_balance.md`

## Plazas Balance Que

### Para Que Sirve

Acceder al balance de plazas entre delegaciones, elegir con qué dl comparar la propia, y ver el grid comparativo que se carga debajo.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Abrir **Balance de plazas** desde el menú (según tipo y colectivo).
2. El sistema carga `plazas_balance_que_data`: opciones del desplegable de delegaciones e
3. Al elegir una delegación (`fnjs_comparativa`), solicita por AJAX `plazas_balance_dl` e inserta el

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio; la autorización de oficina se resuelve en frontend

### Referencias Internas

- Flujo: `actividadplazas.plazas_balance_que.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadplazas/flujos/plazas_balance_que.md`

## Plazas Ceder

### Para Que Sirve

Desde el resumen de plazas de una actividad, indicar cuántas plazas ceder a otra delegación y confirmar el cambio.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Abrir el resumen de plazas de la actividad.
2. En el bloque **Ceder**, escribir el número de plazas y elegir la delegación destino.
3. Pulsar **Guardar** (`fnjs_guardar`): envía `id_activ`, `num_plazas` y `region_dl` a
4. Si tiene éxito, la pantalla se recarga (`fnjs_actualizar`) mostrando el nuevo reparto.

### Errores O Avisos Frecuentes

- `faltan parametros id_activ / region_dl`
- `hay un error, no se ha guardado`

### Permisos

- Sin control de permisos propio; solo se ceden plazas de mi dl (`ConfigGlobal::mi_delef()`) y la

### Referencias Internas

- Flujo: `actividadplazas.plazas_ceder.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadplazas/flujos/plazas_ceder.md`

## Posibles Propietarios

### Para Que Sirve

Al editar la asistencia de una persona en una actividad (o viceversa), elegir qué delegación es propietaria de la plaza entre las opciones válidas para esa combinación persona+actividad.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Abrir el formulario de asistencia (persona↔actividad) desde el módulo asistentes.
2. Al cargar o cambiar persona/actividad, el frontend solicita `posibles_propietarios_data` con
3. El sistema devuelve el payload estándar de desplegable (`id`, `opciones`, `selected`, `blanco`,

### Errores O Avisos Frecuentes

- `faltan parametros id_nom / id_activ`
- `No se encuentra persona con id_nom <id>`

### Permisos

- Sin control de permisos propio; se invoca desde los formularios de asistentes al asignar plaza y la

### Referencias Internas

- Flujo: `actividadplazas.posibles_propietarios.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadplazas/flujos/posibles_propietarios.md`

## Resumen Plazas

### Para Que Sirve

Ver el estado completo de plazas de una actividad (por dl y totales), comprobar avisos de publicación o visibilidad, y acceder al formulario para ceder plazas a otra delegación.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Desde una actividad, abrir la opción de plazas/resumen.
2. El sistema carga `resumen_plazas_data` con el desglose por delegación y totales.
3. Muestra avisos si la actividad no está publicada o si solo se ven las ocupadas por la propia dl
4. Pinta la tabla (calendario, cedidas, conseguidas, disponibles, ocupadas, libres) y el desplegable

### Errores O Avisos Frecuentes

- `falta parametro id_activ`

### Permisos

- Sin control de permisos propio; la autorización de oficina se resuelve en frontend

### Referencias Internas

- Flujo: `actividadplazas.resumen_plazas.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadplazas/flujos/resumen_plazas.md`

## Revision Pendiente

- Sustituir nombres tecnicos por nombres visibles en la aplicacion.
- Completar rutas de menu.
- Confirmar permisos necesarios.
- Anadir capturas o ejemplos si se quiere publicar para usuarios finales.
