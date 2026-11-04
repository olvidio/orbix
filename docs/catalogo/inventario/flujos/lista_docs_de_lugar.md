---
id: "inventario.lista_docs_de_lugar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Ver los documentos de la casa"
capacidad: "inventario.lista_docs_de_lugar.gestionar"
pantallas_principales: ["inventario.pantalla.equipajes_lista_docs", "inventario.pantalla.equipajes_ver_docs"]
fragmentos: ["inventario.pantalla.equipajes_lista_docs", "inventario.pantalla.equipajes_ver_docs"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/lista_docs_de_lugar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Docs De Lugar

## Objetivo De Usuario

Ver los documentos que tiene la casa (el lugar) para repartirlos en valijas.

## Punto De Entrada

Dentro de **hacer equipajes**, al montar una valija.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.equipajes_lista_docs`
- `inventario.pantalla.equipajes_ver_docs`

## Escenarios Inferidos

### Ver los documentos de la casa

Pasos propuestos:
1. En **hacer equipajes**, elegir el equipaje y abrir una valija.
2. Se muestra la lista de documentos de la casa con **sigla**, **identificador** y **num reg**.
3. Marcar los que van en la valija y pulsar **crear valija**.

Endpoints asociados:
- `/src/inventario/lista_docs_de_lugar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.sel`
- `post.id_equipaje`
- `post.id_grupo`
- `post.id_item_egm`
- `post.id_lugar`
- `post.nom_grupo`

Acciones JavaScript:
- `fnjs_eliminar_grupo`
- `fnjs_modificar_form_add`
- `fnjs_modificar_form_del`
- `fnjs_update_grupo`

## Endpoints Del Flujo

- `/src/inventario/lista_docs_de_lugar`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
