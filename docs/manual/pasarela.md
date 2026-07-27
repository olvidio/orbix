---
tipo: "manual_usuario"
modulo: "pasarela"
flujos: 14
estado_revision: "revisado_parcial"
---

# Manual De Usuario - pasarela

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## fecha de activación

### Para Que Sirve

Configurar cuándo se publica/activa cada tipo de actividad en la pasarela exterior.

### Donde Entrar

- 
- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

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

- 
- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `Falta valor por defecto`

### Referencias Internas

- Flujo: `pasarela.activacion_default.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/pasarela/flujos/activacion_default.md`

## Excepción de activación por tipo

### Para Que Sirve

Definir activación distinta para un tipo de actividad concreto.

### Donde Entrar

- 
- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `Falta id_tipo_activ`
- `Falta valor de activación`

### Referencias Internas

- Flujo: `pasarela.activacion_excepcion.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/pasarela/flujos/activacion_excepcion.md`

## contribución no duerme

### Para Que Sirve

Porcentaje de contribución para quien no pernocta.

### Donde Entrar

- 
- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

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

Cambiar el porcentaje global.

### Donde Entrar

- 
- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `Falta valor por defecto`
- `Debe ser un numero entero del 1 al 100`

### Referencias Internas

- Flujo: `pasarela.contribucion_no_duerme_default.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/pasarela/flujos/contribucion_no_duerme_default.md`

## Excepción contribución no duerme

### Para Que Sirve

Porcentaje distinto por tipo de actividad.

### Donde Entrar

- 
- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `Falta id_tipo_activ`
- `Falta valor de contribución`
- `Debe ser un numero entero del 1 al 100`

### Referencias Internas

- Flujo: `pasarela.contribucion_no_duerme_excepcion.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/pasarela/flujos/contribucion_no_duerme_excepcion.md`

## contribución reserva

### Para Que Sirve

Porcentaje de contribución en reserva de plaza.

### Donde Entrar

- 
- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

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

Cambiar porcentaje global de reserva.

### Donde Entrar

- 
- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `Falta valor por defecto`
- `Debe ser un numero entero del 1 al 100`

### Referencias Internas

- Flujo: `pasarela.contribucion_reserva_default.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/pasarela/flujos/contribucion_reserva_default.md`

## Excepción contribución reserva

### Para Que Sirve

Porcentaje de reserva distinto por tipo.

### Donde Entrar

- 
- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

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

- 
- **Legacy:** dre > Pasarela > exportar actividades
- **Pills2:** dre > Pasarela > exportar actividades; ACTIVIDADES > Pasarela > exportar actividades

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

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

- 
- **Legacy:** dre > Pasarela > exportar actividades
- **Pills2:** dre > Pasarela > exportar actividades; ACTIVIDADES > Pasarela > exportar actividades

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `pasarela.exportar_que_actividad_tipo_html.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/pasarela/flujos/exportar_que_actividad_tipo_html.md`

## nombres particulares

### Para Que Sirve

Asignar nombre exportado distinto al tipo genérico.

### Donde Entrar

- 
- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

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

- 
- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- `Falta id_tipo_activ`
- `Falta nombre`

### Referencias Internas

- Flujo: `pasarela.nombre_excepcion.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/pasarela/flujos/nombre_excepcion.md`

## Texto descriptivo del tipo

### Para Que Sirve

Mostrar etiqueta legible del tipo al editar excepciones.

### Donde Entrar

- 
- sin entrada de menú en el índice (acceso desde `parametros_menu` o dispatcher AJAX embebido).

### Tareas Habituales

Consulte el flujo en el catálogo o la pantalla indicada; no se han inferido pasos detallados.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Referencias Internas

- Flujo: `pasarela.tipo_activ_txt.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/pasarela/flujos/tipo_activ_txt.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/pasarela/flujos/`.
