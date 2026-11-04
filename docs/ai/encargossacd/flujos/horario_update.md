---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Guardar un horario de encargo"
flujo: "encargossacd.horario_update.gestionar.flujo"
preguntas: ["Como guardar un horario de encargo en Guardar un horario de encargo?"]
pantallas_principales: ["encargossacd.pantalla.horario_update"]
fragmentos: ["encargossacd.pantalla.horario_update"]
endpoints: ["/src/encargossacd/horario_update_data"]
source: "docs/catalogo/encargossacd/flujos/horario_update.md"
estado_revision: "generado"
---

# Ayuda IA - Guardar un horario de encargo

Usa este documento para responder preguntas de usuario sobre como trabajar con `Guardar un horario de encargo`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como guardar un horario de encargo en Guardar un horario de encargo?

## Donde Entrar

- Horario Update (`encargossacd.pantalla.horario_update`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Guardar un horario de encargo

1. Rellenar día, horas y periodo.
2. Pulsar guardar. Para quitarlo, usar eliminar y confirmar.

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/horario_update_data`

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.horario_update`
- `encargossacd.pantalla.horario_update`

## Objetivo

Guardar, modificar o quitar un horario de un encargo.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
