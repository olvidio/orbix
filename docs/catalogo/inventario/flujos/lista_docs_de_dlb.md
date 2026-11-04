---
id: "inventario.lista_docs_de_dlb.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Elegir lugares para asignar un documento"
capacidad: "inventario.lista_docs_de_dlb.gestionar"
pantallas_principales: ["inventario.pantalla.doc_de_dlb"]
fragmentos: ["inventario.pantalla.doc_de_dlb"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/lista_docs_de_dlb"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Docs De Dlb

## Objetivo De Usuario

Elegir los centros-lugar de la dlb/casa a los que se asignará un documento (o de los que se va a imprimir el inventario).

## Punto De Entrada

Desde **asignar documento** (botón **Asignar a dlb/casa (especificar lugar)**) o desde **de centros o dlb** (botón **inventario de dlb/casa**).

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.doc_de_dlb`

## Escenarios Inferidos

### Elegir lugares para asignar un documento

Pasos propuestos:
1. En **asignar documento**, elegir el tipo de documento y pulsar **Asignar a dlb/casa (especificar lugar)**.
2. Marcar los **centro - lugar** de la lista.
3. Pulsar **Asignar** para pasar a indicar número y fechas.

Endpoints asociados:
- `/src/inventario/lista_docs_de_dlb`

### Elegir lugares para imprimir su inventario

Pasos propuestos:
1. Abrir **de centros o dlb** y pulsar **inventario de dlb/casa**.
2. Marcar los lugares que interesan.
3. Pulsar **Imprimir** (o **Imprimir dl** para incluir las observaciones de la delegación).

Endpoints asociados:
- `/src/inventario/lista_docs_de_dlb`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.sel`
- `post.id_tipo_doc`
- `post.inventario`

Acciones JavaScript:
- `fnjs_go`
- `fnjs_selectAll`

## Endpoints Del Flujo

- `/src/inventario/lista_docs_de_dlb`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
