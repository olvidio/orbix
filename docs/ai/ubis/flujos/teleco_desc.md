---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubis"
titulo: "Teleco Desc"
flujo: "ubis.teleco_desc.gestionar.flujo"
preguntas: ["Como consultar el listado en Teleco Desc?"]
pantallas_principales: []
fragmentos: ["ubis.pantalla.teleco_desc_lista_ajax"]
endpoints: ["/src/ubis/teleco_desc_lista"]
source: "docs/catalogo/ubis/flujos/teleco_desc.md"
estado_revision: "generado"
---

# Ayuda IA - Teleco Desc

Usa este documento para responder preguntas de usuario sobre como trabajar con `Teleco Desc`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar el listado en Teleco Desc?

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
- `/src/ubis/teleco_desc_lista`

## Pantallas Y Fragmentos Relacionados

- `ubis.pantalla.teleco_desc_lista_ajax`

## Objetivo

Gestiona TelecoDescLista. Descripcion funcional pendiente de revisar.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
