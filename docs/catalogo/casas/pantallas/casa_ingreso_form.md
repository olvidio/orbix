---
id: "casas.pantalla.casa_ingreso_form"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "casas"
nombre: "Casa Ingreso Form"
controller: "frontend/casas/controller/casa_ingreso_form.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/casas/casa_ingreso_form_data"]
capacidades: ["casas.casa_ingreso.gestionar"]
campos: ["get.id_activ", "html.id_activ", "html.ingresos", "html.num_asistentes", "html.observ", "html.precio", "post.id_activ"]
acciones: ["fnjs_cerrar", "fnjs_guardar"]
estado_revision: "revisado"
---

# Casa Ingreso Form

Controlador AJAX HTML: formulario modal del ingreso de una actividad (edición).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/casas/controller/casa_ingreso_form.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/casas/casa_ingreso_form_data`

## Capacidades Relacionadas

- `casas.casa_ingreso.gestionar`

## Campos Detectados

- `get.id_activ`
- `html.id_activ`
- `html.ingresos`
- `html.num_asistentes`
- `html.observ`
- `html.precio`
- `post.id_activ`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.
## Ruta de menú

- **Legacy:** exterior > casas > Gestión económica
- **Pills2:** CASAS Y CTR > Gestión casas > Gestión económica

