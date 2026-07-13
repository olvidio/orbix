---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Actividad Asignatura"
flujo: "actividadestudios.actividad_asignatura.gestionar.flujo"
preguntas: ["Como crear en Actividad Asignatura?", "Como eliminar en Actividad Asignatura?"]
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.form_asignaturas_de_una_actividad"]
endpoints: ["/src/actividadestudios/actividad_asignatura_eliminar", "/src/actividadestudios/actividad_asignatura_nueva"]
source: "docs/catalogo/actividadestudios/flujos/actividad_asignatura.md"
estado_revision: "generado"
---

# Ayuda IA - Actividad Asignatura

Usa este documento para responder preguntas de usuario sobre como trabajar con `Actividad Asignatura`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear en Actividad Asignatura?
- Como eliminar en Actividad Asignatura?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear

1. En el dossier 3005 de una actividad, pulsar **nuevo** para abrir el formulario de alta.
2. Elegir asignatura, profesor, fechas y tipo; pulsar **guardar**.
3. El sistema crea la `ActividadAsignatura` y abre el dossier 3005 de la actividad.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/actividad_asignatura_nueva`

## Eliminar

1. En el listado de asignaturas del dossier 3005, seleccionar una fila.
2. Pulsar **borrar** y confirmar.
3. El sistema elimina la asignatura impartida y refresca el listado.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/actividad_asignatura_eliminar`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.form_asignaturas_de_una_actividad`

## Objetivo

El usuario crea una nueva asignatura impartida en la actividad (profesor, fechas, tipo) o elimina una existente desde el dossier de asignaturas. Sustituye los cases `nuevo` y `eliminar` del antiguo `update_3005.php`.

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
