---
id: "configuracion.modulos.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "configuracion"
nombre: "Flujo - Gestionar módulo (ficha)"
capacidad: "configuracion.modulos.gestionar"
pantallas_principales: []
fragmentos: ["configuracion.pantalla.modulos_form", "configuracion.pantalla.modulos_update"]
acciones: ["ver_formulario", "crear_actualizar", "eliminar"]
endpoints: ["/src/configuracion/modulos_form_data", "/src/configuracion/modulos_update"]
estado_revision: "revisado"
---

# Flujo - Gestionar módulo (ficha)

## Objetivo De Usuario

Dar de alta un módulo nuevo o editar nombre, descripción y dependencias (módulos/apps
requeridos) de uno existente.

## Punto De Entrada

`frontend/configuracion/controller/modulos_form.php` (desde listado «definir módulos»).

## Escenarios

### Ver formulario

1. Desde listado: «añadir módulo» (`mod=nuevo`) o «modificar» (fila seleccionada).
2. `modulos_form_data` devuelve hashes, catálogo de módulos/apps y valores actuales.
3. Apps requeridas por módulos dependientes se muestran checked+disabled.

### Crear o actualizar

1. Editar campos; cambios en checkboxes guardan al vuelo (`fnjs_cambio`).
2. «Guardar cambios» → `modulos_update` (alta si `mod=nuevo` y `nom` no vacío; edición por `id_mod`).
3. Volver al listado con `navAtras`.

### Eliminar

Gestión desde el listado (`modulos_select`), no desde la ficha.

## Errores Conocidos

- `hay un error, no se ha eliminado` (solo en baja desde listado)

## Ruta de menú

Sin entrada de menú en el índice (subflujo de «definir módulos»).
