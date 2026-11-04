---
id: "cartaspresentacion.cartas_presentacion_shell.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cartaspresentacion"
nombre: "Flujo - Buscar los centros"
capacidad: "cartaspresentacion.cartas_presentacion_shell.gestionar"
pantallas_principales: ["cartaspresentacion.pantalla.cartas_presentacion"]
fragmentos: []
acciones: ["obtener_datos", "buscar_centros", "modificar_carta", "eliminar_carta"]
endpoints: ["/src/cartaspresentacion/cartas_presentacion_shell_data", "/src/cartaspresentacion/ubis_lista_data", "/src/cartaspresentacion/poblaciones_data", "/src/cartaspresentacion/carta_presentacion_form_data", "/src/cartaspresentacion/carta_presentacion_update", "/src/cartaspresentacion/carta_presentacion_eliminar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Cartas Presentacion (pantalla principal)

Gestión de cartas de presentación desde la pantalla **modificar**: buscar centros, crear/editar y
eliminar cartas.

## Objetivo De Usuario

Mantener los datos de presentación (director, contacto, zona) de los centros de la delegación o de regiones extranjeras.

## Punto De Entrada

Menú **scdl > direcciones > cartas presentacion > modificar**.

## Escenarios Inferidos

### Buscar los centros

Pasos propuestos:
1. Abrir **modificar**: sale el formulario de selección.
2. Elegir «según dl» y, si procede, la población (con «según r» no hay población).
3. Pulsar **buscar**: debajo aparece la tabla de centros.

Endpoints asociados:
- `/src/cartaspresentacion/cartas_presentacion_shell_data`
- `/src/cartaspresentacion/ubis_lista_data`
- `/src/cartaspresentacion/poblaciones_data`
- `/src/cartaspresentacion/carta_presentacion_form_data`
- `/src/cartaspresentacion/carta_presentacion_update`
- `/src/cartaspresentacion/carta_presentacion_eliminar`

### Modificar la carta de un centro

Pasos propuestos:
1. Pulsar **director** en la fila del centro: se abre una ventana.
2. Rellenar los campos y pulsar **guardar**; la lista se actualiza.

Endpoints asociados:
- `/src/cartaspresentacion/cartas_presentacion_shell_data`
- `/src/cartaspresentacion/ubis_lista_data`
- `/src/cartaspresentacion/poblaciones_data`
- `/src/cartaspresentacion/carta_presentacion_form_data`
- `/src/cartaspresentacion/carta_presentacion_update`
- `/src/cartaspresentacion/carta_presentacion_eliminar`

### Quitar una carta

Pasos propuestos:
1. En las filas con carta, pulsar **quitar** y confirmar.

Endpoints asociados:
- `/src/cartaspresentacion/cartas_presentacion_shell_data`
- `/src/cartaspresentacion/ubis_lista_data`
- `/src/cartaspresentacion/poblaciones_data`
- `/src/cartaspresentacion/carta_presentacion_form_data`
- `/src/cartaspresentacion/carta_presentacion_update`
- `/src/cartaspresentacion/carta_presentacion_eliminar`

## Endpoints Del Flujo

- `/src/cartaspresentacion/cartas_presentacion_shell_data`
- `/src/cartaspresentacion/ubis_lista_data`
- `/src/cartaspresentacion/poblaciones_data`
- `/src/cartaspresentacion/carta_presentacion_form_data`
- `/src/cartaspresentacion/carta_presentacion_update`
- `/src/cartaspresentacion/carta_presentacion_eliminar`

## Errores Conocidos

- Formulario: `No puede modificar datos de otra dl`, `Centro no encontrado`.
- Update: `Hay un error, no se ha guardado.`
- Eliminar: `Carta de presentacion no encontrada`, `Hay un error, no se ha borrado.`

## Ruta de menú

- **Legacy:** scdl > direcciones > cartas presentacion > modificar
- **Pills2:** scdl > direcciones > cartas presentacion > modificar
