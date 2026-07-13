---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadtarifas"
titulo: "Tarifa Ubi"
flujo: "actividadtarifas.tarifa_ubi.gestionar.flujo"
preguntas: ["Como actualizar importes en lote en Tarifa Ubi?", "Como copiar en Tarifa Ubi?", "Como crear o modificar en Tarifa Ubi?", "Como eliminar en Tarifa Ubi?", "Como consultar el listado en Tarifa Ubi?", "Como abrir el formulario en Tarifa Ubi?"]
pantallas_principales: ["actividadtarifas.pantalla.tarifa_ubi"]
fragmentos: ["actividadtarifas.pantalla.tarifa_ubi_form", "actividadtarifas.pantalla.tarifa_ubi_lista"]
endpoints: ["/src/actividadtarifas/tarifa_ubi_copiar", "/src/actividadtarifas/tarifa_ubi_eliminar", "/src/actividadtarifas/tarifa_ubi_form_data", "/src/actividadtarifas/tarifa_ubi_lista_data", "/src/actividadtarifas/tarifa_ubi_update", "/src/actividadtarifas/tarifa_ubi_update_inc"]
source: "docs/catalogo/actividadtarifas/flujos/tarifa_ubi.md"
estado_revision: "generado"
---

# Ayuda IA - Tarifa Ubi

Usa este documento para responder preguntas de usuario sobre como trabajar con `Tarifa Ubi`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como actualizar importes en lote en Tarifa Ubi?
- Como copiar en Tarifa Ubi?
- Como crear o modificar en Tarifa Ubi?
- Como eliminar en Tarifa Ubi?
- Como consultar el listado en Tarifa Ubi?
- Como abrir el formulario en Tarifa Ubi?

## Donde Entrar

- Tarifa Ubi (`actividadtarifas.pantalla.tarifa_ubi`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Actualizar importes en lote

1. Abrir la pantalla o proceso que permite actualizacion en lote.
2. Revisar el conjunto de registros afectados.
3. Ejecutar la actualizacion.
4. Comprobar importes o valores recalculados.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadtarifas/tarifa_ubi_update_inc`

## Copiar

1. Cargar listado de casa/año destino.
2. Pulsar copiar tarifas del año anterior (solo si hay `token_copiar`).
3. Confirmar; el cliente reenvía la cápsula `ctx_copiar` (HashB) sin inspeccionarla.
4. **Nota:** la operación devuelve hoy «función pendiente de reimplementar».

Referencias tecnicas para verificar la respuesta:
- `/src/actividadtarifas/tarifa_ubi_copiar`

## Crear o modificar

1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadtarifas/tarifa_ubi_update`

## Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadtarifas/tarifa_ubi_eliminar`

## Consultar el listado

1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadtarifas/tarifa_ubi_lista_data`

## Abrir el formulario

1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadtarifas/tarifa_ubi_form_data`

## Pantallas Y Fragmentos Relacionados

- `actividadtarifas.pantalla.tarifa_ubi`
- `actividadtarifas.pantalla.tarifa_ubi_form`
- `actividadtarifas.pantalla.tarifa_ubi_lista`

## Objetivo

Consultar y mantener las tarifas económicas de una casa para un año: listado, alta, edición, eliminación, copia desde el año anterior y actualización en lote desde el estudio económico. Plantilla de redacción revisada en `docs/manual/actividadtarifas.md` (sección Tarifa Ubi).

## Errores Documentados

- `Operación no autorizada (cápsula HashB inválida en update/eliminar/copiar)`
- `función de copiar tarifas pendiente de reimplementar`
- `hay un error, no se ha borrado`
- `hay un error, no se ha guardado`
- `no se encuentra la tarifa`
- `no sé cuál he de borrar`
- `no sé qué casa/año tengo que copiar`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
