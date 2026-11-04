---
id: "cartaspresentacion.carta_presentacion.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cartaspresentacion"
nombre: "Flujo - Dar de alta o modificar la carta de un centro"
capacidad: "cartaspresentacion.carta_presentacion.gestionar"
pantallas_principales: ["cartaspresentacion.pantalla.cartas_presentacion"]
fragmentos: ["cartaspresentacion.pantalla.cartas_presentacion_form"]
acciones: ["crear_actualizar", "eliminar", "ver_formulario"]
endpoints: ["/src/cartaspresentacion/carta_presentacion_eliminar", "/src/cartaspresentacion/carta_presentacion_form_data", "/src/cartaspresentacion/carta_presentacion_update"]
estado_revision: "revisado"
---

# Flujo - Alta/edición/eliminación de una carta

Operaciones sobre una carta individual (par centro + dirección).

## Objetivo De Usuario

Dar de alta, modificar o quitar los datos de presentación de un centro.

## Punto De Entrada

Desde la lista de la pantalla de cartas de presentación (opción **modificar**): enlace **director** para abrir el formulario o **quitar** para eliminar. No tiene entrada de menú propia.

## Escenarios Inferidos

### Dar de alta o modificar la carta de un centro

Pasos propuestos:
1. En la lista, pulsar **director** en la fila del centro.
2. Se abre el formulario; si el centro no tiene carta, sale vacío y se crea al guardar. No se pueden modificar datos de otra delegación.
3. Rellenar nombre, teléfono, e-mail, zona y observaciones.
4. Pulsar **Guardar**.

Endpoints asociados:
- `/src/cartaspresentacion/carta_presentacion_eliminar`
- `/src/cartaspresentacion/carta_presentacion_form_data`
- `/src/cartaspresentacion/carta_presentacion_update`

### Quitar la carta de un centro

Pasos propuestos:
1. En una fila que ya tiene carta, pulsar **quitar**.
2. Confirmar el aviso.

Endpoints asociados:
- `/src/cartaspresentacion/carta_presentacion_eliminar`
- `/src/cartaspresentacion/carta_presentacion_form_data`
- `/src/cartaspresentacion/carta_presentacion_update`

## Errores Conocidos

- `Faltan id_ubi o id_direccion`
- `No puede modificar datos de otra dl`
- `Carta de presentacion no encontrada`
- `Hay un error, no se ha guardado.` / `Hay un error, no se ha borrado.`

## Ruta de menú

sin entrada de menú en el índice (subflujo de `cartas_presentacion` > modificar).
