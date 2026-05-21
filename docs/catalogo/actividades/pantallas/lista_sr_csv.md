---
id: "actividades.pantalla.lista_sr_csv"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividades"
nombre: "Lista Sr Csv"
controller: "frontend/actividades/controller/lista_sr_csv.php"
vistas: ["frontend/actividades/view/lista_sr_csv.phtml"]
fragmentos_frontend: ["frontend/actividades/controller/lista_sr_csv.php"]
endpoints: ["/src/actividades/lista_sr_csv_datos"]
capacidades: ["actividades.lista_sr_csv.gestionar"]
campos: ["post.c_activ", "post.dl_org", "post.empiezamax", "post.empiezamin", "post.id_cdc", "post.periodo", "post.que", "post.status", "post.year"]
acciones: []
estado_revision: "generado"
---

# Lista Sr Csv

Listado de actividades de SR para exportar como CSV o mostrar en pantalla.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividades/controller/lista_sr_csv.php`

## Vistas Relacionadas

- `frontend/actividades/view/lista_sr_csv.phtml`

## Fragmentos Frontend Relacionados

- `frontend/actividades/controller/lista_sr_csv.php`

## Endpoints Usados

- `/src/actividades/lista_sr_csv_datos`

## Capacidades Relacionadas

- `actividades.lista_sr_csv.gestionar`

## Campos Detectados

- `post.c_activ`
- `post.dl_org`
- `post.empiezamax`
- `post.empiezamin`
- `post.id_cdc`
- `post.periodo`
- `post.que`
- `post.status`
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
