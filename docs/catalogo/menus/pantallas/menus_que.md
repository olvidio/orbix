---
id: "menus.pantalla.menus_que"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "menus"
nombre: "Menus Que"
controller: "frontend/menus/controller/menus_que.php"
vistas: ["frontend/menus/view/menus_que.phtml"]
fragmentos_frontend: ["frontend/menus/controller/menus_get.php"]
endpoints: ["/src/menus/grupmenu_lista"]
capacidades: ["menus.grupmenu.gestionar"]
campos: ["form.filtro_grupo", "post.filtro_grupo"]
acciones: ["fnjs_lista_menus"]
estado_revision: "revisado"
---

# Gestor de menús (seleccionar grupo)

Punto de entrada al gestor: desplegable de grupmenu y carga AJAX del listado/ficha en `#ficha`.

## Tipo

- Subtipo: `pantalla_principal`

## Ruta de menú

- **Legacy:** sistema > menus > seleccionar
- **Pills2:** ADMIN GLOBAL > menus > seleccionar

## Manual De Usuario

1. Abrir desde menú administración. 2. Elegir grupo de menú. 3. Gestionar ítems en el panel inferior.
