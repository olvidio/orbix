---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadcargos"
titulo: "Añadir o modificar un cargo en una actividad"
flujo: "actividadcargos.form_cargos_de_actividad.gestionar.flujo"
preguntas: ["Como añadir o modificar un cargo en una actividad en Añadir o modificar un cargo en una actividad?", "Como ver los cargos de una actividad en Añadir o modificar un cargo en una actividad?"]
pantallas_principales: ["actividadcargos.pantalla.form_cargos_de_actividad"]
fragmentos: ["actividadcargos.pantalla.form_cargos_de_actividad"]
endpoints: ["/src/actividadcargos/form_cargos_de_actividad_data"]
source: "docs/catalogo/actividadcargos/flujos/form_cargos_de_actividad.md"
estado_revision: "generado"
---

# Ayuda IA - Añadir o modificar un cargo en una actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Añadir o modificar un cargo en una actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como añadir o modificar un cargo en una actividad en Añadir o modificar un cargo en una actividad?
- Como ver los cargos de una actividad en Añadir o modificar un cargo en una actividad?

## Donde Entrar

- Form Cargos De Actividad (`actividadcargos.pantalla.form_cargos_de_actividad`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Añadir o modificar un cargo en una actividad

1. Pulsar **añadir n** / **añadir agd** en «Relación de cargos», o marcar a un asistente y pulsar **añadir cargo** / **modificar cargo**.
2. Se abre «Cargo de una actividad» con el nombre del asistente y los campos **Cargo** (desplegable, por ejemplo «d»), **¿Puede ser agd?** y **Observaciones**.
3. Pulsar **Guardar datos del cargo**, o **Cancelar** para salir sin guardar.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadcargos/form_cargos_de_actividad_data`

## Ver los cargos de una actividad

1. Marcar la actividad en la lista y pulsar **cargos**.
2. Se abre «Relación de cargos» con el enlace **( dossiers )**; sin cargos indica «no hay ninguna fila».
3. Los botones **añadir n** y **añadir agd** abren el formulario de cargo.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadcargos/form_cargos_de_actividad_data`

## Pantallas Y Fragmentos Relacionados

- `actividadcargos.pantalla.form_cargos_de_actividad`
- `actividadcargos.pantalla.form_cargos_de_actividad`

## Objetivo

Añadir o modificar un cargo de una persona en una actividad.

## Errores Documentados

- `no encuentro el cargo (edición con sel inválido)`
- `Mensajes HTML de persona no encontrada (No encuentro a nadie con id_nom: …)`
- `redir: go_atras cuando falta obj_pau en altas`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
