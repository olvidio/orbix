---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "menus"
titulo: "Grupmenu"
flujo: "menus.grupmenu.gestionar.flujo"
preguntas: ["Como eliminar en Grupmenu?", "Como guardar en Grupmenu?", "Como consultar el listado en Grupmenu?"]
pantallas_principales: []
fragmentos: ["menus.pantalla.grupmenu_lista", "menus.pantalla.menus_get", "menus.pantalla.menus_que"]
endpoints: ["/src/menus/grupmenu_eliminar", "/src/menus/grupmenu_guardar", "/src/menus/grupmenu_lista"]
source: "docs/catalogo/menus/flujos/grupmenu.md"
estado_revision: "generado"
---

# Ayuda IA - Grupmenu

Usa este documento para responder preguntas de usuario sobre como trabajar con `Grupmenu`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como eliminar en Grupmenu?
- Como guardar en Grupmenu?
- Como consultar el listado en Grupmenu?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Referencias tecnicas para verificar la respuesta:
- `/src/menus/grupmenu_eliminar`

## Guardar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Consultar el listado

1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Referencias tecnicas para verificar la respuesta:
- `/src/menus/grupmenu_lista`

## Pantallas Y Fragmentos Relacionados

- `menus.pantalla.grupmenu_lista`
- `menus.pantalla.menus_get`
- `menus.pantalla.menus_que`

## Objetivo

Gestiona GrupMenuListaUseCase. Descripcion funcional pendiente de revisar.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
