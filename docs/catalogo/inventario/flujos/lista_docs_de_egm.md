---
id: "inventario.lista_docs_de_egm.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Ver los documentos de una valija"
capacidad: "inventario.lista_docs_de_egm.gestionar"
pantallas_principales: ["inventario.pantalla.equipajes_form_del", "inventario.pantalla.equipajes_lista_docs"]
fragmentos: ["inventario.pantalla.equipajes_form_del", "inventario.pantalla.equipajes_lista_docs"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/lista_docs_de_egm"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Docs De Egm

## Objetivo De Usuario

Ver los documentos que lleva una valija, para quitar alguno.

## Punto De Entrada

Dentro de **hacer equipajes**, al pulsar **quitar documento** en una valija.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.equipajes_form_del`
- `inventario.pantalla.equipajes_lista_docs`

## Escenarios Inferidos

### Ver los documentos de una valija

Pasos propuestos:
1. En **hacer equipajes**, pulsar **quitar documento** en una valija.
2. Aparece la lista de documentos de esa valija (sigla e identificador).
3. Marcar los que se quitan.

Endpoints asociados:
- `/src/inventario/lista_docs_de_egm`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.sel`
- `post.id_equipaje`
- `post.id_grupo`
- `post.id_item_egm`
- `post.id_lugar`

Acciones JavaScript:
- `fnjs_cerrar`
- `fnjs_del_doc`
- `fnjs_eliminar_grupo`
- `fnjs_modificar_form_add`
- `fnjs_modificar_form_del`

## Endpoints Del Flujo

- `/src/inventario/lista_docs_de_egm`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
