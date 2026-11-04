---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Ver y borrar matrículas pendientes"
flujo: "actividadestudios.matriculas_pendientes.gestionar.flujo"
preguntas: ["Como ver y borrar matrículas pendientes en Ver y borrar matrículas pendientes?"]
pantallas_principales: ["actividadestudios.pantalla.matriculas_pendientes"]
fragmentos: ["actividadestudios.pantalla.matriculas_pendientes"]
endpoints: ["/src/actividadestudios/matriculas_pendientes_data"]
source: "docs/catalogo/actividadestudios/flujos/matriculas_pendientes.md"
estado_revision: "generado"
---

# Ayuda IA - Ver y borrar matrículas pendientes

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver y borrar matrículas pendientes`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver y borrar matrículas pendientes en Ver y borrar matrículas pendientes?

## Donde Entrar

- Matriculas Pendientes (`actividadestudios.pantalla.matriculas_pendientes`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver y borrar matrículas pendientes

1. Entrar en **Matr. Pendientes**: aparece una lista de matrículas con casilla para marcar.
2. Marcar las matrículas y pulsar **ver asignaturas ca** (ver sus asignaturas) o **borrar matrícula**.
3. Si hay datos huérfanos, salen mensajes como «No encuentro a nadie con id_nom: …» o «No encuentro ninguna actividad con id: … (alumno id_nom: …)».

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/matriculas_pendientes_data`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.matriculas_pendientes`
- `actividadestudios.pantalla.matriculas_pendientes`

## Objetivo

Ver las matrículas de actividades pendientes de procesar y borrar las que no valgan.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
