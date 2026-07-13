---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadcargos"
titulo: "Form Cargos Personas En Actividad"
flujo: "actividadcargos.form_cargos_personas_en_actividad.gestionar.flujo"
preguntas: ["Como obtener datos en Form Cargos Personas En Actividad?"]
pantallas_principales: []
fragmentos: ["actividadcargos.pantalla.form_cargos_personas_en_actividad"]
endpoints: ["/src/actividadcargos/form_cargos_personas_en_actividad_data"]
source: "docs/catalogo/actividadcargos/flujos/form_cargos_personas_en_actividad.md"
estado_revision: "generado"
---

# Ayuda IA - Form Cargos Personas En Actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Form Cargos Personas En Actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Form Cargos Personas En Actividad?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. El usuario abre el formulario desde el widget de relación de cargos de la persona.
2. El controller POSTea a `form_cargos_personas_en_actividad_data` con `id_pau` (persona), `sel`, `mod`, `que_dl`, `id_tipo` según el enlace de alta.
3. En modo `editar`, carga datos del `ActividadCargo` y fija actividad en solo lectura.
4. En modo `nuevo`, filtra actividades por tipo y delegación (`que_dl` vacío = otras delegaciones).
5. El front pinta desplegables y hash; el usuario completa y guarda vía `cargo_nuevo`/`cargo_editar`.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadcargos/form_cargos_personas_en_actividad_data`

## Pantallas Y Fragmentos Relacionados

- `actividadcargos.pantalla.form_cargos_personas_en_actividad`

## Objetivo

Gestionar los cargos de una persona en distintas actividades: el sistema carga el listado de actividades candidatas (en altas), valores del cargo en edición y URLs de mutación.

## Errores Documentados

- `no encuentro el cargo (edición)`
- `actividad no encontrada`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
