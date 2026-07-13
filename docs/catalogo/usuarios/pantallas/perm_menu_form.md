---
id: "usuarios.pantalla.perm_menu_form"
tipo: "pantalla_frontend"
subtipo: "modal"
modulo: "usuarios"
nombre: "Perm Menu Form"
controller: "frontend/usuarios/controller/perm_menu_form.php"
vistas: ["frontend/usuarios/view/perm_menu_form.phtml"]
fragmentos_frontend: []
endpoints: ["/src/usuarios/perm_menu_guardar", "/src/usuarios/perm_menu_info"]
capacidades: ["usuarios.perm_menu.gestionar", "usuarios.perm_menu_info.gestionar"]
campos: ["form.menu_perm", "post.id_item", "post.id_usuario", "post.sel"]
acciones: ["fnjs_grabar"]
estado_revision: "revisado"
---

# Perm Menu Form

Modal permiso menú DL (bits oficina/grupo).

## Tipo

- Subtipo: `modal`


- Controller: `frontend/usuarios/controller/perm_menu_form.php`

## Vistas Relacionadas

- `frontend/usuarios/view/perm_menu_form.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/usuarios/perm_menu_guardar`
- `/src/usuarios/perm_menu_info`

## Capacidades Relacionadas

- `usuarios.perm_menu.gestionar`
- `usuarios.perm_menu_info.gestionar`

## Campos Detectados

- `form.menu_perm`
- `post.id_item`
- `post.id_usuario`
- `post.sel`

## Acciones Detectadas

- `fnjs_grabar`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
