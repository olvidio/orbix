---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Listar las matrículas de un periodo"
flujo: "actividadestudios.matriculas.gestionar.flujo"
preguntas: ["Como listar las matrículas de un periodo en Listar las matrículas de un periodo?"]
pantallas_principales: ["actividadestudios.pantalla.matriculas_lista"]
fragmentos: ["actividadestudios.pantalla.matriculas_lista"]
endpoints: ["/src/actividadestudios/matriculas_lista_data"]
source: "docs/catalogo/actividadestudios/flujos/matriculas.md"
estado_revision: "generado"
---

# Ayuda IA - Listar las matrículas de un periodo

Usa este documento para responder preguntas de usuario sobre como trabajar con `Listar las matrículas de un periodo`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como listar las matrículas de un periodo en Listar las matrículas de un periodo?

## Donde Entrar

- Matriculas Lista (`actividadestudios.pantalla.matriculas_lista`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Listar las matrículas de un periodo

1. Entrar en **Matrículas**: «PERIODO DE SELECCIÓN DE ACTIVIDADES».
2. Elegir todo el año, un trimestre, el curso ca u **otro** (con fechas) y el año.
3. Pulsar **buscar** para ver la lista de matrículas.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/matriculas_lista_data`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.matriculas_lista`
- `actividadestudios.pantalla.matriculas_lista`

## Objetivo

Ver las matrículas de las actividades de un periodo.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
