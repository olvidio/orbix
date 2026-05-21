---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "cartaspresentacion"
titulo: "Carta Presentacion"
flujo: "cartaspresentacion.carta_presentacion.gestionar.flujo"
preguntas: ["Como crear o modificar en Carta Presentacion?", "Como eliminar en Carta Presentacion?", "Como abrir el formulario en Carta Presentacion?"]
pantallas_principales: []
fragmentos: ["cartaspresentacion.pantalla.cartas_presentacion_form"]
endpoints: ["/src/cartaspresentacion/carta_presentacion_eliminar", "/src/cartaspresentacion/carta_presentacion_form_data", "/src/cartaspresentacion/carta_presentacion_update"]
source: "docs/catalogo/cartaspresentacion/flujos/carta_presentacion.md"
estado_revision: "generado"
---

# Ayuda IA - Carta Presentacion

Usa este documento para responder preguntas de usuario sobre como trabajar con `Carta Presentacion`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear o modificar en Carta Presentacion?
- Como eliminar en Carta Presentacion?
- Como abrir el formulario en Carta Presentacion?

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
- `/src/cartaspresentacion/carta_presentacion_update`

## Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Referencias tecnicas para verificar la respuesta:
- `/src/cartaspresentacion/carta_presentacion_eliminar`

## Abrir el formulario

1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

Referencias tecnicas para verificar la respuesta:
- `/src/cartaspresentacion/carta_presentacion_form_data`

## Pantallas Y Fragmentos Relacionados

- `cartaspresentacion.pantalla.cartas_presentacion_form`

## Objetivo

Gestiona CartaPresentacion. Crea / actualiza una CartaPresentacion. Datos del formulario de modificacion de una CartaPresentacion (valida permisos: solo dl propia o cr). Elimina una CartaPresentacion.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
