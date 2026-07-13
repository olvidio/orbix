---
tipo: "manual_usuario"
modulo: "actividadtarifas"
flujos: 3
estado_revision: "generado"
---

# Manual De Usuario - actividadtarifas

Este manual es un borrador generado desde `docs/catalogo`. Debe revisarse para ajustar nombres de menu, permisos, validaciones y lenguaje final de usuario.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas de menu y nombres visibles pueden necesitar revision manual.

## Relacion Tarifa

### Para Que Sirve

- Definir qué tipo de tarifa del catálogo corresponde a cada tipo de actividad.
- Plantilla de redacción revisada en `docs/manual/actividadtarifas.md` (sección Relacion Tarifa).

### Donde Entrar

- Tarifa Tipo Actividad (frontend/actividadtarifas/controller/tarifa_tipo_actividad.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Crear o modificar

1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

#### Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

#### Consultar el listado

1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

#### Abrir el formulario

1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

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

## Tarifa Ubi

### Para Que Sirve

- Consultar y mantener las tarifas económicas de una casa para un año: listado, alta, edición, eliminación, copia desde el año anterior y actualización en lote desde el estudio económico.
- Plantilla de redacción revisada en `docs/manual/actividadtarifas.md` (sección Tarifa Ubi).

### Donde Entrar

- Tarifa Ubi (frontend/actividadtarifas/controller/tarifa_ubi.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Actualizar importes en lote

1. Abrir la pantalla o proceso que permite actualizacion en lote.
2. Revisar el conjunto de registros afectados.
3. Ejecutar la actualizacion.
4. Comprobar importes o valores recalculados.

#### Copiar

1. Cargar listado de casa/año destino.
2. Pulsar copiar tarifas del año anterior (solo si hay `token_copiar`).
3. Confirmar; el cliente reenvía la cápsula `ctx_copiar` (HashB) sin inspeccionarla.
4. **Nota:** la operación devuelve hoy «función pendiente de reimplementar».

#### Crear o modificar

1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

#### Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

#### Consultar el listado

1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

#### Abrir el formulario

1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

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

## Tipo Tarifa

### Para Que Sirve

- Mantener el catálogo maestro de tipos de tarifa (letra, modo, observaciones).
- Plantilla de redacción revisada en `docs/manual/actividadtarifas.md` (sección Tipo Tarifa).

### Donde Entrar

- Tarifa (frontend/actividadtarifas/controller/tarifa.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Crear o modificar

1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

#### Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

#### Consultar el listado

1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

#### Abrir el formulario

1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

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

## Revision Pendiente

- Sustituir nombres tecnicos por nombres visibles en la aplicacion.
- Completar rutas de menu.
- Confirmar permisos necesarios.
- Anadir capturas o ejemplos si se quiere publicar para usuarios finales.
