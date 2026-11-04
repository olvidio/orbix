---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "asistentes"
titulo: "Preparar el informe de última asistencia"
flujo: "asistentes.lista_ultim_que_ctr.gestionar.flujo"
preguntas: ["Como preparar el informe de última asistencia en Preparar el informe de última asistencia?"]
pantallas_principales: ["asistentes.pantalla.lista_ultim_que_ctr"]
fragmentos: ["asistentes.pantalla.lista_ultim_que_ctr"]
endpoints: ["/src/asistentes/lista_ultim_que_ctr_data"]
source: "docs/catalogo/asistentes/flujos/lista_ultim_que_ctr.md"
estado_revision: "generado"
---

# Ayuda IA - Preparar el informe de última asistencia

Usa este documento para responder preguntas de usuario sobre como trabajar con `Preparar el informe de última asistencia`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como preparar el informe de última asistencia en Preparar el informe de última asistencia?

## Donde Entrar

- Lista Ultim Que Ctr (`asistentes.pantalla.lista_ultim_que_ctr`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Preparar el informe de última asistencia

1. Entrar en la opción que interese (este curso o el curso pasado).
2. Elegir el centro.
3. Se muestra el informe.

Referencias tecnicas para verificar la respuesta:
- `/src/asistentes/lista_ultim_que_ctr_data`

## Pantallas Y Fragmentos Relacionados

- `asistentes.pantalla.lista_ultim_que_ctr`
- `asistentes.pantalla.lista_ultim_que_ctr`

## Objetivo

Elegir centro y curso para el informe de última asistencia (quién no ha ido al crt o a la cv).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
