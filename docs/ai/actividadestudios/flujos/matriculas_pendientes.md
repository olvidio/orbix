---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Matriculas Pendientes"
flujo: "actividadestudios.matriculas_pendientes.gestionar.flujo"
preguntas: ["Como obtener datos en Matriculas Pendientes?"]
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.matriculas_pendientes"]
endpoints: ["/src/actividadestudios/matriculas_pendientes_data"]
source: "docs/catalogo/actividadestudios/flujos/matriculas_pendientes.md"
estado_revision: "generado"
---

# Ayuda IA - Matriculas Pendientes

Usa este documento para responder preguntas de usuario sobre como trabajar con `Matriculas Pendientes`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Matriculas Pendientes?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Abrir **Matr. Pendientes** / **Exam. pendientes de acta** desde el menú.
2. El sistema carga automáticamente `matriculas_pendientes_data`.
3. Se muestra la tabla con avisos de región STGR si aplica.
4. Opcional: ver dossier CA de una fila o borrar matrículas.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/matriculas_pendientes_data`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.matriculas_pendientes`

## Objetivo

El usuario consulta las matrículas que aún no tienen nota definitiva en acta: una fila por matrícula con actividad, asignatura, alumno y permiso. Puede abrir el dossier de la actividad o borrar matrículas seleccionadas.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
