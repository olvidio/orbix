---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "asistentes"
titulo: "Ver los estudios matriculados por centro"
flujo: "asistentes.lista_est_ctr.gestionar.flujo"
preguntas: ["Como ver los estudios matriculados por centro en Ver los estudios matriculados por centro?"]
pantallas_principales: ["asistentes.pantalla.lista_est_ctr"]
fragmentos: ["asistentes.pantalla.lista_est_ctr"]
endpoints: ["/src/asistentes/lista_est_ctr_data"]
source: "docs/catalogo/asistentes/flujos/lista_est_ctr.md"
estado_revision: "generado"
---

# Ayuda IA - Ver los estudios matriculados por centro

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver los estudios matriculados por centro`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver los estudios matriculados por centro en Ver los estudios matriculados por centro?

## Donde Entrar

- Lista Est Ctr (`asistentes.pantalla.lista_est_ctr`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver los estudios matriculados por centro

1. Elegir los centros y el curso y pulsar **Buscar**.
2. Debería aparecer, por centro, la lista de personas con sus estudios matriculados (pantalla no comprobada en la instancia).

Referencias tecnicas para verificar la respuesta:
- `/src/asistentes/lista_est_ctr_data`

## Pantallas Y Fragmentos Relacionados

- `asistentes.pantalla.lista_est_ctr`
- `asistentes.pantalla.lista_est_ctr`

## Objetivo

Ver, centro por centro, los estudios en los que están matriculadas sus personas.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
