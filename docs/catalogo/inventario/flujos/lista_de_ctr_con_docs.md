---
id: "inventario.lista_de_ctr_con_docs.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Elegir centros para asignar un documento"
capacidad: "inventario.lista_de_ctr_con_docs.gestionar"
pantallas_principales: ["inventario.pantalla.doc_de_ctr"]
fragmentos: ["inventario.pantalla.doc_de_ctr"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/lista_de_ctr_con_docs"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista De Ctr Con Docs

## Objetivo De Usuario

Elegir los centros a los que se asignará un documento (o de los que se va a imprimir el inventario).

## Punto De Entrada

Desde **asignar documento** (botón **Asignar a centros (sin especificar lugar)**) o desde **Inventario > inventarios > de centros o dlb** (botón **inventario de centros**).

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.doc_de_ctr`

## Escenarios Inferidos

### Elegir centros para asignar un documento

Pasos propuestos:
1. En **asignar documento**, elegir el tipo de documento y pulsar **Asignar a centros (sin especificar lugar)**.
2. Marcar los centros en la lista (**marcar todos**, **desmarcar todos** y **marcar/desmarcar todos** ayudan con listas largas).
3. Pulsar **Asignar** para pasar a indicar número y fechas.

Endpoints asociados:
- `/src/inventario/lista_de_ctr_con_docs`

### Elegir centros para imprimir su inventario

Pasos propuestos:
1. Abrir **de centros o dlb** y pulsar **inventario de centros**.
2. Marcar los centros que interesan.
3. Pulsar **Imprimir ctr** (inventario para el centro) o **Imprimir dl** (inventario con las observaciones de la delegación).

Endpoints asociados:
- `/src/inventario/lista_de_ctr_con_docs`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.sel`
- `post.id_tipo_doc`
- `post.inventario`

Acciones JavaScript:
- `fnjs_go`
- `fnjs_selectAll`

## Endpoints Del Flujo

- `/src/inventario/lista_de_ctr_con_docs`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/AJAX/dossier)
