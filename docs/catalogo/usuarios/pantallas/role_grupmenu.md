---
id: "usuarios.pantalla.role_grupmenu"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "usuarios"
nombre: "Role Grupmenu"
controller: "frontend/usuarios/controller/role_grupmenu.php"
vistas: ["frontend/usuarios/view/role_grupmenu.phtml"]
fragmentos_frontend: []
endpoints: ["/src/usuarios/role_grupmenu_add", "/src/usuarios/role_grupmenu_info"]
capacidades: ["usuarios.role_grupmenu_add.gestionar", "usuarios.role_grupmenu_info.gestionar"]
campos: ["form.sel", "post.id_role", "post.sel"]
acciones: ["fnjs_add_grupmenu"]
estado_revision: "generado"
---

# Role Grupmenu

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/usuarios/controller/role_grupmenu.php`

## Vistas Relacionadas

- `frontend/usuarios/view/role_grupmenu.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/usuarios/role_grupmenu_add`
- `/src/usuarios/role_grupmenu_info`

## Capacidades Relacionadas

- `usuarios.role_grupmenu_add.gestionar`
- `usuarios.role_grupmenu_info.gestionar`

## Campos Detectados

- `form.sel`
- `post.id_role`
- `post.sel`

## Acciones Detectadas

- `fnjs_add_grupmenu`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
