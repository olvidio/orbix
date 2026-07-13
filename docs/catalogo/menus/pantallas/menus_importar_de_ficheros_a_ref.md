---
id: "menus.pantalla.menus_importar_de_ficheros_a_ref"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "menus"
nombre: "Menus Importar De Ficheros A Ref"
controller: "frontend/menus/controller/menus_importar_de_ficheros_a_ref.php"
vistas: []
fragmentos_frontend: []
endpoints: []
capacidades: []
campos: ["get.seguro", "get.todos", "post.seguro", "post.todos"]
acciones: ["fnjs_update_div"]
estado_revision: "revisado"
---

# Restaurar menús por defecto (ref→DL)

Confirmación y ejecución masiva ref→aux por esquema(s). Controller HTTP en `src/menus/`.

## Tipo

- Subtipo: `pantalla`

## Ruta de menú

- **Legacy:** sistema > menus > importar desde ficheros
- **Pills2:** sin entrada de menú en el índice

## Manual De Usuario

Operación destructiva de mantenimiento; requiere confirmación en pantalla.
