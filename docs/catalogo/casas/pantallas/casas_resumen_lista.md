---
id: "casas.pantalla.casas_resumen_lista"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "casas"
nombre: "Casas Resumen Lista"
controller: "frontend/casas/controller/casas_resumen_lista.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/casas/casas_resumen_data"]
capacidades: ["casas.casas_resumen.gestionar"]
campos: ["post.cdc_sel", "post.empiezamax", "post.empiezamin", "post.id_cdc", "post.periodo", "post.que", "post.year"]
acciones: []
estado_revision: "generado"
---

# Casas Resumen Lista

Controlador AJAX HTML: resumen económico de casas (modo periodo y modo anual 5 años).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/casas/controller/casas_resumen_lista.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/casas/casas_resumen_data`

## Capacidades Relacionadas

- `casas.casas_resumen.gestionar`

## Campos Detectados

- `post.cdc_sel`
- `post.empiezamax`
- `post.empiezamin`
- `post.id_cdc`
- `post.periodo`
- `post.que`
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
