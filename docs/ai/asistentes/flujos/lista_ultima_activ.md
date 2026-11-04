---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "asistentes"
titulo: "Consultar quién no ha asistido recientemente"
flujo: "asistentes.lista_ultima_activ.gestionar.flujo"
preguntas: ["Como consultar quién no ha asistido recientemente en Consultar quién no ha asistido recientemente?"]
pantallas_principales: ["asistentes.pantalla.lista_ultima_activ"]
fragmentos: ["asistentes.pantalla.lista_ultima_activ"]
endpoints: ["/src/asistentes/lista_ultima_activ_data"]
source: "docs/catalogo/asistentes/flujos/lista_ultima_activ.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar quién no ha asistido recientemente

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar quién no ha asistido recientemente`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar quién no ha asistido recientemente en Consultar quién no ha asistido recientemente?

## Donde Entrar

- Lista Ultima Activ (`asistentes.pantalla.lista_ultima_activ`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar quién no ha asistido recientemente

1. Entrar en la opción **s que no han ido al crt** (o a la **cv**) del curso actual o del anterior.
2. Se muestra la lista con la fecha de su última asistencia.

Referencias tecnicas para verificar la respuesta:
- `/src/asistentes/lista_ultima_activ_data`

## Pantallas Y Fragmentos Relacionados

- `asistentes.pantalla.lista_ultima_activ`
- `asistentes.pantalla.lista_ultima_activ`

## Objetivo

Informe de las personas s que no han asistido recientemente al crt o a la cv.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
