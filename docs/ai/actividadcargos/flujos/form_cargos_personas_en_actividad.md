---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadcargos"
titulo: "Abrir el formulario de cargo de una persona"
flujo: "actividadcargos.form_cargos_personas_en_actividad.gestionar.flujo"
preguntas: ["Como abrir el formulario de cargo de una persona en Abrir el formulario de cargo de una persona?"]
pantallas_principales: ["actividadcargos.pantalla.form_cargos_personas_en_actividad"]
fragmentos: ["actividadcargos.pantalla.form_cargos_personas_en_actividad"]
endpoints: ["/src/actividadcargos/form_cargos_personas_en_actividad_data"]
source: "docs/catalogo/actividadcargos/flujos/form_cargos_personas_en_actividad.md"
estado_revision: "generado"
---

# Ayuda IA - Abrir el formulario de cargo de una persona

Usa este documento para responder preguntas de usuario sobre como trabajar con `Abrir el formulario de cargo de una persona`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como abrir el formulario de cargo de una persona en Abrir el formulario de cargo de una persona?

## Donde Entrar

- Form Cargos Personas En Actividad (`actividadcargos.pantalla.form_cargos_personas_en_actividad`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Abrir el formulario de cargo de una persona

1. Desde la relación de cargos de la persona, pulsar **añadir cargo de la dl**, **añadir cargo de otra dl** o seleccionar un cargo y pulsar **modificar cargo**.
2. Al modificar, aparecen los datos del cargo y la actividad no se puede cambiar.
3. Al añadir, el desplegable de actividades solo ofrece las del tipo elegido y de la propia dl (o de otras delegaciones, según el enlace).
4. Completar el formulario y guardar.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadcargos/form_cargos_personas_en_actividad_data`

## Pantallas Y Fragmentos Relacionados

- `actividadcargos.pantalla.form_cargos_personas_en_actividad`
- `actividadcargos.pantalla.form_cargos_personas_en_actividad`

## Objetivo

Abrir el formulario para dar de alta o modificar un cargo de una persona en una actividad, con las actividades candidatas en las altas.

## Errores Documentados

- `no encuentro el cargo (edición)`
- `actividad no encontrada`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
