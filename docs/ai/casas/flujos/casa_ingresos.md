---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "casas"
titulo: "Casa Ingresos"
flujo: "casas.casa_ingresos.gestionar.flujo"
preguntas: ["Como consultar el listado en Casa Ingresos?"]
pantallas_principales: []
fragmentos: ["casas.pantalla.casa_ingresos_lista"]
endpoints: ["/src/casas/casa_ingresos_lista_data"]
source: "docs/catalogo/casas/flujos/casa_ingresos.md"
estado_revision: "generado"
---

# Ayuda IA - Casa Ingresos

Usa este documento para responder preguntas de usuario sobre como trabajar con `Casa Ingresos`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar el listado en Casa Ingresos?

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
- `/src/casas/casa_ingresos_lista_data`

## Pantallas Y Fragmentos Relacionados

- `casas.pantalla.casa_ingresos_lista`

## Objetivo

Gestiona CasaIngresos. Listado económico de actividades por casa (casa_ingresos_lista).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
