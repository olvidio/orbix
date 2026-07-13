---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Tabla listado actividades"
flujo: "actividades.lista_activ.gestionar.flujo"
preguntas: []
pantallas_principales: ["actividades.pantalla.lista_activ_que"]
fragmentos: ["actividades.pantalla.lista_activ"]
endpoints: ["/src/actividades/lista_activ_datos"]
source: "docs/catalogo/actividades/flujos/lista_activ.md"
estado_revision: "generado"
---

# Ayuda IA - Tabla listado actividades

Usa este documento para responder preguntas de usuario sobre como trabajar con `Tabla listado actividades`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Filtros listados SR/SG (`actividades.pantalla.lista_activ_que`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.lista_activ_que`
- `actividades.pantalla.lista_activ`

## Objetivo

Ver tabla de actividades tras enviar filtros desde `lista_activ_que` o `actividad_que`.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
