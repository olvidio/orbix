---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Ver las actividades de cada centro"
flujo: "actividades.lista_centros_activ.gestionar.flujo"
preguntas: ["Como ver las actividades de cada centro en Ver las actividades de cada centro?"]
pantallas_principales: ["actividades.pantalla.actividades_centro_que"]
fragmentos: ["actividades.pantalla.lista_centros_activ"]
endpoints: ["/src/actividades/lista_centros_activ_datos"]
source: "docs/catalogo/actividades/flujos/lista_centros_activ.md"
estado_revision: "generado"
---

# Ayuda IA - Ver las actividades de cada centro

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver las actividades de cada centro`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver las actividades de cada centro en Ver las actividades de cada centro?

## Donde Entrar

- Seleccionar centro y periodo (listados por ctr) (`actividades.pantalla.actividades_centro_que`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver las actividades de cada centro

1. Entrar en **vsg > crt** (o **cv**) **> de cada ctr**.
2. Elegir el centro y el periodo y buscar.
3. El listado aparece en la misma pantalla; pulsar una actividad para abrirla.

Referencias tecnicas para verificar la respuesta:
- `/src/actividades/lista_centros_activ_datos`

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.actividades_centro_que`
- `actividades.pantalla.lista_centros_activ`

## Objetivo

Ver, tras elegir centro y periodo, las actividades de cada centro de sg.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
