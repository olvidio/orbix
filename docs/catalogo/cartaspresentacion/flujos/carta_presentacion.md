---
id: "cartaspresentacion.carta_presentacion.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cartaspresentacion"
nombre: "Flujo - Gestionar Carta Presentacion"
capacidad: "cartaspresentacion.carta_presentacion.gestionar"
pantallas_principales: []
fragmentos: ["cartaspresentacion.pantalla.cartas_presentacion_form"]
acciones: ["crear_actualizar", "eliminar", "ver_formulario"]
endpoints: ["/src/cartaspresentacion/carta_presentacion_eliminar", "/src/cartaspresentacion/carta_presentacion_form_data", "/src/cartaspresentacion/carta_presentacion_update"]
estado_revision: "revisado"
---

# Flujo - Alta/edición/eliminación de una carta

Operaciones sobre una carta individual (par centro + dirección).

## Objetivo De Usuario

Dar de alta, modificar o quitar los datos de presentación de un centro concreto.

## Punto De Entrada

Desde el listado de la pantalla principal (`cartas_presentacion`): enlace **director** (formulario) o
**quitar** (eliminar). No tiene entrada de menú propia.

## Escenarios

### Ver formulario / alta

1. Pulsar **director** → `carta_presentacion_form_data` con `id_ubi` + `id_direccion`.
2. Si no hay carta, campos vacíos (alta al guardar). Si hay permiso denegado, mensaje de error.

### Crear / actualizar

1. Rellenar nombre, teléfono, e-mail, zona, observaciones.
2. **Guardar** → `carta_presentacion_update`.

### Eliminar

1. En fila con carta, pulsar **quitar** → confirmación → `carta_presentacion_eliminar`.

## Errores Conocidos

- `Faltan id_ubi o id_direccion`
- `No puede modificar datos de otra dl`
- `Carta de presentacion no encontrada`
- `Hay un error, no se ha guardado.` / `Hay un error, no se ha borrado.`

## Ruta de menú

sin entrada de menú en el índice (subflujo de `cartas_presentacion` > modificar).
