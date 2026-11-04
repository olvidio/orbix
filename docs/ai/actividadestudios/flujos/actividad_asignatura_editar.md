---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Modificar una asignatura de la actividad"
flujo: "actividadestudios.actividad_asignatura_editar.gestionar.flujo"
preguntas: ["Como modificar una asignatura de la actividad en Modificar una asignatura de la actividad?"]
pantallas_principales: ["actividadestudios.pantalla.form_asignaturas_de_una_actividad"]
fragmentos: ["actividadestudios.pantalla.form_asignaturas_de_una_actividad"]
endpoints: ["/src/actividadestudios/actividad_asignatura_editar"]
source: "docs/catalogo/actividadestudios/flujos/actividad_asignatura_editar.md"
estado_revision: "generado"
---

# Ayuda IA - Modificar una asignatura de la actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Modificar una asignatura de la actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como modificar una asignatura de la actividad en Modificar una asignatura de la actividad?

## Donde Entrar

- Form Asignaturas De Una Actividad (`actividadestudios.pantalla.form_asignaturas_de_una_actividad`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Modificar una asignatura de la actividad

1. En el dossier de asignaturas, seleccionar la asignatura y pulsar **modificar**.
2. Ajustar el profesor, las fechas, el aviso al profesor o el tipo.
3. Pulsar **guardar**.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/actividad_asignatura_editar`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.form_asignaturas_de_una_actividad`
- `actividadestudios.pantalla.form_asignaturas_de_una_actividad`

## Objetivo

Cambiar el profesor, las fechas, el tipo u otros datos de una asignatura que ya se imparte en la actividad.

## Errores Documentados

- `faltan claves de la asignatura de actividad`
- `hay un error, no se ha guardado`
- `no encuentro la asignatura`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
