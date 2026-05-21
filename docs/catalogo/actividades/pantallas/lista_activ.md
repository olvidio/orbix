---
id: "actividades.pantalla.lista_activ"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividades"
nombre: "Lista Activ"
controller: "frontend/actividades/controller/lista_activ.php"
vistas: ["frontend/actividades/view/lista_activ.phtml"]
fragmentos_frontend: []
endpoints: ["/src/actividades/lista_activ_datos"]
capacidades: ["actividades.lista_activ.gestionar"]
campos: ["post.Gstack", "post.asist", "post.c_activ", "post.continuar", "post.dl_org", "post.empiezamax", "post.empiezamin", "post.filtro_lugar", "post.id_tipo_activ", "post.id_ubi", "post.periodo", "post.que", "post.sactividad", "post.sasistentes", "post.seccion", "post.snom_tipo", "post.ssfsv", "post.stack", "post.status", "post.titulo", "post.year"]
acciones: []
estado_revision: "generado"
---

# Lista Activ

Pantalla que muestra el listado de actividades filtradas.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividades/controller/lista_activ.php`

## Vistas Relacionadas

- `frontend/actividades/view/lista_activ.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividades/lista_activ_datos`

## Capacidades Relacionadas

- `actividades.lista_activ.gestionar`

## Campos Detectados

- `post.Gstack`
- `post.asist`
- `post.c_activ`
- `post.continuar`
- `post.dl_org`
- `post.empiezamax`
- `post.empiezamin`
- `post.filtro_lugar`
- `post.id_tipo_activ`
- `post.id_ubi`
- `post.periodo`
- `post.que`
- `post.sactividad`
- `post.sasistentes`
- `post.seccion`
- `post.snom_tipo`
- `post.ssfsv`
- `post.stack`
- `post.status`
- `post.titulo`
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
