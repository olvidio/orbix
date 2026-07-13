---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Form Asignaturas De Una Actividad"
flujo: "actividadestudios.form_asignaturas_de_una_actividad.gestionar.flujo"
preguntas: ["Como obtener datos en Form Asignaturas De Una Actividad?"]
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.form_asignaturas_de_una_actividad"]
endpoints: ["/src/actividadestudios/form_asignaturas_de_una_actividad_data"]
source: "docs/catalogo/actividadestudios/flujos/form_asignaturas_de_una_actividad.md"
estado_revision: "generado"
---

# Ayuda IA - Form Asignaturas De Una Actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Form Asignaturas De Una Actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Form Asignaturas De Una Actividad?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. En el dossier 3005, pulsar **nuevo** o **modificar** sobre una asignatura.
2. El sistema carga el formulario consultando `form_asignaturas_de_una_actividad_data`.
3. Se muestran desplegables, fechas y botón guardar con hash de seguridad.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/form_asignaturas_de_una_actividad_data`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.form_asignaturas_de_una_actividad`

## Objetivo

El usuario abre el formulario para crear o editar una asignatura impartida en una actividad CA: el sistema devuelve desplegables de asignaturas y profesores, fechas, flags de aviso y permisos según el modo (nuevo/editar).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
