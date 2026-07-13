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

1. Abrir **Matrículas** desde el menú.
2. Elegir año y periodo (por defecto `curso_ca`) y pulsar **buscar**.
3. El sistema consulta `matriculas_lista_data` y muestra la tabla paginada.
4. Opcionalmente, seleccionar filas para ver dossier CA o borrar matrículas.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/matriculas_lista_data`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.matriculas_lista`

## Objetivo

El usuario elige un periodo y pulsa **buscar**: el sistema muestra la tabla de matrículas de actividades cuyo `f_ini` cae en ese intervalo, con alumno, centro, actividad, asignatura, preceptor y nota.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
