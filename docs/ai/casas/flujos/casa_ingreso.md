---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "casas"
titulo: "Casa Ingreso"
flujo: "casas.casa_ingreso.gestionar.flujo"
preguntas: ["Como crear o modificar en Casa Ingreso?", "Como eliminar en Casa Ingreso?", "Como abrir el formulario en Casa Ingreso?"]
pantallas_principales: ["casas.pantalla.casa"]
fragmentos: ["casas.pantalla.casa_ingreso_form"]
endpoints: ["/src/casas/casa_ingreso_eliminar", "/src/casas/casa_ingreso_form_data", "/src/casas/casa_ingreso_update"]
source: "docs/catalogo/casas/flujos/casa_ingreso.md"
estado_revision: "generado"
---

# Ayuda IA - Casa Ingreso

Usa este documento para responder preguntas de usuario sobre como trabajar con `Casa Ingreso`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear o modificar en Casa Ingreso?
- Como eliminar en Casa Ingreso?
- Como abrir el formulario en Casa Ingreso?

## Donde Entrar

- Casa (`casas.pantalla.casa`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear o modificar

1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Referencias tecnicas para verificar la respuesta:
- `/src/casas/casa_ingreso_update`

## Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Referencias tecnicas para verificar la respuesta:
- `/src/casas/casa_ingreso_eliminar`

## Abrir el formulario

1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

Referencias tecnicas para verificar la respuesta:
- `/src/casas/casa_ingreso_form_data`

## Pantallas Y Fragmentos Relacionados

- `casas.pantalla.casa`
- `casas.pantalla.casa_ingreso_form`

## Objetivo

Gestiona CasaIngreso. Crear/actualizar el Ingreso de una actividad. Datos para el formulario de ingreso de una actividad (casa_ingreso_form). Eliminar el Ingreso de una actividad.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
