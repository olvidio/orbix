---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Sacd Ficha"
flujo: "encargossacd.sacd_ficha.gestionar.flujo"
preguntas: ["Como crear o modificar en Sacd Ficha?", "Como obtener datos en Sacd Ficha?"]
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.sacd_ficha_ajax"]
endpoints: ["/src/encargossacd/sacd_ficha_data", "/src/encargossacd/sacd_ficha_update"]
source: "docs/catalogo/encargossacd/flujos/sacd_ficha.md"
estado_revision: "generado"
---

# Ayuda IA - Sacd Ficha

Usa este documento para responder preguntas de usuario sobre como trabajar con `Sacd Ficha`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear o modificar en Sacd Ficha?
- Como obtener datos en Sacd Ficha?

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
- `/src/encargossacd/sacd_ficha_update`

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.sacd_ficha_ajax`

## Objetivo

Gestiona SacdFicha. Datos para la ficha de encargos de un SACD (sacd_ficha_ajax?que=ficha). Porta la lectura del antiguo controlador frontend y devuelve un payload estructurado con los encargos y sus dedicaciones (horario del centro y del SACD ya calculadas como texto cuando mod_horario=3). Mutacion de la ficha de encargos de un SACD (sacd_ficha_ajax?que=update). Porta la logica del antiguo controlador frontend, haciendo la misma actualizacion de dedicaciones por modulo y de observaciones.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
