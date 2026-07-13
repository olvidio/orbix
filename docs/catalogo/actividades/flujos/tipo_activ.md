---
id: "actividades.tipo_activ.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Gestionar tipos de actividad"
capacidad: "actividades.tipo_activ.gestionar"
pantallas_principales: ["actividades.pantalla.tipo_activ"]
fragmentos: []
acciones: ["crear", "crear_actualizar", "eliminar", "listar"]
endpoints: ["/src/actividades/tipo_activ_eliminar", "/src/actividades/tipo_activ_lista", "/src/actividades/tipo_activ_nuevo", "/src/actividades/tipo_activ_update"]
estado_revision: "revisado"
---

# Flujo - Gestionar tipos de actividad

CRUD del catálogo local de tipos (id compuesto de 6 caracteres + nombre).

## Objetivo De Usuario

Listar tipos, crear uno nuevo, renombrar o eliminar desde la pantalla de administración.

## Punto De Entrada

`tipo_activ.php` (menú configuración).

## Escenarios

### Listar

1. Al cargar, AJAX `tipo_activ_lista` pinta la tabla.

### Crear / Actualizar

1. Formulario nuevo (`tipo_activ_form_nuevo`) o modificar (`tipo_activ_form_modificar`).
2. Guardar vía `tipo_activ_nuevo` o `tipo_activ_update`.

### Eliminar

1. Confirmar mensaje; POST `tipo_activ_eliminar` con `id_tipo_activ`.

## Endpoints Del Flujo

- `/src/actividades/tipo_activ_lista`
- `/src/actividades/tipo_activ_nuevo`
- `/src/actividades/tipo_activ_update`
- `/src/actividades/tipo_activ_eliminar`

## Errores Conocidos

- `tipo de actividad no encontrado`
- `Id incorrecto` (alta)
- `hay un error, no se ha guardado` / `hay un error, no se ha eliminado`
- Aviso: `IMPORTANTE: Debe añadir un proceso…` (con `procesos` instalado)

## Ruta de menú

- **Legacy:** sistema > Configuración > gestión Tipos actividades.
- **Pills2:** ADMIN LOCAL > Gestión tipos de actividad.
