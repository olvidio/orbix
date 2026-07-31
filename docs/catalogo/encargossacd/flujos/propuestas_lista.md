---
id: "encargossacd.propuestas_lista.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Propuestas Lista"
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

Filtrar centros, ver encargos propuestos y cambiar SACD titular/suplente/colaborador o
dedicación m/t/v antes de aprobar.

## Punto De Entrada

Desde hub propuestas → «modificar propuestas» (sin entrada de menú propia).


## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.propuestas_lista`
- `encargossacd.pantalla.propuestas_ajax`

## Escenarios Inferidos

### Obtener lista

1. Carga opciones de sección (`opciones_seccion_data`).
2. Al cambiar `filtro_ctr` o al recargar: `que=get_lista` vía FE ajax.
3. Inyecta HTML en `#lista` o alert del mensaje de error.

### Asignar / cambiar SACD

1. Click en nombre → `lista_sacd` (desplegable).
2. Cambio en desplegable → `cmb_sacd` (alta/update/borrar colaborador vacío).
3. Actualiza fila o cierra popup.

### Info / dedicación

1. `info` o `dedicacion` abren popup HTML.
2. Guardar horario: `dedicacion_update` y refresca lista.

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
