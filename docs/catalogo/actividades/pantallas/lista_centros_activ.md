---
id: "actividades.pantalla.lista_centros_activ"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividades"
nombre: "Lista Centros Activ"
controller: "frontend/actividades/controller/lista_centros_activ.php"
vistas: []
fragmentos_frontend: ["frontend/actividades/controller/lista_centros_activ.php"]
endpoints: ["/src/actividades/lista_centros_activ_datos"]
capacidades: ["actividades.lista_centros_activ.gestionar"]
campos: ["post.empiezamax", "post.empiezamin", "post.id_ctr", "post.id_ctr_num", "post.periodo", "post.year"]
acciones: []
estado_revision: "generado"
---

# Lista Centros Activ

Fragmento HTML con la lista de centros y sus actividades en un periodo.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividades/controller/lista_centros_activ.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

- `frontend/actividades/controller/lista_centros_activ.php`

## Endpoints Usados

- `/src/actividades/lista_centros_activ_datos`

## Capacidades Relacionadas

- `actividades.lista_centros_activ.gestionar`

## Campos Detectados

- `post.empiezamax`
- `post.empiezamin`
- `post.id_ctr`
- `post.id_ctr_num`
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
