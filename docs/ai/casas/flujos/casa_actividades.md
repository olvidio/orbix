---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "casas"
titulo: "Casa Actividades"
flujo: "casas.casa_actividades.gestionar.flujo"
preguntas: ["Como consultar el listado en Casa Actividades?"]
pantallas_principales: []
fragmentos: ["casas.pantalla.casa_actividades_lista"]
endpoints: ["/src/casas/casa_actividades_lista_data"]
source: "docs/catalogo/casas/flujos/casa_actividades.md"
estado_revision: "generado"
---

# Ayuda IA - Casa Actividades

Usa este documento para responder preguntas de usuario sobre como trabajar con `Casa Actividades`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar el listado en Casa Actividades?

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
- `/src/casas/casa_actividades_lista_data`

## Pantallas Y Fragmentos Relacionados

- `casas.pantalla.casa_actividades_lista`

## Objetivo

Gestiona CasaActividades. Listado de actividades por casa y periodo (casa_actividades_lista).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
