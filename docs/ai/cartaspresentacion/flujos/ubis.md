---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "cartaspresentacion"
titulo: "Ubis"
flujo: "cartaspresentacion.ubis.gestionar.flujo"
preguntas: ["Como consultar el listado en Ubis?"]
pantallas_principales: []
fragmentos: ["cartaspresentacion.pantalla.cartas_presentacion_ubis_lista"]
endpoints: ["/src/cartaspresentacion/ubis_lista_data"]
source: "docs/catalogo/cartaspresentacion/flujos/ubis.md"
estado_revision: "generado"
---

# Ayuda IA - Ubis

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ubis`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar el listado en Ubis?

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
- `/src/cartaspresentacion/ubis_lista_data`

## Pantallas Y Fragmentos Relacionados

- `cartaspresentacion.pantalla.cartas_presentacion_ubis_lista`

## Objetivo

Gestiona CartasPresentacionUbis. Listado de centros con el estado de su carta de presentacion, en dos variantes (delegacion del usuario o centros extranjeros).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
