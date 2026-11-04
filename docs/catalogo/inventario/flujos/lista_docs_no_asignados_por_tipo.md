---
id: "inventario.lista_docs_no_asignados_por_tipo.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Consultar los centros que no tienen el documento"
capacidad: "inventario.lista_docs_no_asignados_por_tipo.gestionar"
pantallas_principales: ["inventario.pantalla.doc_no_asignado"]
fragmentos: ["inventario.pantalla.doc_no_asignado"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/lista_docs_no_asignados_por_tipo"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Docs No Asignados Por Tipo

## Objetivo De Usuario

Ver qué centros todavía no tienen asignado un tipo de documento.

## Punto De Entrada

Desde **asignar documento**, tras elegir el tipo de documento y pulsar **lista de NO asignados**.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.doc_no_asignado`

## Escenarios Inferidos

### Consultar los centros que no tienen el documento

Pasos propuestos:
1. En **asignar documento**, elegir el tipo de documento.
2. Pulsar **lista de NO asignados**.
3. Leer el listado **Centros que NO tienen este documento**; sirve de punto de partida para asignarlo.

Endpoints asociados:
- `/src/inventario/lista_docs_no_asignados_por_tipo`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_tipo_doc`
- `post.id_tipo_doc`
- `post.inventario`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/inventario/lista_docs_no_asignados_por_tipo`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
