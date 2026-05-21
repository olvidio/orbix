---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "cambios"
titulo: "Avisos Generar"
flujo: "cambios.avisos_generar.gestionar.flujo"
preguntas: ["Como consultar el listado en Avisos Generar?"]
pantallas_principales: []
fragmentos: ["cambios.pantalla.avisos_generar"]
endpoints: ["/src/cambios/avisos_generar_lista_data"]
source: "docs/catalogo/cambios/flujos/avisos_generar.md"
estado_revision: "generado"
---

# Ayuda IA - Avisos Generar

Usa este documento para responder preguntas de usuario sobre como trabajar con `Avisos Generar`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar el listado en Avisos Generar?

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
- `/src/cambios/avisos_generar_lista_data`

## Pantallas Y Fragmentos Relacionados

- `cambios.pantalla.avisos_generar`

## Objetivo

Gestiona AvisosGenerar. Listado de avisos CambioUsuario (con avisado=false) para el usuario/aviso_tipo dado + opciones de desplegables de la pantalla avisos_generar.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
