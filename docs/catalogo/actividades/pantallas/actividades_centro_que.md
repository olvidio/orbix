---
id: "actividades.pantalla.actividades_centro_que"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "actividades"
nombre: "Actividades Centro Que"
controller: "frontend/actividades/controller/actividades_centro_que.php"
vistas: ["frontend/actividades/view/actividades_centro_que.phtml"]
fragmentos_frontend: ["frontend/actividades/controller/calendario_listas.php", "frontend/actividades/controller/lista_centros_activ.php"]
endpoints: []
capacidades: []
campos: ["form.empiezamax", "form.empiezamin", "form.iactividad_val", "form.iasistentes_val", "form.id_ctr", "form.id_ctr_mas", "form.id_ctr_num", "form.periodo", "form.year", "post.empiezamax", "post.empiezamin", "post.periodo", "post.tipo_ctr", "post.tipo_lista", "post.ver_ctr", "post.year"]
acciones: ["fnjs_cerrar", "fnjs_guardar", "fnjs_mas_centros", "fnjs_modificar", "fnjs_update_div", "fnjs_ver"]
estado_revision: "generado"
---

# Actividades Centro Que

Formulario para escoger un centro (y un periodo) y lanzar un listado de actividades, datos economicos, cdc, etc.

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/actividades/controller/actividades_centro_que.php`

## Vistas Relacionadas

- `frontend/actividades/view/actividades_centro_que.phtml`

## Fragmentos Frontend Relacionados

- `frontend/actividades/controller/calendario_listas.php`
- `frontend/actividades/controller/lista_centros_activ.php`

## Endpoints Usados

No se han detectado endpoints `/src/...`.

## Capacidades Relacionadas

No se han detectado capacidades relacionadas.

## Campos Detectados

- `form.empiezamax`
- `form.empiezamin`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.id_ctr`
- `form.id_ctr_mas`
- `form.id_ctr_num`
- `form.periodo`
- `form.year`
- `post.empiezamax`
- `post.empiezamin`
- `post.periodo`
- `post.tipo_ctr`
- `post.tipo_lista`
- `post.ver_ctr`
- `post.year`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar`
- `fnjs_mas_centros`
- `fnjs_modificar`
- `fnjs_update_div`
- `fnjs_ver`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
