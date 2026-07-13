---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Plan Estudios Ca"
flujo: "actividadestudios.plan_estudios_ca.gestionar.flujo"
preguntas: ["Como obtener datos en Plan Estudios Ca?"]
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.plan_estudios_ca"]
endpoints: ["/src/actividadestudios/plan_estudios_ca_data"]
source: "docs/catalogo/actividadestudios/flujos/plan_estudios_ca.md"
estado_revision: "generado"
---

# Ayuda IA - Plan Estudios Ca

Usa este documento para responder preguntas de usuario sobre como trabajar con `Plan Estudios Ca`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Plan Estudios Ca?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. En `actividad_select`, seleccionar una actividad CA.
2. Pulsar la acción **plan estudios**.
3. El sistema consulta `plan_estudios_ca_data` y muestra el informe completo.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/plan_estudios_ca_data`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.plan_estudios_ca`

## Objetivo

El usuario consulta el plan de estudios de una actividad CA: director de estudios, preceptores, profesores por asignatura y alumnos con sus asignaturas matriculadas y observaciones de plan (`observ_est`).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
