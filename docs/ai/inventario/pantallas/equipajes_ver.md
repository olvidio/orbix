---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "inventario"
titulo: "Gestionar equipajes"
pantalla: "inventario.pantalla.equipajes_ver"
preguntas: ["Que se puede hacer en Gestionar equipajes?", "Que campos tiene Gestionar equipajes?", "Que acciones hay en Gestionar equipajes?"]
capacidades: ["inventario.lista_equipajes_desde_fecha.gestionar"]
endpoints: ["/src/inventario/lista_equipajes_desde_fecha"]
source: "docs/catalogo/inventario/pantallas/equipajes_ver.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Gestionar equipajes

## Resumen

Lista equipajes desde fecha; modificar, eliminar o imprimir según parámetro URL.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

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

## Capacidades Relacionadas

- `inventario.lista_equipajes_desde_fecha.gestionar`

## Endpoints Relacionados

- `/src/inventario/lista_equipajes_desde_fecha`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
