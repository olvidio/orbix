---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadtarifas"
titulo: "Tipo Tarifa"
flujo: "actividadtarifas.tipo_tarifa.gestionar.flujo"
preguntas: ["Como crear o modificar en Tipo Tarifa?", "Como eliminar en Tipo Tarifa?", "Como consultar el listado en Tipo Tarifa?", "Como abrir el formulario en Tipo Tarifa?"]
pantallas_principales: ["actividadtarifas.pantalla.tarifa"]
fragmentos: ["actividadtarifas.pantalla.tarifa_form", "actividadtarifas.pantalla.tarifa_lista"]
endpoints: ["/src/actividadtarifas/tipo_tarifa_eliminar", "/src/actividadtarifas/tipo_tarifa_form_data", "/src/actividadtarifas/tipo_tarifa_lista_data", "/src/actividadtarifas/tipo_tarifa_update"]
source: "docs/catalogo/actividadtarifas/flujos/tipo_tarifa.md"
estado_revision: "generado"
---

# Ayuda IA - Tipo Tarifa

Usa este documento para responder preguntas de usuario sobre como trabajar con `Tipo Tarifa`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear o modificar en Tipo Tarifa?
- Como eliminar en Tipo Tarifa?
- Como consultar el listado en Tipo Tarifa?
- Como abrir el formulario en Tipo Tarifa?

## Donde Entrar

- Tarifa (`actividadtarifas.pantalla.tarifa`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear o modificar

1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadtarifas/tipo_tarifa_update`

## Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadtarifas/tipo_tarifa_eliminar`

## Consultar el listado

1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadtarifas/tipo_tarifa_lista_data`

## Abrir el formulario

1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadtarifas/tipo_tarifa_form_data`

## Pantallas Y Fragmentos Relacionados

- `actividadtarifas.pantalla.tarifa`
- `actividadtarifas.pantalla.tarifa_form`
- `actividadtarifas.pantalla.tarifa_lista`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
