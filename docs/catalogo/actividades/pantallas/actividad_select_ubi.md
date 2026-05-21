---
id: "actividades.pantalla.actividad_select_ubi"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "actividades"
nombre: "Actividad Select Ubi"
controller: "frontend/actividades/controller/actividad_select_ubi.php"
vistas: ["frontend/actividades/view/actividad_select_ubi.phtml"]
fragmentos_frontend: ["frontend/ubis/controller/ubis_lista.php"]
endpoints: ["/src/actividades/actividad_select_ubi_desplegable", "/src/actividades/actividad_tipo_get"]
capacidades: ["actividades.actividad_select_ubi_desplegable.gestionar", "actividades.actividad_tipo.gestionar"]
campos: ["form.dl_org", "form.entrada", "form.extendida", "form.filtro_lugar", "form.frm_4_nombre_ubi", "form.id_ubi_1", "form.isfsv", "form.lst_lugar", "form.modo", "form.nombre_ubi", "form.salida", "form.tipo", "html.b_buscar"]
acciones: ["fnjs_buscar", "fnjs_cargar_desplegable", "fnjs_construir_desplegable", "fnjs_enviar_form", "fnjs_lugar"]
estado_revision: "generado"
---

# Actividad Select Ubi

Controlador frontend de la pantalla "seleccionar lugar para una actividad".

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/actividades/controller/actividad_select_ubi.php`

## Vistas Relacionadas

- `frontend/actividades/view/actividad_select_ubi.phtml`

## Fragmentos Frontend Relacionados

- `frontend/ubis/controller/ubis_lista.php`

## Endpoints Usados

- `/src/actividades/actividad_select_ubi_desplegable`
- `/src/actividades/actividad_tipo_get`

## Capacidades Relacionadas

- `actividades.actividad_select_ubi_desplegable.gestionar`
- `actividades.actividad_tipo.gestionar`

## Campos Detectados

- `form.dl_org`
- `form.entrada`
- `form.extendida`
- `form.filtro_lugar`
- `form.frm_4_nombre_ubi`
- `form.id_ubi_1`
- `form.isfsv`
- `form.lst_lugar`
- `form.modo`
- `form.nombre_ubi`
- `form.salida`
- `form.tipo`
- `html.b_buscar`

## Acciones Detectadas

- `fnjs_buscar`
- `fnjs_cargar_desplegable`
- `fnjs_construir_desplegable`
- `fnjs_enviar_form`
- `fnjs_lugar`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
