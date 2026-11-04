---
id: "inventario.inventario_que.elegir.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Elegir inventario de centros o de dlb/casa"
capacidad: "inventario.inventario_que.elegir"
pantallas_principales: ["inventario.pantalla.inventario_que"]
fragmentos: ["inventario.pantalla.doc_de_ctr", "inventario.pantalla.doc_de_dlb"]
acciones: ["elegir"]
endpoints: []
estado_revision: "revisado"
---

# Flujo - Elegir inventario de centros o de dlb/casa

Puerta de entrada al inventario de documentos.

## Objetivo De Usuario

Decidir si se va a trabajar con el inventario de **centros** o con el de la **dlb/casa** (la dlb y las casas pueden tener varios lugares, y se inventaría lo que hay en cada lugar), y pasar a la pantalla correspondiente (asignar, modificar o imprimir documentos).

## Punto De Entrada

Menú **inventarios > de centros o dlb**. La pantalla solo tiene dos botones y no guarda ni consulta nada por sí misma.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.doc_de_ctr`: inventario por centros (selección de tipo de documento y de centros; enlaces a asignar, modificar e imprimir).
- `inventario.pantalla.doc_de_dlb`: inventario de la dlb/casa (consulta por tipo de documento; asignar, modificar e imprimir).

## Escenarios Inferidos

### Trabajar con el inventario de centros

Pasos propuestos:
1. Abrir **de centros o dlb** desde el menú.
2. Pulsar **inventario de centros**.
3. En la pantalla que se abre, elegir el tipo de documento y los centros, y usar los enlaces para asignar, modificar o imprimir.

Endpoints asociados:
- Ninguno directo (la pantalla siguiente usa `/src/inventario/lista_de_ctr_con_docs`).

### Trabajar con el inventario de la dlb/casa

Pasos propuestos:
1. Abrir **de centros o dlb** desde el menú.
2. Pulsar **inventario de dlb/casa**.
3. En la pantalla que se abre, elegir el tipo de documento y usar los enlaces para asignar, modificar o imprimir.

Endpoints asociados:
- Ninguno directo (la pantalla siguiente usa `/src/inventario/lista_docs_de_dlb`).

## Campos Y Acciones Detectadas En Pantalla

Acciones JavaScript:
- `fnjs_go`, `fnjs_enviar_formulario`

## Errores Conocidos

La pantalla no genera errores propios.

## Ruta de menú

- **Legacy:** scdl > Inventario > inventarios > de centros o dlb
- **Pills2:** sin entrada en el índice de menús (falta en el layout nuevo; pendiente de añadir).
