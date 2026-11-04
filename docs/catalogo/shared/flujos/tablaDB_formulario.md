---
id: "shared.tablaDB_formulario.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "shared"
nombre: "Flujo - Crear o modificar un registro"
capacidad: "shared.tablaDB_formulario.gestionar"
pantallas_principales: ["shared.pantalla.tablaDB_formulario_ver"]
fragmentos: ["shared.pantalla.tablaDB_formulario_ver"]
acciones: ["cargar", "guardar", "cancelar"]
endpoints: ["/src/shared/tablaDB_formulario_datos", "/src/shared/tablaDB_update"]
estado_revision: "revisado"
---

# Flujo - Formulario tabla genérica

## Objetivo De Usuario

Crear o modificar un registro en un mantenimiento genérico de tablas.

## Punto De Entrada

Desde la tabla del mantenimiento (botones **nuevo** o **modificar**) o desde un dossier.

## Escenarios Inferidos

### Crear o modificar un registro

Pasos propuestos:
1. En la tabla, pulsar **nuevo**, o seleccionar una fila y pulsar **modificar**.
2. Aparece el formulario con los campos del registro; en una modificación salen con sus valores.
3. Rellenar los campos; las fechas se comprueban antes de enviar.
4. Pulsar guardar: se vuelve a la tabla.

Endpoints asociados:
- `/src/shared/tablaDB_formulario_datos`
- `/src/shared/tablaDB_update`

## Errores Conocidos

- Mensajes de `tablaDB_update` en `alert` vía `json.mensaje`.

## Ruta de menú

sin entrada de menú en el índice.
