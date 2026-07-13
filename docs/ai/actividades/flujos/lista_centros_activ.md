---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Listado por centros"
flujo: "actividades.lista_centros_activ.gestionar.flujo"
preguntas: []
pantallas_principales: ["actividades.pantalla.actividades_centro_que"]
fragmentos: ["actividades.pantalla.lista_centros_activ"]
endpoints: ["/src/actividades/lista_centros_activ_datos"]
source: "docs/catalogo/actividades/flujos/lista_centros_activ.md"
estado_revision: "generado"
---

# Ayuda IA - Listado por centros

Usa este documento para responder preguntas de usuario sobre como trabajar con `Listado por centros`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Seleccionar centro y periodo (listados por ctr) (`actividades.pantalla.actividades_centro_que`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.actividades_centro_que`
- `actividades.pantalla.lista_centros_activ`

## Objetivo

Tras elegir centro y periodo en *de cada ctr*, ver el listado AJAX en la misma pantalla.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
