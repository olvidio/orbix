---
id: "casas.pantalla.casa_ec_gastos_lista"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "casas"
nombre: "Casa Ec Gastos Lista"
controller: "frontend/casas/controller/casa_ec_gastos_lista.php"
vistas: ["frontend/casas/view/casa_ec_gastos_lista.phtml"]
fragmentos_frontend: []
endpoints: ["/src/casas/casa_ec_gastos_form_data", "/src/casas/casa_ec_gastos_guardar"]
capacidades: ["casas.casa_ec_gastos.gestionar"]
campos: ["html.id_ubi", "html.year", "post.id_cdc", "post.year"]
acciones: ["fnjs_comprobar_dinero", "fnjs_gastos_guardar", "fnjs_ver"]
estado_revision: "revisado"
---

# Casa Ec Gastos Lista

Controlador AJAX HTML: formulario anual con gastos y aportaciones (sv/sf) por mes de una casa.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/casas/controller/casa_ec_gastos_lista.php`

## Vistas Relacionadas

- `frontend/casas/view/casa_ec_gastos_lista.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/casas/casa_ec_gastos_form_data`
- `/src/casas/casa_ec_gastos_guardar`

## Capacidades Relacionadas

- `casas.casa_ec_gastos.gestionar`

## Campos Detectados

- `html.id_ubi`
- `html.year`
- `post.id_cdc`
- `post.year`

## Acciones Detectadas

- `fnjs_comprobar_dinero`
- `fnjs_gastos_guardar`
- `fnjs_ver`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.
## Ruta de menú

- **Legacy:** exterior > casas > gastos casa
- **Pills2:** CASAS Y CTR > Gestión casas > gastos casas

