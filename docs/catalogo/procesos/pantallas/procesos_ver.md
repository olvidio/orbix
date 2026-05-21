---
id: "procesos.pantalla.procesos_ver"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "procesos"
nombre: "Procesos Ver"
controller: "frontend/procesos/controller/procesos_ver.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/procesos/procesos_depende", "/src/procesos/procesos_update", "/src/procesos/procesos_ver_data"]
capacidades: ["procesos.procesos.gestionar", "procesos.procesos_depende.gestionar", "procesos.procesos_ver.gestionar"]
campos: ["form.acc", "form.dep_num", "form.id_fase", "form.id_fase_previa", "form.id_of_responsable", "form.id_tarea", "form.id_tarea_previa", "form.mensaje_requisito", "form.status", "form.valor_depende", "post.id_item", "post.id_tipo_proceso", "post.mod"]
acciones: ["fnjs_get_depende"]
estado_revision: "generado"
---

# Procesos Ver

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/procesos/controller/procesos_ver.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/procesos/procesos_depende`
- `/src/procesos/procesos_update`
- `/src/procesos/procesos_ver_data`

## Capacidades Relacionadas

- `procesos.procesos.gestionar`
- `procesos.procesos_depende.gestionar`
- `procesos.procesos_ver.gestionar`

## Campos Detectados

- `form.acc`
- `form.dep_num`
- `form.id_fase`
- `form.id_fase_previa`
- `form.id_of_responsable`
- `form.id_tarea`
- `form.id_tarea_previa`
- `form.mensaje_requisito`
- `form.status`
- `form.valor_depende`
- `post.id_item`
- `post.id_tipo_proceso`
- `post.mod`

## Acciones Detectadas

- `fnjs_get_depende`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
