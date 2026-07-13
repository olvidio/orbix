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
estado_revision: "revisado"
---

# Role Grupmenu

Pantalla asignación grupmenu↔rol (añadir desde candidatos).

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

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
