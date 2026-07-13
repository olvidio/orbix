---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "Actividad Asignatura Editar"
flujo: "actividadestudios.actividad_asignatura_editar.gestionar.flujo"
preguntas: ["Como ejecutar en Actividad Asignatura Editar?"]
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.form_asignaturas_de_una_actividad"]
endpoints: ["/src/actividadestudios/actividad_asignatura_editar"]
source: "docs/catalogo/actividadestudios/flujos/actividad_asignatura_editar.md"
estado_revision: "generado"
---

# Ayuda IA - Actividad Asignatura Editar

Usa este documento para responder preguntas de usuario sobre como trabajar con `Actividad Asignatura Editar`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Actividad Asignatura Editar?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. En el dossier 3005, seleccionar una asignatura impartida y pulsar **modificar**.
2. Ajustar profesor, fechas, aviso a profesor o tipo en el formulario.
3. Pulsar **guardar**; el sistema persiste los cambios en la `ActividadAsignatura`.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/actividad_asignatura_editar`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.form_asignaturas_de_una_actividad`

## Objetivo

El usuario modifica profesor, fechas, tipo u otros datos de una asignatura ya impartida en la actividad y guarda los cambios. Sustituye el case `editar` del antiguo `update_3005.php`.

## Errores Documentados

- `faltan claves de la asignatura de actividad`
- `hay un error, no se ha guardado`
- `no encuentro la asignatura`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
