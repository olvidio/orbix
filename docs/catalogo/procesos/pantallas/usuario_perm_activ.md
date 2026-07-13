---
id: "procesos.pantalla.usuario_perm_activ"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "procesos"
nombre: "Usuario Perm Activ"
controller: "frontend/procesos/controller/usuario_perm_activ.php"
vistas: ["frontend/procesos/view/usuario_perm_activ.html.twig"]
fragmentos_frontend: []
endpoints: ["/src/procesos/usuario_perm_activ_ajax", "/src/procesos/usuario_perm_activ_data", "/src/usuarios/perm_activ_guardar"]
capacidades: ["procesos.usuario_perm_activ.gestionar", "procesos.usuario_perm_activ_ajax.gestionar"]
campos: ["form.dl_propia", "form.extendida", "form.fase_ref", "form.iactividad_val", "form.iasistentes_val", "form.id_tipo_activ", "form.inom_tipo_val", "form.isfsv_val", "form.perm_off", "form.perm_on", "post.dl_propia", "post.id_tipo_activ_txt", "post.id_usuario", "post.que", "post.quien", "post.sel"]
acciones: []
estado_revision: "revisado"
---

# Usuario Perm Activ

Alta o edición de permisos de actividad para un usuario: selector de tipo de actividad (DL propia/otras), ámbitos afectados, fase de referencia y permisos on/off por fila.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/procesos/controller/usuario_perm_activ.php`

## Vistas Relacionadas

- `frontend/procesos/view/usuario_perm_activ.html.twig`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/procesos/usuario_perm_activ_ajax`
- `/src/procesos/usuario_perm_activ_data`
- `/src/usuarios/perm_activ_guardar`

## Capacidades Relacionadas

- `procesos.usuario_perm_activ.gestionar`
- `procesos.usuario_perm_activ_ajax.gestionar`

## Campos Detectados

- `form.dl_propia`
- `form.extendida`
- `form.fase_ref`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.id_tipo_activ`
- `form.inom_tipo_val`
- `form.isfsv_val`
- `form.perm_off`
- `form.perm_on`
- `post.dl_propia`
- `post.id_tipo_activ_txt`
- `post.id_usuario`
- `post.que`
- `post.quien`
- `post.sel`

## Acciones Detectadas

No se han detectado acciones.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
