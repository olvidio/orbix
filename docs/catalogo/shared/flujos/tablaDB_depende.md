---
id: "shared.tablaDB_depende.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "shared"
nombre: "Flujo - Cambiar un campo que condiciona otro"
capacidad: "shared.tablaDB_depende.gestionar"
pantallas_principales: ["shared.pantalla.tablaDB_formulario_ver"]
fragmentos: ["shared.pantalla.tablaDB_formulario_ver"]
acciones: ["actualizar_opciones"]
endpoints: ["/src/shared/tablaDB_depende_datos"]
estado_revision: "revisado"
---

# Flujo - Desplegable dependiente

## Objetivo De Usuario

Actualizar las opciones de un campo cuando cambia el valor de otro del que depende en un formulario de mantenimiento (por ejemplo, al cambiar el centro se actualizan los lugares en inventario).

## Punto De Entrada

Se activa solo al cambiar el desplegable «padre» de un formulario de mantenimiento de tabla; solo en los que tienen campos dependientes.

## Escenarios Inferidos

### Cambiar un campo que condiciona otro

Pasos propuestos:
1. En el formulario, cambiar el desplegable principal (por ejemplo el centro).
2. El desplegable dependiente (por ejemplo el lugar) se rellena con las opciones válidas para ese valor.

Endpoints asociados:
- `/src/shared/tablaDB_depende_datos`

## Errores Conocidos

- Error AJAX mostrado en `alert` con `json.mensaje`.

## Ruta de menú

sin entrada de menú en el índice (subflujo del formulario).
