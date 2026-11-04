---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "menus"
titulo: "Ver mi menú lateral"
flujo: "menus.grupmenu_coleccion.gestionar.flujo"
preguntas: ["Como ver mi menú lateral en Ver mi menú lateral?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/menus/grupmenu_coleccion"]
source: "docs/catalogo/menus/flujos/grupmenu_coleccion.md"
estado_revision: "generado"
---

# Ayuda IA - Ver mi menú lateral

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver mi menú lateral`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver mi menú lateral en Ver mi menú lateral?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver mi menú lateral

1. Iniciar sesión.
2. El menú lateral muestra solo los grupos y opciones autorizados para el rol del usuario.

Referencias tecnicas para verificar la respuesta:
- `/src/menus/grupmenu_coleccion`

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Cargar los grupos e ítems de menú que puede ver el usuario en el menú lateral.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
