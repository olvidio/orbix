---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Matriculas"
flujo: "actividadestudios.matriculas.gestionar.flujo"
preguntas: ["Como consultar el listado en Matriculas?"]
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.matriculas_lista"]
endpoints: ["/src/actividadestudios/matriculas_lista_data"]
source: "docs/catalogo/actividadestudios/flujos/matriculas.md"
estado_revision: "generado"
---

# Ayuda IA - Matriculas

Usa este documento para responder preguntas de usuario sobre como trabajar con `Matriculas`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar el listado en Matriculas?

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
- `/src/actividadestudios/matriculas_lista_data`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.matriculas_lista`

## Objetivo

Gestiona Matriculas. Listado de matrículas en un intervalo de fechas (actividades cuyo f_ini cae en el periodo). Usado por matriculas_lista vía PostRequest.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
