---
id: "cambios.pantalla.usuario_avisos_pref_propiedades"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "cambios"
nombre: "Tabla de propiedades"
controller: "frontend/cambios/controller/usuario_avisos_pref_propiedades.php"
vistas: ["frontend/cambios/view/usuario_avisos_pref_propiedades.phtml"]
fragmentos_frontend: []
endpoints: ["/src/cambios/cambio_usuario_objeto_pref_propiedades_data"]
capacidades: ["cambios.cambio_usuario_objeto_pref_propiedades.gestionar"]
campos: ["html.id_item_usuario_objeto_prop", "html.salida", "post.id_item_usuario_objeto", "post.objeto"]
acciones: ["fnjs_modificar", "fnjs_selectAll"]
estado_revision: "revisado"
---

# Tabla de propiedades

Fragmento AJAX con la tabla de propiedades vigilables del objeto seleccionado (checkboxes y enlace a
configurar condición).

## Endpoints Usados

- `/src/cambios/cambio_usuario_objeto_pref_propiedades_data`

## Acciones Detectadas

- `fnjs_modificar` — abre modal de condición
- `fnjs_selectAll` — marca/desmarca propiedades

## Ruta de menú

sin entrada de menú en el índice
