---
id: "casas.pantalla.casa_ingresos_lista"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "casas"
nombre: "Casa Ingresos Lista"
controller: "frontend/casas/controller/casa_ingresos_lista.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/casas/casa_ingresos_lista_data"]
capacidades: ["casas.casa_ingresos.gestionar"]
campos: ["post.empiezamax", "post.empiezamin", "post.id_cdc", "post.periodo", "post.year"]
acciones: []
estado_revision: "generado"
---

# Casa Ingresos Lista

Controlador AJAX HTML: listado económico de actividades por casa.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/casas/controller/casa_ingresos_lista.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/casas/casa_ingresos_lista_data`

## Capacidades Relacionadas

- `casas.casa_ingresos.gestionar`

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

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
