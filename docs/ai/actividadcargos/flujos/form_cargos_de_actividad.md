---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadcargos"
titulo: "Form Cargos De Actividad"
flujo: "actividadcargos.form_cargos_de_actividad.gestionar.flujo"
preguntas: ["Como obtener datos en Form Cargos De Actividad?"]
pantallas_principales: []
fragmentos: ["actividadcargos.pantalla.form_cargos_de_actividad"]
endpoints: ["/src/actividadcargos/form_cargos_de_actividad_data"]
source: "docs/catalogo/actividadcargos/flujos/form_cargos_de_actividad.md"
estado_revision: "generado"
---

# Ayuda IA - Form Cargos De Actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Form Cargos De Actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Form Cargos De Actividad?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. El usuario abre el formulario desde el widget de relación de cargos (modo `nuevo` o `editar`).
2. El controller POSTea a `form_cargos_de_actividad_data` con contexto del dossier (`pau`, `id_pau`, `obj_pau`, `sel`, `mod`, `permiso`).
3. El backend devuelve payload con desplegables (`personas_select`, `cargos_select`), valores (`observ`, `chk`), flags (`show_asis`, `id_nom_real`) y `hash_form_config`.
4. El front compone HTML de desplegables y hash; pinta el formulario en el bloque AJAX.
5. Si falta contexto válido, puede devolver `redir: go_atras` o `error`.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadcargos/form_cargos_de_actividad_data`

## Pantallas Y Fragmentos Relacionados

- `actividadcargos.pantalla.form_cargos_de_actividad`

## Objetivo

Asignar o editar el cargo de una persona en una actividad: el sistema carga desplegables, valores actuales, hash de campos y URLs de mutación antes de mostrar el formulario.

## Errores Documentados

- `no encuentro el cargo (edición con sel inválido)`
- `Mensajes HTML de persona no encontrada (No encuentro a nadie con id_nom: …)`
- `redir: go_atras cuando falta obj_pau en altas`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
