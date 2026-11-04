---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Consultar alumnos de otras regiones"
flujo: "actividadestudios.matriculas_lista_otras_r.gestionar.flujo"
preguntas: ["Como consultar alumnos de otras regiones en Consultar alumnos de otras regiones?"]
pantallas_principales: ["actividadestudios.pantalla.matriculas_lista_otras_r"]
fragmentos: ["actividadestudios.pantalla.matriculas_lista_otras_r"]
endpoints: ["/src/actividadestudios/matriculas_lista_otras_r_data"]
source: "docs/catalogo/actividadestudios/flujos/matriculas_lista_otras_r.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar alumnos de otras regiones

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar alumnos de otras regiones`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar alumnos de otras regiones en Consultar alumnos de otras regiones?

## Donde Entrar

- Matriculas Lista Otras R (`actividadestudios.pantalla.matriculas_lista_otras_r`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar alumnos de otras regiones

1. Abrir **Envío información a otras r**.
2. Si se quiere, escribir un apellido y pulsar **buscar**.
3. Aparecen los alumnos con sus avisos y asignaturas.
4. Seleccionar un alumno y pulsar **imprimir certificado** para emitir su E43.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/matriculas_lista_otras_r_data`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.matriculas_lista_otras_r`
- `actividadestudios.pantalla.matriculas_lista_otras_r`

## Objetivo

Buscar por apellido a alumnos de otras regiones para ver sus asignaturas matriculadas y emitir certificados E43. Solo está disponible para las regiones del stgr.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
