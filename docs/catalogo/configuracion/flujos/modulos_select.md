---
id: "configuracion.modulos_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "configuracion"
nombre: "Flujo - Definir módulos (listado)"
capacidad: "configuracion.modulos_select.gestionar"
pantallas_principales: ["configuracion.pantalla.modulos_select"]
fragmentos: ["configuracion.pantalla.modulos_form", "configuracion.pantalla.modulos_update"]
acciones: ["listar", "nuevo", "modificar", "eliminar"]
endpoints: ["/src/configuracion/modulos_select_data", "/src/configuracion/modulos_update"]
estado_revision: "revisado"
---

# Flujo - Definir módulos (listado)

## Objetivo De Usuario

Consultar los módulos definidos en el esquema y acceder a alta, edición o baja de cada uno.

## Punto De Entrada

`frontend/configuracion/controller/modulos_select.php`

## Escenarios

### Listar módulos

1. Abrir desde menú Configuración > definir módulos.
2. La pantalla carga `modulos_select_data` y muestra tabla con dependencias.
3. Opcional: restaurar selección/scroll vía `ListNavSupport`.

### Alta

1. Pulsar «añadir módulo» → `modulos_form.php` (`mod=nuevo`).
2. Continuar en flujo «Gestionar módulos (ficha)».

### Modificar

1. Seleccionar exactamente una fila (`fnjs_solo_uno`).
2. Botón «modificar» → `modulos_form.php` con `sel[]`.

### Eliminar

1. Seleccionar una fila y confirmar eliminación.
2. POST AJAX a `modulos_update` (`mod=eliminar`).
3. Refresco del listado; error si el repositorio no puede borrar.

## Errores Conocidos

- `hay un error, no se ha eliminado` (+ texto de `getErrorTxt()` del repositorio)

## Ruta de menú

- **Legacy:** sistema > Configuración > definir módulos
- **Pills2:** sistema > Configuración > definir módulos; ADMIN GLOBAL > Configuración > definir módulos
