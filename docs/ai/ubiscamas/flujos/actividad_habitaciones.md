---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubiscamas"
titulo: "Actividad Habitaciones"
flujo: "ubiscamas.actividad_habitaciones.gestionar.flujo"
preguntas: ["Como consultar el listado en Actividad Habitaciones?"]
pantallas_principales: []
fragmentos: ["ubiscamas.pantalla.lista_habitaciones", "ubiscamas.pantalla.lista_habitaciones_distribucion", "ubiscamas.pantalla.lista_habitaciones_nombres"]
endpoints: ["/src/ubiscamas/actividad_habitaciones_lista"]
source: "docs/catalogo/ubiscamas/flujos/actividad_habitaciones.md"
estado_revision: "generado"
---

# Ayuda IA - Actividad Habitaciones

Usa este documento para responder preguntas de usuario sobre como trabajar con `Actividad Habitaciones`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar el listado en Actividad Habitaciones?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar el listado

1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Referencias tecnicas para verificar la respuesta:
- `/src/ubiscamas/actividad_habitaciones_lista`

## Pantallas Y Fragmentos Relacionados

- `ubiscamas.pantalla.lista_habitaciones`
- `ubiscamas.pantalla.lista_habitaciones_distribucion`
- `ubiscamas.pantalla.lista_habitaciones_nombres`

## Objetivo

Gestiona HabitacionesCamaLista. Descripcion funcional pendiente de revisar.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
