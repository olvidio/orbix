---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Matriculas Lista Otras R"
flujo: "actividadestudios.matriculas_lista_otras_r.gestionar.flujo"
preguntas: ["Como obtener datos en Matriculas Lista Otras R?"]
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.matriculas_lista_otras_r"]
endpoints: ["/src/actividadestudios/matriculas_lista_otras_r_data"]
source: "docs/catalogo/actividadestudios/flujos/matriculas_lista_otras_r.md"
estado_revision: "generado"
---

# Ayuda IA - Matriculas Lista Otras R

Usa este documento para responder preguntas de usuario sobre como trabajar con `Matriculas Lista Otras R`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Matriculas Lista Otras R?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Abrir **Envío información a otras r** (solo regiones STGR).
2. Opcionalmente filtrar por apellido y pulsar **buscar**.
3. El sistema consulta `matriculas_lista_otras_r_data` y muestra alumnos con alertas y
4. Seleccionar un alumno para **imprimir certificado** (`fnjs_imp_certificado`).

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/matriculas_lista_otras_r_data`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.matriculas_lista_otras_r`

## Objetivo

El usuario busca alumnos de otras regiones por apellido para consultar sus asignaturas matriculadas y emitir certificados E43. Solo visible en ámbito región STGR (`rstgr` o `r`).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
