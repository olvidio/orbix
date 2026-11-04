---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Ver el listado de actividades"
flujo: "actividades.lista_activ.gestionar.flujo"
preguntas: ["Como ver el listado de actividades en Ver el listado de actividades?"]
pantallas_principales: ["actividades.pantalla.lista_activ_que"]
fragmentos: ["actividades.pantalla.lista_activ"]
endpoints: ["/src/actividades/lista_activ_datos"]
source: "docs/catalogo/actividades/flujos/lista_activ.md"
estado_revision: "generado"
---

# Ayuda IA - Ver el listado de actividades

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver el listado de actividades`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver el listado de actividades en Ver el listado de actividades?

## Donde Entrar

- Filtros listados SR/SG (`actividades.pantalla.lista_activ_que`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver el listado de actividades

1. Rellenar el formulario con el tipo de actividad, el periodo y las secciones; si no se ponen fechas, se toma el curso actual.
2. Pulsar buscar.
3. Aparece la tabla de actividades que cumplen los filtros.

Referencias tecnicas para verificar la respuesta:
- `/src/actividades/lista_activ_datos`

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.lista_activ_que`
- `actividades.pantalla.lista_activ`

## Objetivo

Ver la tabla de actividades después de enviar los filtros del formulario de búsqueda.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
