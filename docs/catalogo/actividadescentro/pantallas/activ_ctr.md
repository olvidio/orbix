---
id: "actividadescentro.pantalla.activ_ctr"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadescentro"
nombre: "Activ Ctr"
controller: "frontend/actividadescentro/controller/activ_ctr.php"
vistas: ["frontend/actividadescentro/view/activ_ctr.phtml"]
fragmentos_frontend: []
endpoints: ["/src/actividadescentro/", "/src/actividadescentro/activ_ctr_shell_data"]
capacidades: ["actividadescentro.activ_ctr_shell.gestionar"]
campos: ["form.empiezamax", "form.empiezamin", "form.periodo", "form.tipo", "form.year", "post.periodo", "post.tipo", "post.year"]
acciones: ["fnjs_actualizar_activ", "fnjs_asignar_ctr", "fnjs_cambiar_ctr", "fnjs_cerrar", "fnjs_construir_celda_ctrs", "fnjs_construir_tabla_disponibles", "fnjs_construir_tabla_lista", "fnjs_eliminar", "fnjs_enviar", "fnjs_esc", "fnjs_left_side_hide", "fnjs_nuevo_ctr", "fnjs_parse_rta", "fnjs_reordenar", "fnjs_ver"]
estado_revision: "generado"
---

# Activ Ctr

Pantalla principal del modulo `actividadescentro`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadescentro/controller/activ_ctr.php`

## Vistas Relacionadas

- `frontend/actividadescentro/view/activ_ctr.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividadescentro/`
- `/src/actividadescentro/activ_ctr_shell_data`

## Capacidades Relacionadas

- `actividadescentro.activ_ctr_shell.gestionar`

## Campos Detectados

- `form.empiezamax`
- `form.empiezamin`
- `form.periodo`
- `form.tipo`
- `form.year`
- `post.periodo`
- `post.tipo`
- `post.year`

## Acciones Detectadas

- `fnjs_actualizar_activ`
- `fnjs_asignar_ctr`
- `fnjs_cambiar_ctr`
- `fnjs_cerrar`
- `fnjs_construir_celda_ctrs`
- `fnjs_construir_tabla_disponibles`
- `fnjs_construir_tabla_lista`
- `fnjs_eliminar`
- `fnjs_enviar`
- `fnjs_esc`
- `fnjs_left_side_hide`
- `fnjs_nuevo_ctr`
- `fnjs_parse_rta`
- `fnjs_reordenar`
- `fnjs_ver`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
