---
id: "casas.pantalla.casa_actividades_lista"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "casas"
nombre: "Casa Actividades Lista"
controller: "frontend/casas/controller/casa_actividades_lista.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/casas/casa_actividades_lista_data"]
capacidades: ["casas.casa_actividades.gestionar"]
campos: ["post.empiezamax", "post.empiezamin", "post.id_cdc", "post.periodo", "post.year"]
acciones: []
estado_revision: "revisado"
---

# Casa Actividades Lista

Controlador AJAX HTML: listado de actividades por casa y periodo (`tipo_lista=lista_activ`).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/casas/controller/casa_actividades_lista.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/casas/casa_actividades_lista_data`

## Capacidades Relacionadas

- `casas.casa_actividades.gestionar`

## Campos Detectados

- `post.empiezamax`
- `post.empiezamin`
- `post.id_cdc`
- `post.periodo`
- `post.year`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.
## Ruta de menú

- **Legacy:** exterior > casas > lista actividades
- **Pills2:** exterior > casas > lista actividades

