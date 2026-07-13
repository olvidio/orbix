---
id: "inventario.pantalla.equipajes_ver"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "inventario"
nombre: "Gestionar equipajes"
controller: "frontend/inventario/controller/equipajes_ver.php"
vistas: ["frontend/inventario/view/equipajes_ver.phtml"]
fragmentos_frontend: ["frontend/inventario/controller/equipajes_desplegable.php", "frontend/inventario/controller/equipajes_doc_casa.php", "frontend/inventario/controller/equipajes_docs_libres.php", "frontend/inventario/controller/equipajes_form_add.php", "frontend/inventario/controller/equipajes_form_del.php", "frontend/inventario/controller/equipajes_form_texto_listado.php", "frontend/inventario/controller/equipajes_imprimir.php", "frontend/inventario/controller/equipajes_lista_docs.php", "frontend/inventario/controller/equipajes_posibles_maletas.php", "frontend/inventario/controller/equipajes_ver.php", "frontend/inventario/controller/equipajes_ver_docs.php"]
endpoints: ["/src/inventario/lista_equipajes_desde_fecha"]
capacidades: ["inventario.lista_equipajes_desde_fecha.gestionar"]
campos: ["form.filtro", "form.id_equipaje", "form.loc", "form.texto", "post.eliminar", "post.filtro", "post.imprimir"]
acciones: ["fnjs_actualizar_lista_equipaje", "fnjs_add_doc", "fnjs_cerrar", "fnjs_del_doc", "fnjs_docs_libres", "fnjs_eliminar_equipaje", "fnjs_eliminar_grupo", "fnjs_guardar_listado", "fnjs_lista_docs", "fnjs_mod_texto_equipaje", "fnjs_modificar_form_add", "fnjs_modificar_form_del", "fnjs_nuevo_grupo", "fnjs_update_div", "fnjs_update_grupo", "fnjs_ver_1", "fnjs_ver_2", "fnjs_ver_docs"]
estado_revision: "revisado"
---

# Gestionar equipajes

Lista equipajes desde fecha; modificar, eliminar o imprimir según parámetro URL.


## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/inventario/controller/equipajes_ver.php`

## Vistas Relacionadas

- `frontend/inventario/view/equipajes_ver.phtml`

## Fragmentos Frontend Relacionados

- `frontend/inventario/controller/equipajes_desplegable.php`
- `frontend/inventario/controller/equipajes_doc_casa.php`
- `frontend/inventario/controller/equipajes_docs_libres.php`
- `frontend/inventario/controller/equipajes_form_add.php`
- `frontend/inventario/controller/equipajes_form_del.php`
- `frontend/inventario/controller/equipajes_form_texto_listado.php`
- `frontend/inventario/controller/equipajes_imprimir.php`
- `frontend/inventario/controller/equipajes_lista_docs.php`
- `frontend/inventario/controller/equipajes_posibles_maletas.php`
- `frontend/inventario/controller/equipajes_ver.php`
- `frontend/inventario/controller/equipajes_ver_docs.php`

## Endpoints Usados

- `/src/inventario/lista_equipajes_desde_fecha`

## Capacidades Relacionadas

- `inventario.lista_equipajes_desde_fecha.gestionar`

## Campos Detectados

- `form.filtro`
- `form.id_equipaje`
- `form.loc`
- `form.texto`
- `post.eliminar`
- `post.filtro`
- `post.imprimir`

## Acciones Detectadas

- `fnjs_actualizar_lista_equipaje`
- `fnjs_add_doc`
- `fnjs_cerrar`
- `fnjs_del_doc`
- `fnjs_docs_libres`
- `fnjs_eliminar_equipaje`
- `fnjs_eliminar_grupo`
- `fnjs_guardar_listado`
- `fnjs_lista_docs`
- `fnjs_mod_texto_equipaje`
- `fnjs_modificar_form_add`
- `fnjs_modificar_form_del`
- `fnjs_nuevo_grupo`
- `fnjs_update_div`
- `fnjs_update_grupo`
- `fnjs_ver_1`
- `fnjs_ver_2`
- `fnjs_ver_docs`

## Manual De Usuario

Ver [`manual/inventario.md`](../../../manual/inventario.md). Lista equipajes desde fecha; modificar, eliminar o imprimir según parámetro URL.

## Ruta de menú

- **Legacy:** scdl > Inventario > equipajes > hacer/eliminar/imprimir equipajes
- **Pills2:** —
