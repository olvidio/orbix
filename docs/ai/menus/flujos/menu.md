---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "menus"
titulo: "Menu"
flujo: "menus.menu.gestionar.flujo"
preguntas: ["Como copiar en Menu?", "Como eliminar en Menu?", "Como guardar en Menu?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/menus/menu_copiar", "/src/menus/menu_eliminar", "/src/menus/menu_guardar"]
source: "docs/catalogo/menus/flujos/menu.md"
estado_revision: "generado"
---

# Ayuda IA - Menu

Usa este documento para responder preguntas de usuario sobre como trabajar con `Menu`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como copiar en Menu?
- Como eliminar en Menu?
- Como guardar en Menu?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Copiar

1. Abrir el listado en el contexto origen/destino correspondiente.
2. Pulsar la accion de copiar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que los datos copiados aparecen en el listado.

Referencias tecnicas para verificar la respuesta:
- `/src/menus/menu_copiar`

## Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Referencias tecnicas para verificar la respuesta:
- `/src/menus/menu_eliminar`

## Guardar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Gestiona Menu. Descripcion funcional pendiente de revisar.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
