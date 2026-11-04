---
id: "inventario.lista_docs_asignar_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Rellenar la asignación a centros"
capacidad: "inventario.lista_docs_asignar_ctr.gestionar"
pantallas_principales: ["inventario.pantalla.doc_asignar_ctr"]
fragmentos: ["inventario.pantalla.doc_asignar_ctr"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/lista_docs_asignar_ctr"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Docs Asignar Ctr

## Objetivo De Usuario

Indicar, para cada centro elegido, cuántos ejemplares (o qué número de registro) recibe de un documento.

## Punto De Entrada

Se abre al pulsar **Asignar** en la lista de centros de **asignar documento**.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.doc_asignar_ctr`

## Escenarios Inferidos

### Rellenar la asignación a centros

Pasos propuestos:
1. Revisar el nombre del documento que aparece como título.
2. Escribir en cada centro el **número de registro** (documentos numerados) o el **número de ejemplares**.
3. Indicar las fechas **recibido** y **asignado** (formato dd/mm/aa) si procede.
4. Pulsar **guardar**.

Endpoints asociados:
- `/src/inventario/lista_docs_asignar_ctr`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.f_asignado`
- `html.f_recibido`
- `html.okay`
- `post.id_tipo_doc`
- `post.sel`

Acciones JavaScript:
- `fnjs_guardar`

## Endpoints Del Flujo

- `/src/inventario/lista_docs_asignar_ctr`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
