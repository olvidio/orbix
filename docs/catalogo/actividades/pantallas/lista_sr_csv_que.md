---
id: "actividades.pantalla.lista_sr_csv_que"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividades"
nombre: "Lista Sr Csv Que"
controller: "frontend/actividades/controller/lista_sr_csv_que.php"
vistas: []
fragmentos_frontend: ["frontend/actividades/controller/lista_sr_csv.php", "frontend/actividades/controller/lista_sr_csv_que.php"]
endpoints: ["/src/actividades/lista_sr_csv_que_datos"]
capacidades: ["actividades.lista_sr_csv_que.gestionar"]
campos: ["form.c_activ", "form.empiezamax", "form.empiezamin", "form.id_cdc_mas", "form.id_cdc_num", "form.periodo", "form.status", "form.year", "post.empiezamax", "post.empiezamin", "post.periodo", "post.year"]
acciones: []
estado_revision: "generado"
---

# Lista Sr Csv Que

Pantalla del formulario para listados particulares de SR.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividades/controller/lista_sr_csv_que.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

- `frontend/actividades/controller/lista_sr_csv.php`
- `frontend/actividades/controller/lista_sr_csv_que.php`

## Endpoints Usados

- `/src/actividades/lista_sr_csv_que_datos`

## Capacidades Relacionadas

- `actividades.lista_sr_csv_que.gestionar`

## Campos Detectados

- `form.c_activ`
- `form.empiezamax`
- `form.empiezamin`
- `form.id_cdc_mas`
- `form.id_cdc_num`
- `form.periodo`
- `form.status`
- `form.year`
- `post.empiezamax`
- `post.empiezamin`
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
