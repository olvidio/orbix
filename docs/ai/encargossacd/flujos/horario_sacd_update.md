---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Guardar un horario de sacerdote"
flujo: "encargossacd.horario_sacd_update.gestionar.flujo"
preguntas: ["Como guardar un horario de sacerdote en Guardar un horario de sacerdote?"]
pantallas_principales: ["encargossacd.pantalla.horario_sacd_update"]
fragmentos: ["encargossacd.pantalla.horario_sacd_update"]
endpoints: ["/src/encargossacd/horario_sacd_update_data"]
source: "docs/catalogo/encargossacd/flujos/horario_sacd_update.md"
estado_revision: "generado"
---

# Ayuda IA - Guardar un horario de sacerdote

Usa este documento para responder preguntas de usuario sobre como trabajar con `Guardar un horario de sacerdote`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como guardar un horario de sacerdote en Guardar un horario de sacerdote?

## Donde Entrar

- Horario Sacd Update (`encargossacd.pantalla.horario_sacd_update`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Guardar un horario de sacerdote

1. Rellenar el horario y pulsar **guardar horario**.
2. Para borrarlo, **eliminar horario** y confirmar «¿Está seguro que desea borrar este horario?».

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/horario_sacd_update_data`

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.horario_sacd_update`
- `encargossacd.pantalla.horario_sacd_update`

## Objetivo

Guardar, modificar o quitar el horario de un sacerdote en un encargo.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
