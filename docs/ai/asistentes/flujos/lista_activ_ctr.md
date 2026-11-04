---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "asistentes"
titulo: "Ver las actividades por centro"
flujo: "asistentes.lista_activ_ctr.gestionar.flujo"
preguntas: ["Como ver las actividades por centro en Ver las actividades por centro?"]
pantallas_principales: ["asistentes.pantalla.lista_activ_ctr"]
fragmentos: ["asistentes.pantalla.lista_activ_ctr"]
endpoints: ["/src/asistentes/lista_activ_ctr_data"]
source: "docs/catalogo/asistentes/flujos/lista_activ_ctr.md"
estado_revision: "generado"
---

# Ayuda IA - Ver las actividades por centro

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver las actividades por centro`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver las actividades por centro en Ver las actividades por centro?

## Donde Entrar

- Lista Activ Ctr (`asistentes.pantalla.lista_activ_ctr`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver las actividades por centro

1. Elegir los centros y el periodo (ver «Elegir centro y periodo») y pulsar **Buscar**.
2. Para cada centro (por ejemplo «Alimara») aparece una tabla «nombre / actividades previstas» con cada persona y su actividad (por ejemplo «ca n repaso Hostal Rialb (7/8/2026-29/8/2026)-dlb»), «NO HACE CA [pedida]» o «pendiente de solicitar» si aún no tiene.

Referencias tecnicas para verificar la respuesta:
- `/src/asistentes/lista_activ_ctr_data`

## Pantallas Y Fragmentos Relacionados

- `asistentes.pantalla.lista_activ_ctr`
- `asistentes.pantalla.lista_activ_ctr`

## Objetivo

Ver, centro por centro, las actividades previstas de sus personas.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
