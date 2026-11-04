---
id: "encargossacd.propuestas_lista.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Ver la lista de propuestas"
capacidad: ""
pantallas_principales: ["encargossacd.pantalla.propuestas_lista"]
fragmentos: ["encargossacd.pantalla.propuestas_ajax"]
acciones: ["obtener_lista", "asignar_sacd", "ver_info", "editar_dedicacion"]
endpoints: ["/src/encargossacd/opciones_seccion_data", "/src/encargossacd/propuestas_ajax"]
estado_revision: "revisado"
---

# Flujo - Propuestas Lista

Edición interactiva de propuestas staging por grupo de centros.

## Objetivo De Usuario

Filtrar por centro, ver los encargos propuestos y cambiar el sacd titular, suplente o colaborador, o su dedicación (mañanas, tardes, vespertino), antes de aprobar las propuestas. (Redactado desde el código; no contrastado con la pantalla.)

## Punto De Entrada

Desde el menú de propuestas, opción **modificar propuestas**.

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.propuestas_lista`
- `encargossacd.pantalla.propuestas_ajax`

## Escenarios Inferidos

### Ver la lista de propuestas

Pasos propuestos:
1. Abrir **modificar propuestas** desde el menú de propuestas.
2. Elegir la sección y, si se quiere, filtrar por centro; la lista se actualiza sola.
3. Si algo falla, sale un aviso con el mensaje.

Endpoints asociados:
- `/src/encargossacd/opciones_seccion_data`
- `/src/encargossacd/propuestas_ajax`

### Asignar o cambiar el sacd de un encargo

Pasos propuestos:
1. Pulsar el nombre del sacd de un encargo: se abre un desplegable.
2. Elegir el sacd. Si se deja un colaborador en blanco, se borra.
3. La fila se actualiza y el desplegable se cierra.

Endpoints asociados:
- `/src/encargossacd/opciones_seccion_data`
- `/src/encargossacd/propuestas_ajax`

### Ver el detalle o cambiar la dedicación

Pasos propuestos:
1. Pulsar la información o la dedicación de un encargo: se abre una ventana.
2. Para cambiar el horario, modificarlo y guardar; la lista se refresca.

Endpoints asociados:
- `/src/encargossacd/opciones_seccion_data`
- `/src/encargossacd/propuestas_ajax`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.filtro_ctr`
- `form.dedic_m` / `dedic_t` / `dedic_v`

Acciones JavaScript:
- `fnjs_lista_propuestas`, `fnjs_ver_sacd_posibles`, `fnjs_cmb_sacd`, `fnjs_info`,
  `fnjs_dedicacion`, `fnjs_guardar_horario`, `fnjs_cerrar_propuesta_popup`

## Endpoints Del Flujo

- `/src/encargossacd/opciones_seccion_data`
- `/src/encargossacd/propuestas_ajax`

## Errores Conocidos

- `Debe crear la tabla de propuestas`
- `No se puede guardar. Vuelva a cargar la vista`
- `Registro no encontrado`
- Alert cliente: «Primero debe introducir un sacd» (id_sacd == 1)

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
