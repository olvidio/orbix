---
id: "cartaspresentacion.cartas_presentacion_shell.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cartaspresentacion"
nombre: "Flujo - Gestionar Cartas Presentacion Shell"
capacidad: "cartaspresentacion.cartas_presentacion_shell.gestionar"
pantallas_principales: []
fragmentos: ["cartaspresentacion.pantalla.cartas_presentacion"]
acciones: ["obtener_datos"]
endpoints: ["/src/cartaspresentacion/cartas_presentacion_shell_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Cartas Presentacion Shell

Propuesta generada automaticamente desde la capacidad `cartaspresentacion.cartas_presentacion_shell.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona CartasPresentacionShell. Datos para la shell cartas_presentacion.php: delegación y paths relativos. URLs absolutas y fragment Hash: {.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `cartaspresentacion.pantalla.cartas_presentacion`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.buscar`

Acciones JavaScript:
- `fnjs_cerrar`
- `fnjs_construir_desplegable`
- `fnjs_eliminar_cp`
- `fnjs_guardar_cp`
- `fnjs_left_side_hide`
- `fnjs_modificar`
- `fnjs_poblacion`
- `fnjs_update_div`
- `fnjs_ver`
- `fnjs_ver_ubi`

## Endpoints Del Flujo

- `/src/cartaspresentacion/cartas_presentacion_shell_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
