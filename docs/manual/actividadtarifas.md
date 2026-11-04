---
tipo: "manual_usuario"
modulo: "actividadtarifas"
flujos: 3
estado_revision: "revisado_parcial"
---

# Manual De Usuario - actividadtarifas

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## Asignar una tarifa a un tipo de actividad

### Para Que Sirve

Decidir qué tipo de tarifa corresponde a cada tipo de actividad.

### Donde Entrar

- Tarifa Tipo Actividad (frontend/actividadtarifas/controller/tarifa_tipo_actividad.php)
- **Legacy:** adl > Tarifas > tarifa <-> tipo de actividad; dre/Calendario/exterior > Tarifas > tarifa <-> tipo actividad.
- **Pills2:** ACTIVIDADES > Herramientas de calendario > Tarifas > Tarifa-tipo actividad.

### Tareas Habituales

#### Asignar una tarifa a un tipo de actividad

1. Entrar en **tarifa <-> tipos actividad**.
2. Elegir el tipo de actividad (obligatorio: «debe indicar el tipo de actividad») y la tarifa (obligatoria: «debe indicar la id_tarifa»).
3. Guardar. Para cambiar la relación existente, abrirla, elegir otra tarifa y guardar; para quitarla, eliminarla.

### Errores O Avisos Frecuentes

- `debe indicar el tipo de actividad`
- `debe indicar la tarifa`
- `hay un error, no se ha borrado`
- `hay un error, no se ha guardado`
- `no se encuentra la relación`
- `no sé cuál he de borrar`

### Permisos

- Sin control propio; acción desde formulario de edición con permiso `adl` en listado.
- Sin control propio; visibilidad según listado (`puede_anadir`, enlace modificar con `adl`).
- Enlace modificar: `mi_sfsv === isfsv` del tipo y `have_perm_oficina('adl')`.
- `puede_anadir`: `have_perm_oficina('adl'|'pr'|'calendario')`.
- Sin control propio; enlace modificar en listado con `have_perm_oficina('adl')` y sección del tipo

### Referencias Internas

- Flujo: `actividadtarifas.relacion_tarifa.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadtarifas/flujos/relacion_tarifa.md`

## Consultar las tarifas de una casa

### Para Que Sirve

Consultar y mantener las tarifas de una casa para un año: importes por tipo de tarifa y serie.

### Donde Entrar

- Tarifa Ubi (frontend/actividadtarifas/controller/tarifa_ubi.php)
- **Legacy:** adl > Tarifas > tarifas por casa y año; Calendario/dre/exterior > Tarifas > tarifas por casa y año.
- **Pills2:** ACTIVIDADES > Herramientas de calendario > Tarifas > Tarifas por casa y año.

### Tareas Habituales

#### Consultar las tarifas de una casa

1. Entrar en **tarifas por casa y año**.
2. Elegir la casa y el año y pulsar **buscar**.
3. Se listan sus tarifas con el importe en €.

#### Crear o modificar una tarifa de una casa

1. En el listado, pulsar nuevo (o la tarifa a cambiar).
2. Elegir la **tarifa**, la **serie** y escribir el importe (**nuevo importe**).
3. Pulsar **guardar**; **eliminar** la quita (con confirmación).

#### Copiar las tarifas del año anterior

1. Si aparece el botón de copiar, pulsarlo y confirmar.
2. Aviso: hoy la operación responde «función pendiente de reimplementar» (ver D-TAR-1).

### Errores O Avisos Frecuentes

- `Operación no autorizada (cápsula HashB inválida en update/eliminar/copiar)`
- `función de copiar tarifas pendiente de reimplementar`
- `hay un error, no se ha borrado`
- `hay un error, no se ha guardado`
- `no se encuentra la tarifa`
- `no sé cuál he de borrar`
- `no sé qué casa/año tengo que copiar`

### Permisos

- Autorización vía cápsula `HashB` (`ctx_copiar`), solo emitida si `puede_anadir` en el listado
- Autorización vía cápsula `HashB` (`ctx_eliminar`). La acción de eliminar solo se ofrece en el
- Sin control propio; el listado solo muestra enlace modificar con `have_perm_oficina('adl')` y
- Enlace modificar: `mi_sfsv === seccion` y `have_perm_oficina('adl')`.
- `puede_anadir`: `have_perm_oficina('adl'|'pr'|'calendario')` con `id_ubi !== 0`.
- Autorización vía cápsula `HashB` (`ctx_update`): solo quien recibió el token al abrir el
- Sin control propio en el caso de uso; invocado desde el estudio económico de casa

### Referencias Internas

- Flujo: `actividadtarifas.tarifa_ubi.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadtarifas/flujos/tarifa_ubi.md`

## Mantener los tipos de tarifa

### Para Que Sirve

Mantener el catálogo de tipos de tarifa: cada uno con su letra, su modo y observaciones.

### Donde Entrar

- Tarifa (frontend/actividadtarifas/controller/tarifa.php)
- **Legacy:** adl > Tarifas > definir tarifa; dre/Calendario/exterior > Tarifas > definir tarifa.
- **Pills2:** ACTIVIDADES > Herramientas de calendario > Tarifas > Definir tarifa.

### Tareas Habituales

#### Mantener los tipos de tarifa

1. Entrar en **definir tarifa**: aparece el listado de tarifas.
2. Para crear una, pulsar nuevo; escribir la **letra**, el **modo** y las **observaciones**, y pulsar **guardar** (o **cancel**).
3. Para modificar, pulsar la tarifa, cambiar los datos y **guardar**.
4. Para quitarla, abrirla y pulsar **eliminar**, confirmando.

### Errores O Avisos Frecuentes

- `hay un error, no se ha borrado`
- `hay un error, no se ha guardado`
- `no se encuentra la tarifa`
- `no sé cuál he de borrar`

### Permisos

- Sin control propio; botón eliminar solo en formulario de edición con permiso `adl` en listado.
- Sin control propio; formulario accesible según permisos del listado (`puede_anadir` / enlace modificar).
- `puede_editar`: `have_perm_oficina('adl')`.
- Enlace modificar por fila: además `mi_sfsv === sfsv` de la tarifa.
- `puede_anadir`: `have_perm_oficina('adl'|'pr'|'calendario')`.
- Sin control propio; el listado muestra modificar solo con `have_perm_oficina('adl')` y sección

### Referencias Internas

- Flujo: `actividadtarifas.tipo_tarifa.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/actividadtarifas/flujos/tipo_tarifa.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/actividadtarifas/flujos/`.
