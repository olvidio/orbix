---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Elegir las actividades del equipaje"
flujo: "inventario.equipajes_lista_activ_periodo.gestionar.flujo"
preguntas: ["Como marcar las actividades del equipaje en Elegir las actividades del equipaje?"]
pantallas_principales: ["inventario.pantalla.equipajes_lista_activ_periodo"]
fragmentos: ["inventario.pantalla.equipajes_lista_activ_periodo"]
endpoints: ["/src/inventario/equipajes_lista_activ_periodo"]
source: "docs/catalogo/inventario/flujos/equipajes_lista_activ_periodo.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir las actividades del equipaje

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir las actividades del equipaje`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como marcar las actividades del equipaje en Elegir las actividades del equipaje?

## Donde Entrar

- Actividades por periodo (`inventario.pantalla.equipajes_lista_activ_periodo`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Marcar las actividades del equipaje

1. En **nuevo equipaje**, elegir periodo y casa.
2. Leer la lista: **empieza**, **termina**, **actividad** y **observaciones**.
3. Marcar las actividades que viajan juntas.
4. Pulsar **seleccionar** para ponerle nombre al equipaje.

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/equipajes_lista_activ_periodo`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.equipajes_lista_activ_periodo`
- `inventario.pantalla.equipajes_lista_activ_periodo`

## Objetivo

Ver las actividades de una casa en el periodo elegido, para marcar las que irán en el mismo equipaje.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
