---
id: "inventario.lista_docs_de_lugar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Gestionar Lista Docs De Lugar"
capacidad: "inventario.lista_docs_de_lugar.gestionar"
pantallas_principales: []
fragmentos: ["inventario.pantalla.equipajes_lista_docs", "inventario.pantalla.equipajes_ver_docs"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/lista_docs_de_lugar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Docs De Lugar

Propuesta generada automaticamente desde la capacidad `inventario.lista_docs_de_lugar.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ListaDocsDeLugar. Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.equipajes_lista_docs`
- `inventario.pantalla.equipajes_ver_docs`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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
