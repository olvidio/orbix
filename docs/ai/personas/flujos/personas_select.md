---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "personas"
titulo: "Buscar y listar personas"
flujo: "personas.personas_select.gestionar.flujo"
preguntas: []
pantallas_principales: ["personas.pantalla.personas_que", "personas.pantalla.personas_select"]
fragmentos: []
endpoints: ["/src/personas/personas_select_data"]
source: "docs/catalogo/personas/flujos/personas_select.md"
estado_revision: "generado"
---

# Ayuda IA - Buscar y listar personas

Usa este documento para responder preguntas de usuario sobre como trabajar con `Buscar y listar personas`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Buscar personas (`personas.pantalla.personas_que`)
- Resultado búsqueda personas (`personas.pantalla.personas_select`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `personas.pantalla.personas_que`
- `personas.pantalla.personas_select`

## Objetivo

Encontrar personas del colectivo indicado por el menú, revisar resultados y lanzar acciones (ficha, dossiers, STGR, traslado, módulos satélite).

## Errores Documentados

- `No se encuentra ningún centro con esta condición`
- `Avisos suaves región/persona no válida (listado vacío con mensaje)`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
