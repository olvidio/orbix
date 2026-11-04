---
id: "inventario.lista_docs_asignar_dlb.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Rellenar la asignación a la dlb/casa"
capacidad: "inventario.lista_docs_asignar_dlb.gestionar"
pantallas_principales: ["inventario.pantalla.doc_asignar_dlb"]
fragmentos: ["inventario.pantalla.doc_asignar_dlb"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/lista_docs_asignar_dlb"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Docs Asignar Dlb

## Objetivo De Usuario

Indicar, para cada lugar de la dlb/casa elegido, cuántos ejemplares (o qué número de registro) recibe de un documento.

## Punto De Entrada

Se abre al pulsar **Asignar** en la lista de lugares de **asignar documento** (opción dlb/casa).

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.doc_asignar_dlb`

## Escenarios Inferidos

### Rellenar la asignación a la dlb/casa

Pasos propuestos:
1. Revisar el nombre del documento que aparece como título.
2. Escribir en cada **delegación** el **número de registro** (documentos numerados) o el **número de ejemplares**.
3. Indicar las fechas **recibido** y **asignado** si procede.
4. Pulsar **guardar**.

Endpoints asociados:
- `/src/inventario/lista_docs_asignar_dlb`

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

- `/src/inventario/lista_docs_asignar_dlb`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
