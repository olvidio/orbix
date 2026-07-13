---
id: "shared.tablaDB_depende.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "shared"
nombre: "Flujo - Desplegable dependiente"
capacidad: "shared.tablaDB_depende.gestionar"
pantallas_principales: []
fragmentos: ["shared.pantalla.tablaDB_formulario_ver"]
acciones: ["actualizar_opciones"]
endpoints: ["/src/shared/tablaDB_depende_datos"]
estado_revision: "revisado"
---

# Flujo - Desplegable dependiente

## Objetivo De Usuario

Actualizar las opciones de un campo hijo cuando cambia el valor del campo padre en un formulario
`tablaDB` (p. ej. centro → lugar en inventario).

## Punto De Entrada

`onchange` en `<select>` padre → `fnjs_actualizar_depende(camp, accion)` en `tablaDB_formulario.phtml`.

## Escenarios

### Actualizar opciones

1. Leer valor del padre (`valor_depende`).
2. POST a `tablaDB_depende_datos` con `clase_info`, `accion` (id hijo) y hash.
3. Sustituir HTML del `<select>` hijo con `data.aOpciones`.

Solo aplica en `Info*` que implementan `getArrayCamposDepende` / `getOpcionesParaCondicion`.

## Errores Conocidos

- Error AJAX mostrado en `alert` con `json.mensaje`.

## Ruta de menú

sin entrada de menú en el índice (subflujo del formulario).
