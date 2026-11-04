---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "asistentes"
titulo: "Ver las plazas de varias actividades"
flujo: "asistentes.lista_asis_conjunto_activ.gestionar.flujo"
preguntas: ["Como ver las plazas de varias actividades en Ver las plazas de varias actividades?"]
pantallas_principales: ["asistentes.pantalla.lista_asis_conjunto_activ"]
fragmentos: ["asistentes.pantalla.lista_asis_conjunto_activ"]
endpoints: ["/src/asistentes/lista_asis_conjunto_activ_data"]
source: "docs/catalogo/asistentes/flujos/lista_asis_conjunto_activ.md"
estado_revision: "generado"
---

# Ayuda IA - Ver las plazas de varias actividades

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver las plazas de varias actividades`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver las plazas de varias actividades en Ver las plazas de varias actividades?

## Donde Entrar

- Lista Asis Conjunto Activ (`asistentes.pantalla.lista_asis_conjunto_activ`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver las plazas de varias actividades

1. Seleccionar las actividades.
2. Elegir el listado conjunto: se muestran las plazas y asistentes de todas ellas.

Referencias tecnicas para verificar la respuesta:
- `/src/asistentes/lista_asis_conjunto_activ_data`

## Pantallas Y Fragmentos Relacionados

- `asistentes.pantalla.lista_asis_conjunto_activ`
- `asistentes.pantalla.lista_asis_conjunto_activ`

## Objetivo

Ver el listado conjunto de plazas y asistentes de varias actividades.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
