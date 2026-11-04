---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Añadir una asignatura a la actividad"
flujo: "actividadestudios.actividad_asignatura.gestionar.flujo"
preguntas: ["Como añadir una asignatura a la actividad en Añadir una asignatura a la actividad?", "Como eliminar una asignatura de la actividad en Añadir una asignatura a la actividad?"]
pantallas_principales: ["actividadestudios.pantalla.form_asignaturas_de_una_actividad"]
fragmentos: ["actividadestudios.pantalla.form_asignaturas_de_una_actividad"]
endpoints: ["/src/actividadestudios/actividad_asignatura_eliminar", "/src/actividadestudios/actividad_asignatura_nueva"]
source: "docs/catalogo/actividadestudios/flujos/actividad_asignatura.md"
estado_revision: "generado"
---

# Ayuda IA - Añadir una asignatura a la actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Añadir una asignatura a la actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como añadir una asignatura a la actividad en Añadir una asignatura a la actividad?
- Como eliminar una asignatura de la actividad en Añadir una asignatura a la actividad?

## Donde Entrar

- Form Asignaturas De Una Actividad (`actividadestudios.pantalla.form_asignaturas_de_una_actividad`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Añadir una asignatura a la actividad

1. En el dossier de asignaturas de la actividad, pulsar **nuevo**.
2. Elegir la asignatura, el profesor, las fechas y el tipo, y pulsar **guardar**.
3. Se vuelve al dossier de asignaturas con la nueva asignatura en la lista.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/actividad_asignatura_eliminar`
- `/src/actividadestudios/actividad_asignatura_nueva`

## Eliminar una asignatura de la actividad

1. En la lista de asignaturas del dossier, seleccionar la fila.
2. Pulsar **borrar** y confirmar.
3. La asignatura desaparece de la lista.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/actividad_asignatura_eliminar`
- `/src/actividadestudios/actividad_asignatura_nueva`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.form_asignaturas_de_una_actividad`
- `actividadestudios.pantalla.form_asignaturas_de_una_actividad`

## Objetivo

Añadir una asignatura impartida en la actividad (con su profesor, fechas y tipo) o eliminar una que ya está, desde el dossier de asignaturas.

## Errores Documentados

- `faltan claves de la asignatura de actividad`
- `hay un error, no se ha borrado`
- `hay un error, no se ha creado`
- `no encuentro la asignatura`
- `sólo se puede eliminar una asignatura desde el dossier de la actividad`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
