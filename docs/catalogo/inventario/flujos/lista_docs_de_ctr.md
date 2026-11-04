---
id: "inventario.lista_docs_de_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Buscar los documentos que se van a trasladar"
capacidad: "inventario.lista_docs_de_ctr.gestionar"
pantallas_principales: ["inventario.pantalla.traslado_doc_lista"]
fragmentos: ["inventario.pantalla.traslado_doc_lista"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/lista_docs_de_ctr"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Docs De Ctr

## Objetivo De Usuario

Ver los documentos de un centro (y lugar) para marcar cuáles se trasladan.

## Punto De Entrada

Dentro de **traslado de doc**, al pulsar **buscar**.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.traslado_doc_lista`

## Escenarios Inferidos

### Buscar los documentos que se van a trasladar

Pasos propuestos:
1. En **traslado de doc**, elegir centro y lugar de origen.
2. Pulsar **buscar**.
3. Aparece **lista de documentos del centro** con **documento** y **observaciones**.
4. Marcar los documentos a trasladar (**marcar todos** / **desmarcar todos** ayudan).

Endpoints asociados:
- `/src/inventario/lista_docs_de_ctr`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.id_lugar`
- `post.id_ubi`

Acciones JavaScript:
- `fnjs_selectAll`

## Endpoints Del Flujo

- `/src/inventario/lista_docs_de_ctr`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
