---
id: "cartaspresentacion.cartas_presentacion_shell.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cartaspresentacion"
nombre: "Flujo - Gestionar Cartas Presentacion Shell"
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

Mantener los datos de presentación (director, contacto, zona) de los centros de la delegación o de
regiones extranjeras.

## Punto De Entrada

Pantalla `cartas_presentacion` (`frontend/cartaspresentacion/controller/cartas_presentacion.php`):
menú scdl > direcciones > cartas presentacion > modificar.

## Escenarios

### Inicializar pantalla

1. Al cargar, el controller llama a `cartas_presentacion_shell_data` y firma las URLs AJAX.
2. Se muestra el formulario de selección (dl/regiones + población).

### Buscar centros

1. Elegir «según dl» y población (si aplica).
2. Pulsar **buscar** → `ubis_lista_data` pinta la tabla en `#ficha2`.

### Modificar carta

1. Pulsar **director** en una fila → modal con `carta_presentacion_form_data`.
2. Rellenar campos y **guardar** → `carta_presentacion_update`; se refresca el listado.

### Eliminar carta

1. En filas con carta existente, pulsar **quitar** → confirmación → `carta_presentacion_eliminar`.

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
