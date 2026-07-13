---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubiscamas"
titulo: "Habitacion"
flujo: "ubiscamas.habitacion.gestionar.flujo"
preguntas: ["Como crear o modificar en Habitacion?", "Como eliminar en Habitacion?", "Como abrir el formulario en Habitacion?"]
pantallas_principales: []
fragmentos: ["ubiscamas.pantalla.habitacion_form"]
endpoints: ["/src/ubiscamas/habitacion_delete", "/src/ubiscamas/habitacion_form_data", "/src/ubiscamas/habitacion_update"]
source: "docs/catalogo/ubiscamas/flujos/habitacion.md"
estado_revision: "generado"
---

# Ayuda IA - Habitacion

Usa este documento para responder preguntas de usuario sobre como trabajar con `Habitacion`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear o modificar en Habitacion?
- Como eliminar en Habitacion?
- Como abrir el formulario en Habitacion?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear o modificar

1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Referencias tecnicas para verificar la respuesta:
- `/src/ubiscamas/habitacion_update`

## Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Referencias tecnicas para verificar la respuesta:
- `/src/ubiscamas/habitacion_delete`

## Abrir el formulario

1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

Referencias tecnicas para verificar la respuesta:
- `/src/ubiscamas/habitacion_form_data`

## Pantallas Y Fragmentos Relacionados

- `ubiscamas.pantalla.habitacion_form`

## Objetivo

Dar de alta, modificar o eliminar habitaciones de un ubi CDC, incluyendo creación automática de camas según número indicado.

## Errores Documentados

- `No se encontró la habitación a eliminar`
- `hay un error, no se ha eliminado la habitación`
- `Error al eliminar la habitación`
- `Habitación no válida`
- `Error al guardar la habitación`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
