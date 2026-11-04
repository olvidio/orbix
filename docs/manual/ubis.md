---
tipo: "manual_usuario"
modulo: "ubis"
flujos: 36
estado_revision: "revisado"
---

# Manual De Usuario - ubis

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## Consultar los periodos de una casa

### Para Que Sirve

Definir los periodos del calendario de una casa en un año y borrar los que sobran.

### Donde Entrar

- Calendario Periodos (frontend/ubis/controller/calendario_periodos.php)
- **Legacy:** adl > Nuevo Calendario > Definir periodos
- **Pills2:** ACTIVIDADES > Herramientas de calendario > Definir periodos

### Tareas Habituales

#### Consultar los periodos de una casa

1. Entrar en **Definir periodos**.
2. Elegir la casa y el año y pulsar **buscar**.
3. Se listan los periodos ordenados por fecha de inicio y se avisa si alguno se solapa con otro.

#### Borrar un periodo

1. En la lista, pulsar eliminar en el periodo.
2. Confirmar «¿Está seguro de borrar este periodo?».

### Errores O Avisos Frecuentes

- `no sé cuál he de borar`
- `no se encuentra el periodo a borrar`
- `hay un error, no se ha eliminado`

### Referencias Internas

- Flujo: `ubis.calendario_periodos.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/calendario_periodos.md`

## Modificar un periodo

### Para Que Sirve

Abrir el formulario de un periodo ya existente para modificarlo.

### Donde Entrar

- Calendario Periodos Form Periodo (frontend/ubis/controller/calendario_periodos_form_periodo.php)
- **Legacy:** adl > Nuevo Calendario > Definir periodos
- **Pills2:** ACTIVIDADES > Herramientas de calendario > Definir periodos

### Tareas Habituales

#### Modificar un periodo

1. En la lista de periodos de la casa, pulsar el periodo.
2. Cambiar fechas o sección y guardar.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.calendario_periodos_form_periodo.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/calendario_periodos_form_periodo.md`

## Ver todos los periodos de una casa

### Para Que Sirve

Listar todos los periodos de calendario de una casa ordenados por fecha de inicio.

### Donde Entrar

- Calendario Periodos Get (frontend/ubis/controller/calendario_periodos_get.php)
- **Legacy:** adl > Nuevo Calendario > Definir periodos
- **Pills2:** ACTIVIDADES > Herramientas de calendario > Definir periodos

### Tareas Habituales

#### Ver todos los periodos de una casa

1. Entrar en **Definir periodos**, elegir la casa y pulsar **buscar**.
2. Aparecen todos sus periodos por orden de fecha.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.calendario_periodos_get.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/calendario_periodos_get.md`

## Ver los periodos de un año

### Para Que Sirve

Listar los periodos de una casa en un año concreto y detectar solapes.

### Donde Entrar

- Calendario Periodos Get2 (frontend/ubis/controller/calendario_periodos_get2.php)
- **Legacy:** adl > Nuevo Calendario > Definir periodos
- **Pills2:** ACTIVIDADES > Herramientas de calendario > Definir periodos

### Tareas Habituales

#### Ver los periodos de un año

1. Entrar en **Definir periodos**, elegir la casa y el año y pulsar **buscar**.
2. Aparecen los periodos de ese año; los que se pisan quedan señalados.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.calendario_periodos_get2.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/calendario_periodos_get2.md`

## Crear un periodo nuevo

### Para Que Sirve

Abrir el formulario de alta de un periodo ya relleno con la fecha siguiente al último.

### Donde Entrar

- Calendario Periodos Nuevo (frontend/ubis/controller/calendario_periodos_nuevo.php)
- **Legacy:** adl > Nuevo Calendario > Definir periodos
- **Pills2:** ACTIVIDADES > Herramientas de calendario > Definir periodos

### Tareas Habituales

#### Crear un periodo nuevo

1. En **Definir periodos**, elegir la casa y el año y pulsar el botón de nuevo periodo.
2. El formulario propone como inicio el día siguiente al último periodo y su misma sección (sf/sv).
3. Ajustar las fechas y guardar.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.calendario_periodos_nuevo.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/calendario_periodos_nuevo.md`

## Elegir una casa en un desplegable

### Para Que Sirve

Proporcionar la lista de casas que usan los desplegables de otras pantallas.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Elegir una casa en un desplegable

1. En cualquier formulario con selector de casa, abrir el desplegable.
2. La lista se carga filtrada por el tipo de casa y la delegación que corresponda; elegir una.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.casas_opciones.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/casas_opciones.md`

## Guardar los datos de un centro

### Para Que Sirve

Guardar los cambios de un centro de la dl: labor, números o plazas.

### Donde Entrar

- Centros Que (frontend/ubis/controller/centros_que.php)
- **Legacy:** scdl > direcciones > modificar centros
- **Pills2:** scdl > direcciones > modificar centros

### Tareas Habituales

#### Guardar los datos de un centro

1. Elegir **labor**, **pi, cartas, nº buzón** o **sede, plazas** y pulsar **buscar**.
2. Abrir el centro, cambiar el bloque y pulsar **guardar cambios**.

### Errores O Avisos Frecuentes

- `Hay un error, no se ha guardado.`

### Referencias Internas

- Flujo: `ubis.centros.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/centros.md`

## Cambiar el tipo de labor

### Para Que Sirve

Cambiar el tipo de labor de un centro.

### Donde Entrar

- Centros Form Labor (frontend/ubis/controller/centros_form_labor.php)
- **Legacy:** scdl > direcciones > modificar centros
- **Pills2:** scdl > direcciones > modificar centros

### Tareas Habituales

#### Cambiar el tipo de labor

1. En **modificar centros > labor**, pulsar el centro.
2. Elegir el **tipo de labor** y pulsar **guardar cambios**.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.centros_form_labor.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/centros_form_labor.md`

## Cambiar buzón, pi y cartas

### Para Que Sirve

Cambiar el nº de buzón, de ejemplares de pi y de cartas de un centro.

### Donde Entrar

- Centros Form Num (frontend/ubis/controller/centros_form_num.php)
- **Legacy:** scdl > direcciones > modificar centros
- **Pills2:** scdl > direcciones > modificar centros

### Tareas Habituales

#### Cambiar buzón, pi y cartas

1. En **modificar centros > pi, cartas, nº buzón**, pulsar el centro.
2. Rellenar **nº de buzón**, **nº ejemplares pi** y **nº cartas mensuales**.
3. Pulsar **guardar cambios**.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.centros_form_num.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/centros_form_num.md`

## Cambiar plazas y sede

### Para Que Sirve

Cambiar las plazas, las habitaciones individuales y la marca de sede de un centro.

### Donde Entrar

- Centros Form Plazas (frontend/ubis/controller/centros_form_plazas.php)
- **Legacy:** scdl > direcciones > modificar centros
- **Pills2:** scdl > direcciones > modificar centros

### Tareas Habituales

#### Cambiar plazas y sede

1. En **modificar centros > sede, plazas**, pulsar el centro.
2. Rellenar **plazas**, **habitaciones individuales** y marcar si es sede.
3. Pulsar **guardar cambios**.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.centros_form_plazas.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/centros_form_plazas.md`

## Consultar o cambiar el tipo de labor de los centros

### Para Que Sirve

Listar los centros de la dl con su tipo de centro y tipo de labor, para editarlos.

### Donde Entrar

- Centros Get Labor (frontend/ubis/controller/centros_get_labor.php)
- **Legacy:** scdl > direcciones > modificar centros
- **Pills2:** scdl > direcciones > modificar centros

### Tareas Habituales

#### Consultar o cambiar el tipo de labor de los centros

1. Entrar en **modificar centros** y elegir **labor** en el desplegable; pulsar **buscar**.
2. Se muestra la lista de centros con su tipo de centro y de labor.
3. Pulsar un centro para abrir el formulario de edición.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.centros_get_labor.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/centros_get_labor.md`

## Consultar o cambiar buzón, pi y cartas

### Para Que Sirve

Listar los centros de la dl con su nº de buzón, ejemplares de pi y cartas, para editarlos.

### Donde Entrar

- Centros Get Num (frontend/ubis/controller/centros_get_num.php)
- **Legacy:** scdl > direcciones > modificar centros
- **Pills2:** scdl > direcciones > modificar centros

### Tareas Habituales

#### Consultar o cambiar buzón, pi y cartas

1. Entrar en **modificar centros** y elegir **pi, cartas, nº buzón**; pulsar **buscar**.
2. Se muestra la lista con el nº de buzón, nº de ejemplares de pi y nº de cartas.
3. Pulsar un centro para editar sus números.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.centros_get_num.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/centros_get_num.md`

## Consultar o cambiar plazas y sede

### Para Que Sirve

Listar los centros de la dl con plazas, habitaciones individuales y si son sede, para editarlos.

### Donde Entrar

- Centros Get Plazas (frontend/ubis/controller/centros_get_plazas.php)
- **Legacy:** scdl > direcciones > modificar centros
- **Pills2:** scdl > direcciones > modificar centros

### Tareas Habituales

#### Consultar o cambiar plazas y sede

1. Entrar en **modificar centros** y elegir **sede, plazas**; pulsar **buscar**.
2. Se muestra la lista con plazas, habitaciones individuales y la marca de sede.
3. Pulsar un centro para editarlo.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.centros_get_plazas.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/centros_get_plazas.md`

## Elegir un centro en un desplegable

### Para Que Sirve

Proporcionar la lista de centros que usan los desplegables de otras pantallas.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Elegir un centro en un desplegable

1. En cualquier formulario con selector de centro, abrir el desplegable.
2. La lista se carga según el filtro de la pantalla; elegir uno.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.centros_opciones.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/centros_opciones.md`

## Elegir la delegación de destino

### Para Que Sirve

Ofrecer las delegaciones a las que se pueden trasladar los centros o casas marcados.

### Donde Entrar

- Delegacion Que (frontend/ubis/controller/delegacion_que.php)
- **Legacy:** scdl > direcciones > listados
- **Pills2:** Calendario > centros y casas > listados

### Tareas Habituales

#### Elegir la delegación de destino

1. En el listado, marcar los centros o casas a trasladar.
2. Pulsar trasladar.
3. En **Trasladar a**, elegir la delegación y confirmar.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.delegacion_que.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/delegacion_que.md`

## Elegir región y delegación

### Para Que Sirve

Rellenar el desplegable de delegaciones cuando se elige una región del Studium.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Elegir región y delegación

1. Elegir la región.
2. El desplegable de delegaciones se actualiza con las delegaciones de esa región; elegir una.

### Errores O Avisos Frecuentes

- `Se requiere region_stgr`

### Referencias Internas

- Flujo: `ubis.delegaciones_region_stgr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/delegaciones_region_stgr.md`

## Guardar una dirección

### Para Que Sirve

Guardar los datos de la dirección de un centro o casa.

### Donde Entrar

- Direccion Update (frontend/ubis/controller/direccion_update.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Guardar una dirección

1. En la ficha pulsar **dirección** en «Editar:».
2. Rellenar **Nombre sede**, **Dirección**, **Dirección postal**, **Código postal**, **Población**, **Provincia**, **País**, **Comentarios dirección**, **Fecha introducción de datos**, **Latitud** y **Longitud**; marcar **Propietario** o **Principal** si procede (hay un enlace **ver ejemplo dirección**).
3. Pulsar **Guardar Cambios**.
4. Con **Añadir Dirección** se asigna otra dirección al centro y con **Quitar Dirección** se le quita la actual.

### Errores O Avisos Frecuentes

- `no se encuentra el ubi`
- `operación no soportada para este tipo de dirección`
- `no se encuentra la dirección`

### Referencias Internas

- Flujo: `ubis.direccion.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/direccion.md`

## Asignar una dirección existente

### Para Que Sirve

Asociar una dirección ya existente a un centro o casa.

### Donde Entrar

- Direcciones Asignar (frontend/ubis/controller/direcciones_asignar.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Asignar una dirección existente

1. Buscar la dirección y marcarla en la tabla.
2. Pulsar asignar.
3. La dirección aparece en la ficha del centro o casa; no se marca como propietaria.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.direcciones_asignar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/direcciones_asignar.md`

## Modificar la dirección

### Para Que Sirve

Modificar la dirección de un centro o casa y gestionar su plano.

### Donde Entrar

- Direcciones Editar (frontend/ubis/controller/direcciones_editar.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Modificar la dirección

1. En la ficha pulsar **dirección** en «Editar:».
2. Cambiar los campos que hagan falta (dirección, código postal, población, provincia, país, latitud y longitud, comentarios).
3. Para el plano: **ver plano**, **adjuntar plano** o **eliminar plano**.
4. Pulsar **Guardar Cambios**.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.direcciones_editar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/direcciones_editar.md`

## Buscar una dirección existente

### Para Que Sirve

Buscar una dirección que ya existe para asignarla a un centro o casa.

### Donde Entrar

- Direcciones Que (frontend/ubis/controller/direcciones_que.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Buscar una dirección existente

1. En la ficha, pulsar **añadir dirección**.
2. Escribir código postal, población y/o país de la dirección.
3. Pulsar buscar para ver la tabla de direcciones.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.direcciones_que.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/direcciones_que.md`

## Quitar una dirección del centro o casa

### Para Que Sirve

Quitar la dirección de un centro o casa sin borrarla.

### Donde Entrar

- Direcciones Quitar (frontend/ubis/controller/direcciones_quitar.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Quitar una dirección del centro o casa

1. En la ficha pulsar **dirección** en «Editar:».
2. Pulsar **Quitar Dirección**.
3. La dirección deja de aparecer en el centro o casa, pero sigue existiendo para otros.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.direcciones_quitar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/direcciones_quitar.md`

## Elegir una dirección de la tabla

### Para Que Sirve

Mostrar las direcciones encontradas para asignar una al centro o casa, o crear otra.

### Donde Entrar

- Direcciones Tabla (frontend/ubis/controller/direcciones_tabla.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Elegir una dirección de la tabla

1. Revisar la «tabla de direcciones».
2. Marcar la dirección que corresponde y asignarla.
3. Si no está, pulsar **crear otra nueva**.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.direcciones_tabla.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/direcciones_tabla.md`

## Ver el resumen de un centro o casa

### Para Que Sirve

Mostrar la ficha resumen de un centro o casa con su dirección y telecomunicaciones.

### Donde Entrar

- Home Ubis (frontend/ubis/controller/home_ubis.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Ver el resumen de un centro o casa

1. Abrir el centro o casa.
2. Aparecen los apartados **dirección** y **telecos**, cada uno con su botón **editar**.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.home_ubis.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/home_ubis.md`

## Listar centros o casas

### Para Que Sirve

Sacar un listado de centros o casas de una delegación o del exterior, con teléfonos y enlaces a la ficha.

### Donde Entrar

- List Ctr (frontend/ubis/controller/list_ctr.php)
- **Legacy:** scdl > direcciones > listados
- **Pills2:** Calendario > centros y casas > listados

### Tareas Habituales

#### Listar centros o casas

1. Entrar en **listados**.
2. Elegir **Localización** (la propia dl, otras o «otras») y el **Tipo de ubi**: solo centros de n, todos los ctr de la dl, solo centros de agd, de s, de sss+, obras corporativas, casas de retiros y de cv, colegios o resto de casas cdc.
3. Pulsar **listar**.
4. Pulsar un nombre para abrir su ficha; la tabla admite **Buscar en todas las columnas**.

#### Trasladar centros o casas a otra delegación

1. En el listado, marcar los centros o casas.
2. Pulsar el botón de trasladar y elegir la delegación destino.
3. Confirmar con **Trasladar** (o **Cancelar**).

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.list_ctr.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/list_ctr.md`

## Consultar los sacerdotes por centro

### Para Que Sirve

Ver los centros de tipo s de la delegación con el número de sacerdotes asignados a cada uno.

### Donde Entrar

- Lista Ctrs (frontend/ubis/controller/lista_ctrs.php)
- **Legacy:** vsg > buscar > lista ctr i nº s
- **Pills2:** vsg > buscar > lista ctr i nº s

### Tareas Habituales

#### Consultar los sacerdotes por centro

1. Entrar en **lista ctr i nº s**.
2. Se muestra la tabla con cada centro y el número de sacerdotes que tiene.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.lista_ctrs.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/lista_ctrs.md`

## Eliminar una telecomunicación

### Para Que Sirve

Eliminar una telecomunicación de un centro o casa.

### Donde Entrar

- Teleco Editar (frontend/ubis/controller/teleco_editar.php)
- Teleco Tabla (frontend/ubis/controller/teleco_tabla.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Eliminar una telecomunicación

1. En **telecos**, marcar el registro y pulsar **eliminar**.
2. Confirmar el aviso «¿Está seguro que desea eliminar este registro?».

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.teleco.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/teleco.md`

## Elegir la descripción de una teleco

### Para Que Sirve

Ofrecer las descripciones de telecomunicación según el tipo elegido.

### Donde Entrar

- Teleco Desc Lista Ajax (frontend/ubis/controller/teleco_desc_lista_ajax.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Elegir la descripción de una teleco

1. En el formulario de teleco, elegir el tipo.
2. El desplegable de descripción se rellena con las opciones de ese tipo.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.teleco_desc.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/teleco_desc.md`

## Añadir o editar una telecomunicación

### Para Que Sirve

Añadir una telecomunicación o modificar una existente.

### Donde Entrar

- Teleco Editar (frontend/ubis/controller/teleco_editar.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Añadir o editar una telecomunicación

1. En **telecos** pulsar **nuevo**, o marcar un registro y pulsar **modificar**.
2. Elegir el **Tipo teleco** (correo electrónico, fax, página web, teléfono fijo o teléfono móvil); entonces el desplegable **Descripción** se rellena con las opciones de ese tipo (por ejemplo otros, de la casa, dirección, administración, sacd). Escribir el **Número** y, si hace falta, las **Observaciones**.
3. Pulsar **Guardar Cambios**. Vuelve a la ficha y el número aparece junto a los demás teléfonos.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.teleco_editar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/teleco_editar.md`

## Consultar las telecomunicaciones

### Para Que Sirve

Ver las telecomunicaciones (teléfonos, fax, e-mails, web) de un centro o casa.

### Donde Entrar

- Teleco Tabla (frontend/ubis/controller/teleco_tabla.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Consultar las telecomunicaciones

1. En la ficha pulsar **telecos** en «Editar:».
2. Se muestra «Telecomunicaciones de un centro o casa» con Id_ubi, nombre del teleco (hoy sale su número de tipo), descripción, número o siglas y observaciones.
3. Los botones son **modificar** y **eliminar** (sobre las filas marcadas) y **nuevo**.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.teleco_tabla.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/teleco_tabla.md`

## Trasladar centros o casas

### Para Que Sirve

Pasar los centros o casas seleccionados a otra delegación.

### Donde Entrar

- Trasladar Ubis (frontend/ubis/controller/trasladar_ubis.php)
- **Legacy:** scdl > direcciones > listados
- **Pills2:** Calendario > centros y casas > listados

### Tareas Habituales

#### Trasladar centros o casas

1. En **listados**, marcar uno o varios centros o casas.
2. Elegir la delegación en **Trasladar a** y pulsar **Trasladar**.
3. Comprobar el listado: los trasladados ya no pertenecen a la delegación de origen.

### Errores O Avisos Frecuentes

- `No se han seleccionado ubis.`

### Referencias Internas

- Flujo: `ubis.trasladar_ubis.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/trasladar_ubis.md`

## Eliminar un centro o una casa

### Para Que Sirve

Eliminar un centro o una casa.

### Donde Entrar

- Ubis Eliminar (frontend/ubis/controller/ubis_eliminar.php)
- Ubis Lista (frontend/ubis/controller/ubis_lista.php)
- Ubis Update (frontend/ubis/controller/ubis_update.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Eliminar un centro o una casa

1. Abrir la ficha, o marcar el centro o casa en la tabla de resultados.
2. Pulsar **eliminar** y confirmar «¿Está seguro que desea borrar este centro?» (o «esta casa»).

### Errores O Avisos Frecuentes

- `no se encuentra el ubi a borrar`
- `hay un error, no se ha eliminado`

### Referencias Internas

- Flujo: `ubis.ubis.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/ubis.md`

## Buscar un centro o una casa

### Para Que Sirve

Buscar un centro o una casa por nombre, población, región o país, y llegar a su ficha.

### Donde Entrar

- Ubis Buscar (frontend/ubis/controller/ubis_buscar.php)
- **Legacy:** scdl > direcciones > buscar
- **Pills2:** scdl > direcciones > buscar

### Tareas Habituales

#### Buscar un centro o una casa

1. Entrar en **buscar**. Aparece el formulario **Buscar centro o casa**.
2. Escribir parte del **Nombre de la casa o centro**, la **Población**, o elegir **Región** o **País**. Se puede marcar «buscar ubis fuera de uso» para incluir los dados de baja.
3. Pulsar **buscar**: aparece la tabla de resultados.
4. Pulsar el nombre para abrir la ficha.
5. Si no hay resultados sale «no existe este centro o casa».

#### Buscar con más opciones

1. En el formulario, pulsar **buscar otras opciones**.
2. Elegir **Tipo** (Centro, Casa o Todos) y **Localización** (de la propia r/dl, de otra r/dl o todos), además del nombre y la población.
3. Pulsar **buscar**.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.ubis_buscar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/ubis_buscar.md`

## Rellenar la ficha de un centro o casa

### Para Que Sirve

Cambiar los datos básicos de un centro o casa: delegación, nombre, tipo, labor, observaciones y números.

### Donde Entrar

- Ubis Editar (frontend/ubis/controller/ubis_editar.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Rellenar la ficha de un centro o casa

1. Abrir la ficha del centro (aparece su nombre, la dl, la dirección, los teléfonos, fax y e-mails y la «Relación de dossiers»).
2. Pulsar **centro** en «Editar:».
3. Rellenar **Región o delegación**, **En uso**, **Región**, **Nombre del centro**, **¿puede ser cdc?**, **Tipo de centro** (por ejemplo «colegio oc»), **Tipo de labor** (sr, n, agd, sg, club, bachilleres, univ, jóvenes, mayores, sss+), **Observaciones** y **Centro padre**.
4. Más abajo están **Nº ejemplares pi**, **Nº cartas**, **Nº cartas mensuales**, **Nº de buzón**, **Habitaciones individuales** y **Plazas**.
5. Pulsar **Guardar Cambios**. El botón **Eliminar** borra el centro (pide confirmación).
6. Al cambiar un desplegable, el siguiente se rellena con las opciones válidas.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.ubis_editar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/ubis_editar.md`

## Abrir la ficha de un centro o casa

### Para Que Sirve

Cargar la ficha completa de un centro o casa para consultarla, modificarla o darla de alta.

### Donde Entrar

- Ubis Editar (frontend/ubis/controller/ubis_editar.php)
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Abrir la ficha de un centro o casa

1. Buscar el centro o casa y pulsar su nombre.
2. La ficha se abre con sus datos: nombre, tipo, región o delegación, plazas, observaciones y, aparte, dirección y telecomunicaciones.
3. Para modificar, cambiar los campos y pulsar **guardar cambios**.

### Errores O Avisos Frecuentes

- `falta definir obj_pau`
- `No se encuentra ubi id %s`
- `tipo de entidad inesperado para centro dl`
- `tipo de entidad inesperado para centro ex`
- `tipo de entidad inesperado para casa`

### Referencias Internas

- Flujo: `ubis.ubis_editar_load.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/ubis_editar_load.md`

## Ficha de un centro o casa de la propia delegación

### Para Que Sirve

Ajuste interno: si la ficha pertenece a la propia delegación, se trata como centro/casa de la dl y se habilitan todos sus campos.

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

### Tareas Habituales

#### Ficha de un centro o casa de la propia delegación

1. Abrir la ficha de un centro o casa de la propia delegación.
2. Se muestran todos los campos editables; en los de otras delegaciones, solo consulta.

### Errores O Avisos Frecuentes

- `_(ninguno documentado)_`

### Referencias Internas

- Flujo: `ubis.ubis_editar_normalize_dl.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/ubis_editar_normalize_dl.md`

## Trabajar con los resultados de la búsqueda

### Para Que Sirve

Mostrar la tabla de centros y casas encontrados y permitir abrir, crear, eliminar o trasladar.

### Donde Entrar

- Ubis Tabla (frontend/ubis/controller/ubis_tabla.php)
- **Legacy:** scdl > direcciones > buscar
- **Pills2:** scdl > direcciones > buscar

### Tareas Habituales

#### Trabajar con los resultados de la búsqueda

1. Tras buscar, aparece «Tabla de toda las casas y centros».
2. Pulsar un nombre para abrir su ficha.
3. Para crear uno nuevo, usar el botón **Centro** o **Casa** (solo aparecen si se buscó con un tipo y una localización distintos de «todos»; con «todos» la pantalla solo ofrece **buscar otras opciones** y avisa «OJO!: para crear un centro/casa debe especificar el tipo de centro/casa»).
4. Para quitar uno, marcarlo y pulsar eliminar; confirmar «¿Está seguro que desea borrar este ubi?».

### Errores O Avisos Frecuentes

- `debe poner algún criterio de búsqueda`

### Referencias Internas

- Flujo: `ubis.ubis_tabla.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/ubis/flujos/ubis_tabla.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/ubis/flujos/`.
