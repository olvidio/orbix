---
id: "configuracion.modulos_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "configuracion"
nombre: "Flujo - Consultar los módulos definidos"
capacidad: "configuracion.modulos_select.gestionar"
pantallas_principales: ["configuracion.pantalla.modulos_select"]
fragmentos: ["configuracion.pantalla.modulos_form", "configuracion.pantalla.modulos_update"]
acciones: ["listar", "nuevo", "modificar", "eliminar"]
endpoints: ["/src/configuracion/modulos_select_data", "/src/configuracion/modulos_update"]
estado_revision: "revisado"
---

# Flujo - Definir módulos (listado)

## Objetivo De Usuario

Ver los módulos definidos en el esquema y acceder al alta, la modificación o la baja de cada uno.

## Punto De Entrada

Menú **sistema > Configuración > definir módulos**.

## Escenarios Inferidos

### Consultar los módulos definidos

Pasos propuestos:
1. Abrir **definir módulos**: aparece la tabla de módulos con sus dependencias.

Endpoints asociados:
- `/src/configuracion/modulos_select_data`
- `/src/configuracion/modulos_update`

### Añadir un módulo

Pasos propuestos:
1. Pulsar **añadir módulo** y seguir los pasos de «Añadir o modificar un módulo».

Endpoints asociados:
- `/src/configuracion/modulos_select_data`
- `/src/configuracion/modulos_update`

### Modificar un módulo

Pasos propuestos:
1. Seleccionar una sola fila.
2. Pulsar **modificar**.

Endpoints asociados:
- `/src/configuracion/modulos_select_data`
- `/src/configuracion/modulos_update`

### Eliminar un módulo

Pasos propuestos:
1. Seleccionar la fila y pulsar el botón de eliminar.
2. Confirmar.
3. La lista se refresca; si no se puede borrar, sale un aviso con el motivo.

Endpoints asociados:
- `/src/configuracion/modulos_select_data`
- `/src/configuracion/modulos_update`

## Errores Conocidos

- `hay un error, no se ha eliminado` (+ texto de `getErrorTxt()` del repositorio)

## Ruta de menú

- **Legacy:** sistema > Configuración > definir módulos
- **Pills2:** sistema > Configuración > definir módulos; ADMIN GLOBAL > Configuración > definir módulos
