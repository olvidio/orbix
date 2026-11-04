---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Consultar el resumen de pendientes"
flujo: "notas.asignaturas_pendientes_resumen.gestionar.flujo"
preguntas: ["Como consultar el resumen de pendientes en Consultar el resumen de pendientes?"]
pantallas_principales: ["notas.pantalla.asignaturas_pendientes_resumen"]
fragmentos: ["notas.pantalla.asignaturas_pendientes_resumen"]
endpoints: ["/src/notas/asignaturas_pendientes_resumen_data"]
source: "docs/catalogo/notas/flujos/asignaturas_pendientes_resumen.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar el resumen de pendientes

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar el resumen de pendientes`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar el resumen de pendientes en Consultar el resumen de pendientes?

## Donde Entrar

- Asignaturas Pendientes Resumen (`notas.pantalla.asignaturas_pendientes_resumen`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar el resumen de pendientes

1. Entrar en **resumen pendientes**.
2. Cada fila es una asignatura (con sus créditos y año). Las columnas dan el número de alumnos que la tienen pendiente: las que empiezan por **n** son numerarios y las que empiezan por **a** son agregados. Después de la letra va el curso en que está el alumno: **b** = bienio, **c1** = cuadrienio año I, **c2** = cuadrienio año II. La columna **total** suma b + c1 + c2. Así, nb, nc1, nc2 y ntotal son numerarios; ab, ac1, ac2 y atotal son agregados.

Referencias tecnicas para verificar la respuesta:
- `/src/notas/asignaturas_pendientes_resumen_data`

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.asignaturas_pendientes_resumen`
- `notas.pantalla.asignaturas_pendientes_resumen`

## Objetivo

Ver, para cada asignatura, cuántos alumnos la tienen pendiente.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
