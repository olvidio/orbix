---
tipo: "manual_usuario"
modulo: "pasarela"
flujos: 15
estado_revision: "revisado"
---

# Manual De Usuario - pasarela

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## fecha de activación

### Para Que Sirve

Configurar cuándo se publica/activa cada tipo de actividad en la pasarela exterior.

### Donde Entrar

- Fecha de activación (frontend/pasarela/controller/activacion_lista.php)
- **Legacy:** dre > Pasarela > parámetros > fecha de activación
- **Pills2:** ACTIVIDADES > Pasarela > parámetros > fecha de activación

### Tareas Habituales

#### Consultar las fechas de activación

1. Abrir **Pasarela > parámetros** y pulsar **fecha de activación**.
2. La lista muestra primero **por defecto** (por ejemplo «3 días») y luego una fila por cada tipo con valor propio (por ejemplo «sv n crt (sin especificar)»: upload; «sv n crt sin ce»: 5 días).

#### Cambiar el valor general

1. Pulsar el botón de modificar el valor por defecto.
2. Indicar el **número de días antes del inicio de la actividad** (o la palabra `upload` para activar el día en que se hace la exportación).
3. Pulsar **guardar**.

#### Poner una fecha distinta a un tipo de actividad

1. Pulsar **nuevo**.
2. Elegir el tipo de actividad con tres desplegables encadenados — **asistentes** (n, agd, s, sg, sssc, sr), **actividad** (crt, ca, cv, cve, cv-crt) y **tipo de actividad** — e indicar el número de días (o `upload`).
3. Pulsar **guardar**. Para quitar la excepción, abrir la fila y pulsar **eliminar** (pide confirmación).

### Errores O Avisos Frecuentes

- `Falta valor por defecto`
- `Falta id_tipo_activ`
- `Falta valor de activación`

### Referencias Internas

- Flujo: `pasarela.activacion.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/pasarela/flujos/activacion.md`

## Editar activación por defecto

### Para Que Sirve

Cambiar el valor global de días de activación (o `upload`).

### Donde Entrar

- Dispatcher AJAX activación (frontend/pasarela/controller/activacion_ajax.php)
- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).

### Tareas Habituales

#### Cambiar la fecha de activación general

1. En **fecha de activación**, abrir el valor por defecto.
2. Indicar el **número de días antes del inicio de la actividad** (o `upload`).
3. Pulsar **guardar** (o **cancel**). Si se deja vacío avisa de que falta el valor por defecto.

### Errores O Avisos Frecuentes

- `Falta valor por defecto`

### Referencias Internas

- Flujo: `pasarela.activacion_default.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/pasarela/flujos/activacion_default.md`

## Excepción de activación por tipo

### Para Que Sirve

Definir activación distinta para un tipo de actividad concreto.

### Donde Entrar

- Dispatcher AJAX activación (frontend/pasarela/controller/activacion_ajax.php)
- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).

### Tareas Habituales

#### Añadir o cambiar la fecha de un tipo

1. Pulsar **nuevo**, o pulsar el valor de una fila (por ejemplo «5 días» o «upload»): se abre una ventana «Fecha de activación para: …» con el valor y los botones **guardar**, **eliminar** y **cancel**.
2. Elegir el tipo con los desplegables **asistentes**, **actividad** y **tipo de actividad** (cada uno depende del anterior).
3. Indicar el **número de días antes del inicio de la actividad** (o `upload`) y pulsar **guardar**. Si falta el tipo avisa «Tipo no válido»; si falta el valor, «debe indicar un periodo». Aviso: en la instancia de desarrollo (3-11-2026), guardar una excepción ya existente responde «respuesta: Operación no autorizada» (el formulario no envía el permiso que el servidor exige); alta nueva sin comprobar.

#### Quitar la excepción de un tipo

1. Abrir la fila del tipo.
2. Pulsar **eliminar** y confirmar «¿Está seguro que quiere eliminar esta fila?».
3. El tipo vuelve a usar el valor general.

### Errores O Avisos Frecuentes

- `Falta id_tipo_activ`
- `Falta valor de activación`

### Referencias Internas

- Flujo: `pasarela.activacion_excepcion.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/pasarela/flujos/activacion_excepcion.md`

## contribución no duerme

### Para Que Sirve

Importe de contribución para quien no pernocta.

### Donde Entrar

- Contribución no duerme (frontend/pasarela/controller/contribucion_no_duerme_lista.php)
- **Legacy:** dre > Pasarela > parámetros > Contribución no duerme
- **Pills2:** ACTIVIDADES > Pasarela > parámetros > Contribución no duerme

### Tareas Habituales

#### Consultar el importe

1. Abrir **Pasarela > parámetros** y pulsar **Contribución no duerme**.
2. Leer el importe general y las excepciones por tipo de actividad.

#### Cambiar el importe general

1. Pulsar el botón de modificar el valor por defecto.
2. Escribir un número entero de euros entre 0 y 100, que es lo que acepta la pantalla.
3. Pulsar **guardar**.

#### Poner un importe distinto a un tipo de actividad

1. Pulsar **nuevo**.
2. Elegir el tipo de actividad y escribir el importe (entero de euros entre 0 y 100, que es lo que acepta la pantalla).
3. Pulsar **guardar**; con **eliminar** la fila el tipo vuelve al importe general.

### Errores O Avisos Frecuentes

- `Falta valor por defecto`
- `Debe ser un numero entero del 1 al 100`
- `Falta id_tipo_activ`
- `Falta valor de contribución`

### Referencias Internas

- Flujo: `pasarela.contribucion_no_duerme.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/pasarela/flujos/contribucion_no_duerme.md`

## Default contribución no duerme

### Para Que Sirve

Cambiar el importe global.

### Donde Entrar

- Dispatcher AJAX contribución no duerme (frontend/pasarela/controller/contribucion_no_duerme_ajax.php)
- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).

### Tareas Habituales

#### Cambiar el importe general

1. Abrir el valor por defecto.
2. Escribir un número entero de euros entre 0 y 100, que es lo que acepta la pantalla.
3. Pulsar **guardar** (o **cancel**). Fuera de rango o vacío, el sistema avisa.

### Errores O Avisos Frecuentes

- `Falta valor por defecto`
- `Debe ser un numero entero del 1 al 100`

### Referencias Internas

- Flujo: `pasarela.contribucion_no_duerme_default.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/pasarela/flujos/contribucion_no_duerme_default.md`

## Excepción contribución no duerme

### Para Que Sirve

Importe distinto por tipo de actividad.

### Donde Entrar

- Dispatcher AJAX contribución no duerme (frontend/pasarela/controller/contribucion_no_duerme_ajax.php)
- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).

### Tareas Habituales

#### Añadir, cambiar o quitar un importe por tipo

1. Pulsar **nuevo** (o abrir una fila).
2. Elegir el tipo de actividad y escribir el importe (entero de euros entre 0 y 100, que es lo que acepta la pantalla).
3. Pulsar **guardar**, o **eliminar** para quitar la excepción.

### Errores O Avisos Frecuentes

- `Falta id_tipo_activ`
- `Falta valor de contribución`
- `Debe ser un numero entero del 1 al 100`

### Referencias Internas

- Flujo: `pasarela.contribucion_no_duerme_excepcion.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/pasarela/flujos/contribucion_no_duerme_excepcion.md`

## contribución reserva

### Para Que Sirve

Importe de contribución en reserva de plaza.

### Donde Entrar

- Contribución reserva (frontend/pasarela/controller/contribucion_reserva_lista.php)
- **Legacy:** dre > Pasarela > parámetros > Contribución reserva
- **Pills2:** ACTIVIDADES > Pasarela > parámetros > Contribución reserva

### Tareas Habituales

#### Consultar el importe

1. Abrir **Pasarela > parámetros** y pulsar **Contribución reserva**.
2. Leer el importe general y las excepciones por tipo de actividad.

#### Cambiar el importe general

1. Pulsar el botón de modificar el valor por defecto.
2. Escribir un número entero de euros entre 0 y 100, que es lo que acepta la pantalla.
3. Pulsar **guardar**.

#### Poner un importe distinto a un tipo de actividad

1. Pulsar **nuevo**.
2. Elegir el tipo de actividad y escribir el importe (entero de euros entre 0 y 100, que es lo que acepta la pantalla).
3. Pulsar **guardar**; con **eliminar** la fila el tipo vuelve al importe general.

### Errores O Avisos Frecuentes

- `Falta valor por defecto`
- `Debe ser un numero entero del 1 al 100`
- `Falta id_tipo_activ`
- `Falta valor de contribución`

### Referencias Internas

- Flujo: `pasarela.contribucion_reserva.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/pasarela/flujos/contribucion_reserva.md`

## Default contribución reserva

### Para Que Sirve

Cambiar el importe general de reserva.

### Donde Entrar

- Dispatcher AJAX contribución reserva (frontend/pasarela/controller/contribucion_reserva_ajax.php)
- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).

### Tareas Habituales

#### Cambiar el importe general

1. Abrir el valor por defecto.
2. Escribir un número entero de euros entre 0 y 100, que es lo que acepta la pantalla.
3. Pulsar **guardar** (o **cancel**). Fuera de rango o vacío, el sistema avisa.

### Errores O Avisos Frecuentes

- `Falta valor por defecto`
- `Debe ser un numero entero del 1 al 100`

### Referencias Internas

- Flujo: `pasarela.contribucion_reserva_default.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/pasarela/flujos/contribucion_reserva_default.md`

## Excepción contribución reserva

### Para Que Sirve

Importe de reserva distinto por tipo.

### Donde Entrar

- Dispatcher AJAX contribución reserva (frontend/pasarela/controller/contribucion_reserva_ajax.php)
- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).

### Tareas Habituales

#### Añadir, cambiar o quitar un importe por tipo

1. Pulsar **nuevo** (o abrir una fila).
2. Elegir el tipo de actividad y escribir el importe (entero de euros entre 0 y 100, que es lo que acepta la pantalla).
3. Pulsar **guardar**, o **eliminar** para quitar la excepción.

### Errores O Avisos Frecuentes

- `Falta id_tipo_activ`
- `Falta valor de contribución`
- `Debe ser un numero entero del 1 al 100`

### Referencias Internas

- Flujo: `pasarela.contribucion_reserva_excepcion.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/pasarela/flujos/contribucion_reserva_excepcion.md`

## Exportar actividades al exterior

### Para Que Sirve

Generar listado tabular con datos de actividades filtradas para sistemas externos.

### Donde Entrar

- Exportar actividades (frontend/pasarela/controller/exportar_que.php)
- **Legacy:** dre > Pasarela > exportar actividades
- **Pills2:** dre > Pasarela > exportar actividades; ACTIVIDADES > Pasarela > exportar actividades

### Tareas Habituales

#### Exportar actividades al exterior

1. Abrir **Pasarela > exportar actividades**.
2. Elegir el tipo de actividad y el **periodo**.
3. Pulsar **ver selección**.
4. Se muestra la tabla de actividades preparada para el sistema externo.
5. Leer los avisos: indican tarifas o fechas de activación que faltan para alguna actividad.

### Errores O Avisos Frecuentes

- `Periodo no válido`
- `valor no válido para la activación del tipo de actividad %s`
- `No está definido el tipo tarifa...`
- `No está definida la id_tarifa...`

### Referencias Internas

- Flujo: `pasarela.exportar_actividades.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/pasarela/flujos/exportar_actividades.md`

## Selector tipo en exportar

### Para Que Sirve

Refrescar widget de tipo de actividad en la pantalla exportar.

### Donde Entrar

- Exportar actividades (frontend/pasarela/controller/exportar_que.php)
- **Legacy:** dre > Pasarela > exportar actividades
- **Pills2:** dre > Pasarela > exportar actividades; ACTIVIDADES > Pasarela > exportar actividades

### Tareas Habituales

#### Elegir el tipo de actividad

1. Abrir **exportar actividades**.
2. Usar los desplegables de **seleccionar un tipo de actividad**; el selector se actualiza solo según lo elegido.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `pasarela.exportar_que_actividad_tipo_html.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/pasarela/flujos/exportar_que_actividad_tipo_html.md`

## nombres particulares

### Para Que Sirve

Asignar nombre exportado distinto al tipo genérico.

### Donde Entrar

- Nombres de actividades particulares (frontend/pasarela/controller/nombre_lista.php)
- **Legacy:** dre > Pasarela > parámetros > nombres de actividades particulares
- **Pills2:** ACTIVIDADES > Pasarela > parámetros > nombres de actividades particulares

### Tareas Habituales

#### Consultar los nombres particulares

1. Abrir **Pasarela > parámetros** y pulsar **nombres de actividades particulares**.
2. Leer la lista de tipos de actividad con nombre propio.

#### Poner un nombre a un tipo de actividad

1. Pulsar **nuevo**.
2. Elegir el tipo de actividad y escribir el **nombre actividad** que se exportará.
3. Pulsar **guardar**.

#### Cambiar o quitar un nombre

1. Abrir la fila del tipo.
2. Cambiar el nombre y pulsar **guardar**, o pulsar **eliminar** y confirmar «¿Está seguro que desea borrar este nombre?».

### Errores O Avisos Frecuentes

- `Falta id_tipo_activ`
- `Falta nombre`

### Referencias Internas

- Flujo: `pasarela.nombre.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/pasarela/flujos/nombre.md`

## Alta/edición nombre por tipo

### Para Que Sirve

Guardar o borrar un nombre concreto.

### Donde Entrar

- Dispatcher AJAX nombre (frontend/pasarela/controller/nombre_ajax.php)
- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).

### Tareas Habituales

#### Guardar o borrar el nombre de un tipo

1. Pulsar **nuevo** (o abrir una fila).
2. Elegir el tipo de actividad y escribir el nombre.
3. Pulsar **guardar**, o **eliminar** si se quiere quitar. Sin tipo o sin nombre el sistema avisa de lo que falta.

### Errores O Avisos Frecuentes

- `Falta id_tipo_activ`
- `Falta nombre`

### Referencias Internas

- Flujo: `pasarela.nombre_excepcion.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/pasarela/flujos/nombre_excepcion.md`

## Menú de parámetros de la pasarela

### Para Que Sirve

- La pasarela es el sistema web externo donde las personas se inscriben en las actividades y pagan; Orbix le publica las actividades.
- Elegir qué parámetro de la pasarela se quiere revisar o cambiar.
- Cada opción abre su propio listado en la misma ventana.
- Los importes de contribución se expresan en euros.

### Donde Entrar

- Parámetros pasarela (frontend/pasarela/controller/parametros_menu.php)
- **Legacy:** dre > Pasarela > parámetros
- **Pills2:** ACTIVIDADES > Pasarela > parámetros

### Tareas Habituales

#### Abrir un parámetro

1. Entrar en **Pasarela > parámetros** desde el menú.
2. Pulsar el enlace del parámetro que interesa (por ejemplo **fecha de activación**).
3. Trabajar con el listado que aparece; los pasos de cada uno están en su propio apartado de este manual.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `pasarela.parametros_menu.consultar.flujo`
- Fichero catalogo: `docs/catalogo/pasarela/flujos/parametros_menu.md`

## Texto descriptivo del tipo

### Para Que Sirve

Mostrar etiqueta legible del tipo al editar excepciones.

### Donde Entrar

- 
- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).

### Tareas Habituales

#### Ver el nombre del tipo al editar

1. Abrir una fila de un parámetro (fecha de activación, nombre o contribución).
2. El sistema muestra el nombre completo del tipo de actividad (sección, asistentes y actividad) para saber qué se está editando.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `pasarela.tipo_activ_txt.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/pasarela/flujos/tipo_activ_txt.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/pasarela/flujos/`.
