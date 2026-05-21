---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "cartaspresentacion"
titulo: "Cartas Presentacion"
flujo: "cartaspresentacion.cartas_presentacion.gestionar.flujo"
preguntas: ["Como consultar el listado en Cartas Presentacion?"]
pantallas_principales: []
fragmentos: ["cartaspresentacion.pantalla.cartas_presentacion_lista"]
endpoints: ["/src/cartaspresentacion/cartas_presentacion_lista_data"]
source: "docs/catalogo/cartaspresentacion/flujos/cartas_presentacion.md"
estado_revision: "generado"
---

# Ayuda IA - Cartas Presentacion

Usa este documento para responder preguntas de usuario sobre como trabajar con `Cartas Presentacion`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar el listado en Cartas Presentacion?

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
- `/src/cartaspresentacion/cartas_presentacion_lista_data`

## Pantallas Y Fragmentos Relacionados

- `cartaspresentacion.pantalla.cartas_presentacion_lista`

## Objetivo

Gestiona CartasPresentacion. Listado agrupado de cartas de presentacion (modo lista_dl, lista_todo o get con filtros).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
