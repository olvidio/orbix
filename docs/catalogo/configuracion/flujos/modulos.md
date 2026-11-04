---
id: "configuracion.modulos.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "configuracion"
nombre: "Flujo - Añadir o modificar un módulo"
capacidad: "configuracion.modulos.gestionar"
pantallas_principales: ["configuracion.pantalla.modulos_form", "configuracion.pantalla.modulos_update"]
fragmentos: ["configuracion.pantalla.modulos_form", "configuracion.pantalla.modulos_update"]
acciones: ["ver_formulario", "crear_actualizar", "eliminar"]
endpoints: ["/src/configuracion/modulos_form_data", "/src/configuracion/modulos_update"]
estado_revision: "revisado"
---

# Flujo - Gestionar módulo (ficha)

## Objetivo De Usuario

Dar de alta un módulo nuevo, o cambiar el nombre, la descripción y las dependencias (módulos y aplicaciones que necesita) de uno existente.

## Punto De Entrada

Se abre desde la lista de **definir módulos**: botón **añadir módulo** o **modificar**.

## Escenarios Inferidos

### Añadir o modificar un módulo

Pasos propuestos:
1. En la lista de módulos, pulsar **añadir módulo**, o seleccionar uno y pulsar **modificar**.
2. Aparece el formulario con los módulos y aplicaciones disponibles. Las aplicaciones que necesitan otros módulos salen marcadas y no se pueden desmarcar.
3. Rellenar o cambiar el nombre, la descripción y las dependencias (al marcar o desmarcar una casilla, el cambio se guarda al momento).
4. Pulsar **Guardar cambios** para volver a la lista.

Endpoints asociados:
- `/src/configuracion/modulos_form_data`
- `/src/configuracion/modulos_update`

### Eliminar un módulo

Pasos propuestos:
1. Se hace desde la lista de módulos, no desde el formulario (ver «Consultar los módulos definidos»).

Endpoints asociados:
- `/src/configuracion/modulos_form_data`
- `/src/configuracion/modulos_update`

## Errores Conocidos

- `hay un error, no se ha eliminado` (solo en baja desde listado)

## Ruta de menú

Sin entrada de menú en el índice (subflujo de «definir módulos»).
