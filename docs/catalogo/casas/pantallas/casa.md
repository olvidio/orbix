---
id: "casas.pantalla.casa"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "casas"
nombre: "Casa"
controller: "frontend/casas/controller/casa.php"
vistas: ["frontend/casas/view/casa.phtml"]
fragmentos_frontend: ["frontend/actividades/controller/calendario_listas.php", "frontend/casas/controller/casa_actividades_lista.php", "frontend/casas/controller/casa_ec_gastos_lista.php", "frontend/casas/controller/casa_ingreso_form.php", "frontend/casas/controller/casa_ingresos_lista.php"]
endpoints: ["/src/casas/casa_ingreso_eliminar", "/src/casas/casa_ingreso_update"]
capacidades: ["casas.casa_ingreso.gestionar"]
campos: ["form.id_activ", "form.id_tarifa", "form.ingresos", "form.num_asistentes", "form.observ", "form.precio", "html.buscar", "post.id_ubi", "post.periodo", "post.tipo_lista", "post.ver_ctr", "post.year"]
acciones: ["fnjs_cerrar", "fnjs_guardar", "fnjs_mas_casas", "fnjs_modificar", "fnjs_update_div", "fnjs_ver"]
estado_revision: "generado"
---

# Casa

Pantalla principal del módulo `casas` — filtro casa + periodo y delegación a distintas vistas AJAX según `tipo_lista`: - default               → listado económico (`casa_ingresos_lista.php`) - `lista_activ`         → listado de actividades (`casa_actividades_lista.php`) - `ctrsEncargados`      → `frontend/actividades/controller/calendario_listas.php` - `datosEcGastos`       → `frontend/casas/controller/casa_ec_gastos_lista.php` Migrada desde `apps/casas/controller/casa_que.php` + `casa_ajax.php` siguiendo `refactor.md`.

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/casas/controller/casa.php`

## Vistas Relacionadas

- `frontend/casas/view/casa.phtml`

## Fragmentos Frontend Relacionados

- `frontend/actividades/controller/calendario_listas.php`
- `frontend/casas/controller/casa_actividades_lista.php`
- `frontend/casas/controller/casa_ec_gastos_lista.php`
- `frontend/casas/controller/casa_ingreso_form.php`
- `frontend/casas/controller/casa_ingresos_lista.php`

## Endpoints Usados

- `/src/casas/casa_ingreso_eliminar`
- `/src/casas/casa_ingreso_update`

## Capacidades Relacionadas

- `casas.casa_ingreso.gestionar`

## Campos Detectados

- `form.id_activ`
- `form.id_tarifa`
- `form.ingresos`
- `form.num_asistentes`
- `form.observ`
- `form.precio`
- `html.buscar`
- `post.id_ubi`
- `post.periodo`
- `post.tipo_lista`
- `post.ver_ctr`
- `post.year`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar`
- `fnjs_mas_casas`
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
