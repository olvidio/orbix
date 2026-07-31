---
tipo: "manual_usuario"
modulo: "ubis"
flujos: 36
estado_revision: "revisado_parcial"
---

# Manual De Usuario - ubis

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## Calendario Periodos

### Para Que Sirve

Elimina un periodo de calendario CDC identificado por id_item.

### Donde Entrar

- Calendario Periodos (frontend/ubis/controller/calendario_periodos.php)
- **Legacy:** adl > Nuevo Calendario > Definir periodos
- **Pills2:** ACTIVIDADES > Herramientas de calendario > Definir periodos

### Tareas Habituales

#### Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

#### Guardar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `no sé cuál he de borar`
- `no se encuentra el periodo a borrar`
- `hay un error, no se ha eliminado`

### Referencias Internas

- Flujo: `ubis.calendario_periodos.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/calendario_periodos.md`

## Calendario Periodos Form Periodo

### Para Que Sirve

Carga los campos del formulario de edición de un periodo de calendario existente.

### Donde Entrar

- Calendario Periodos Form Periodo (frontend/ubis/controller/calendario_periodos_form_periodo.php)
- **Legacy:** adl > Nuevo Calendario > Definir periodos
- **Pills2:** ACTIVIDADES > Herramientas de calendario > Definir periodos

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.calendario_periodos_form_periodo.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/calendario_periodos_form_periodo.md`

## Calendario Periodos Get

### Para Que Sirve

Devuelve todos los periodos de calendario de una casa ordenados por fecha inicio.

### Donde Entrar

- Calendario Periodos Get (frontend/ubis/controller/calendario_periodos_get.php)
- **Legacy:** adl > Nuevo Calendario > Definir periodos
- **Pills2:** ACTIVIDADES > Herramientas de calendario > Definir periodos

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.calendario_periodos_get.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/calendario_periodos_get.md`

## Calendario Periodos Get2

### Para Que Sirve

Lista los periodos de una casa en un año con detección de solapes.

### Donde Entrar

- Calendario Periodos Get2 (frontend/ubis/controller/calendario_periodos_get2.php)
- **Legacy:** adl > Nuevo Calendario > Definir periodos
- **Pills2:** ACTIVIDADES > Herramientas de calendario > Definir periodos

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.calendario_periodos_get2.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/calendario_periodos_get2.md`

## Calendario Periodos Nuevo

### Para Que Sirve

Precarga el formulario de alta de periodo con fecha siguiente y sfsv del último periodo del año.

### Donde Entrar

- Calendario Periodos Nuevo (frontend/ubis/controller/calendario_periodos_nuevo.php)
- **Legacy:** adl > Nuevo Calendario > Definir periodos
- **Pills2:** ACTIVIDADES > Herramientas de calendario > Definir periodos

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.calendario_periodos_nuevo.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/calendario_periodos_nuevo.md`

## Casas Opciones

### Para Que Sirve

Devuelve opciones de casas filtradas para desplegables compartidos del frontend.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.casas_opciones.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/casas_opciones.md`

## Centros

### Para Que Sirve

Actualiza parcialmente un centro DL según el bloque enviado (labor, num o plazas).

### Donde Entrar

- Centros Que (frontend/ubis/controller/centros_que.php)
- **Legacy:** scdl > direcciones > modificar centros
- **Pills2:** scdl > direcciones > modificar centros

### Tareas Habituales

#### Crear o modificar

1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

### Errores O Avisos Frecuentes

- `Hay un error, no se ha guardado.`

### Referencias Internas

- Flujo: `ubis.centros.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/centros.md`

## Centros Form Labor

### Para Que Sirve

Carga datos del formulario modal de tipo de labor de un centro DL.

### Donde Entrar

- Centros Form Labor (frontend/ubis/controller/centros_form_labor.php)
- **Legacy:** scdl > direcciones > modificar centros
- **Pills2:** scdl > direcciones > modificar centros

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.centros_form_labor.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/centros_form_labor.md`

## Centros Form Num

### Para Que Sirve

Carga datos del formulario modal de números (buzón, pi, cartas) de un centro DL.

### Donde Entrar

- Centros Form Num (frontend/ubis/controller/centros_form_num.php)
- **Legacy:** scdl > direcciones > modificar centros
- **Pills2:** scdl > direcciones > modificar centros

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.centros_form_num.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/centros_form_num.md`

## Centros Form Plazas

### Para Que Sirve

Carga datos del formulario modal de plazas y sede de un centro DL.

### Donde Entrar

- Centros Form Plazas (frontend/ubis/controller/centros_form_plazas.php)
- **Legacy:** scdl > direcciones > modificar centros
- **Pills2:** scdl > direcciones > modificar centros

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.centros_form_plazas.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/centros_form_plazas.md`

## Centros Get Labor

### Para Que Sirve

Lista todos los centros DL activos con su tipo de centro y tipo de labor.

### Donde Entrar

- Centros Get Labor (frontend/ubis/controller/centros_get_labor.php)
- **Legacy:** scdl > direcciones > modificar centros
- **Pills2:** scdl > direcciones > modificar centros

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.centros_get_labor.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/centros_get_labor.md`

## Centros Get Num

### Para Que Sirve

Lista centros DL activos con sus datos numéricos de buzón, pi y cartas.

### Donde Entrar

- Centros Get Num (frontend/ubis/controller/centros_get_num.php)
- **Legacy:** scdl > direcciones > modificar centros
- **Pills2:** scdl > direcciones > modificar centros

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.centros_get_num.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/centros_get_num.md`

## Centros Get Plazas

### Para Que Sirve

Lista centros DL activos con plazas, habitaciones individuales y flag sede.

### Donde Entrar

- Centros Get Plazas (frontend/ubis/controller/centros_get_plazas.php)
- **Legacy:** scdl > direcciones > modificar centros
- **Pills2:** scdl > direcciones > modificar centros

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.centros_get_plazas.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/centros_get_plazas.md`

## Centros Opciones

### Para Que Sirve

Devuelve opciones de centros filtradas para desplegables compartidos del frontend.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.centros_opciones.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/centros_opciones.md`

## Delegacion Que

### Para Que Sirve

Devuelve delegaciones destino disponibles para el traslado de ubis.

### Donde Entrar

- Delegacion Que (frontend/ubis/controller/delegacion_que.php)
- **Legacy:** scdl > direcciones > listados
- **Pills2:** Calendario > centros y casas > listados

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.delegacion_que.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/delegacion_que.md`

## Delegaciones Region Stgr

### Para Que Sirve

Lista delegaciones de una región STGR para desplegables dependientes.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `Se requiere region_stgr`

### Referencias Internas

- Flujo: `ubis.delegaciones_region_stgr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/delegaciones_region_stgr.md`

## Direccion

### Para Que Sirve

Crea o modifica una dirección y su relación con el ubi (principal, propietario).

### Donde Entrar

- Direccion Update (frontend/ubis/controller/direccion_update.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Crear o modificar

1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

### Errores O Avisos Frecuentes

- `no se encuentra el ubi`
- `operación no soportada para este tipo de dirección`
- `no se encuentra la dirección`

### Referencias Internas

- Flujo: `ubis.direccion.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/direccion.md`

## Direcciones Asignar

### Para Que Sirve

Asocia una dirección existente a un ubi sin marcarla como propietaria.

### Donde Entrar

- Direcciones Asignar (frontend/ubis/controller/direcciones_asignar.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.direcciones_asignar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/direcciones_asignar.md`

## Direcciones Editar

### Para Que Sirve

Carga la ficha de edición de direcciones de un ubi, con navegación entre varias direcciones.

### Donde Entrar

- Direcciones Editar (frontend/ubis/controller/direcciones_editar.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.direcciones_editar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/direcciones_editar.md`

## Direcciones Que

### Para Que Sirve

Prepara el formulario de búsqueda de direcciones existentes para asignar a un ubi.

### Donde Entrar

- Direcciones Que (frontend/ubis/controller/direcciones_que.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.direcciones_que.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/direcciones_que.md`

## Direcciones Quitar

### Para Que Sirve

Desvincula una dirección del ubi según el índice en la lista CSV de ids.

### Donde Entrar

- Direcciones Quitar (frontend/ubis/controller/direcciones_quitar.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.direcciones_quitar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/direcciones_quitar.md`

## Direcciones Tabla

### Para Que Sirve

Busca direcciones por cp/ciudad/país y muestra tabla para asignar al ubi.

### Donde Entrar

- Direcciones Tabla (frontend/ubis/controller/direcciones_tabla.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.direcciones_tabla.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/direcciones_tabla.md`

## Home Ubis

### Para Que Sirve

Construye la ficha resumen de un ubi con dirección, telecomunicaciones y objetos pau/dir.

### Donde Entrar

- Home Ubis (frontend/ubis/controller/home_ubis.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.home_ubis.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/home_ubis.md`

## List Ctr

### Para Que Sirve

Lista centros y casas filtrados por delegación/exterior y tipo, con teléfonos y enlaces a ficha.

### Donde Entrar

- List Ctr (frontend/ubis/controller/list_ctr.php)
- **Legacy:** scdl > direcciones > listados
- **Pills2:** Calendario > centros y casas > listados

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.list_ctr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/list_ctr.md`

## Lista Ctrs

### Para Que Sirve

Lista centros tipo s de la delegación con el número de sacerdotes asignados en cada uno.

### Donde Entrar

- Lista Ctrs (frontend/ubis/controller/lista_ctrs.php)
- **Legacy:** vsg > buscar > lista ctr i nº s
- **Pills2:** vsg > buscar > lista ctr i nº s

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.lista_ctrs.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/lista_ctrs.md`

## Teleco

### Para Que Sirve

Elimina una o más telecomunicaciones del ubi por claves primarias codificadas.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

#### Guardar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.teleco.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/teleco.md`

## Teleco Desc

### Para Que Sirve

Devuelve descripciones de telecomunicación dependientes del tipo seleccionado.

### Donde Entrar

- Teleco Desc Lista Ajax (frontend/ubis/controller/teleco_desc_lista_ajax.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Consultar el listado

1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.teleco_desc.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/teleco_desc.md`

## Teleco Editar

### Para Que Sirve

Carga el formulario de alta/edición de una telecomunicación de un ubi.

### Donde Entrar

- Teleco Editar (frontend/ubis/controller/teleco_editar.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.teleco_editar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/teleco_editar.md`

## Teleco Tabla

### Para Que Sirve

Lista las telecomunicaciones de un centro o casa con botones según permisos.

### Donde Entrar

- Teleco Tabla (frontend/ubis/controller/teleco_tabla.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.teleco_tabla.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/teleco_tabla.md`

## Trasladar Ubis

### Para Que Sirve

Traslada centros y casas seleccionados a otra delegación destino.

### Donde Entrar

- Trasladar Ubis (frontend/ubis/controller/trasladar_ubis.php)
- **Legacy:** scdl > direcciones > listados
- **Pills2:** Calendario > centros y casas > listados

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `No se han seleccionado ubis.`

### Referencias Internas

- Flujo: `ubis.trasladar_ubis.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/trasladar_ubis.md`

## Ubis

### Para Que Sirve

Elimina un ubi (centro o casa) del repositorio correspondiente a obj_pau.

### Donde Entrar

- Ubis Eliminar (frontend/ubis/controller/ubis_eliminar.php)
- Ubis Lista (frontend/ubis/controller/ubis_lista.php)
- Ubis Update (frontend/ubis/controller/ubis_update.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

#### Guardar

1. Revisar manualmente los pasos de esta accion.

#### Consultar el listado

1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

### Errores O Avisos Frecuentes

- `no se encuentra el ubi a borrar`
- `hay un error, no se ha eliminado`

### Referencias Internas

- Flujo: `ubis.ubis.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/ubis.md`

## Ubis Buscar

### Para Que Sirve

Devuelve opciones de desplegables para el formulario de búsqueda de ubis.

### Donde Entrar

- Ubis Buscar (frontend/ubis/controller/ubis_buscar.php)
- **Legacy:** scdl > direcciones > buscar
- **Pills2:** scdl > direcciones > buscar

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.ubis_buscar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/ubis_buscar.md`

## Ubis Editar

### Para Que Sirve

Devuelve desplegables dependientes para el formulario de edición de ubi.

### Donde Entrar

- Ubis Editar (frontend/ubis/controller/ubis_editar.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.ubis_editar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/ubis_editar.md`

## Ubis Editar Load

### Para Que Sirve

Carga la ficha completa de un ubi para edición o alta, normalizando obj_pau de delegación.

### Donde Entrar

- Ubis Editar (frontend/ubis/controller/ubis_editar.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `falta definir obj_pau`
- `No se encuentra ubi id %s`
- `tipo de entidad inesperado para centro dl`
- `tipo de entidad inesperado para centro ex`
- `tipo de entidad inesperado para casa`

### Referencias Internas

- Flujo: `ubis.ubis_editar_load.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/ubis_editar_load.md`

## Ubis Editar Normalize Dl

### Para Que Sirve

Ajusta obj_pau a CentroDl/CasaDl cuando la ficha pertenece a la delegación del usuario.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.ubis_editar_normalize_dl.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/ubis_editar_normalize_dl.md`

## Ubis Tabla

### Para Que Sirve

Busca ubis por nombre y/o dirección con filtros tipo/loc y construye tabla navegable.

### Donde Entrar

- Ubis Tabla (frontend/ubis/controller/ubis_tabla.php)
- **Legacy:** scdl > direcciones > buscar
- **Pills2:** scdl > direcciones > buscar

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- `debe poner algún criterio de búsqueda`

### Referencias Internas

- Flujo: `ubis.ubis_tabla.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/ubis_tabla.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/ubis/flujos/`.
