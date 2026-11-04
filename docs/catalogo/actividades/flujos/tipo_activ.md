---
id: "actividades.tipo_activ.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Ver los tipos de actividad"
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

Ver los tipos de actividad, crear uno nuevo, cambiar el nombre de uno existente o eliminarlo.

## Punto De Entrada

Menú **sistema > Configuración > gestión Tipos actividades** (Pills2: ADMIN LOCAL > Gestión tipos de actividad).

## Escenarios Inferidos

### Ver los tipos de actividad

Pasos propuestos:
1. Abrir la gestión de tipos de actividad: aparece la tabla con todos los tipos.

Endpoints asociados:
- `/src/actividades/tipo_activ_eliminar`
- `/src/actividades/tipo_activ_lista`
- `/src/actividades/tipo_activ_nuevo`
- `/src/actividades/tipo_activ_update`

### Crear o modificar un tipo de actividad

Pasos propuestos:
1. Pulsar **nuevo**, o seleccionar un tipo y pulsar **modificar**.
2. Rellenar el formulario y guardar.

Endpoints asociados:
- `/src/actividades/tipo_activ_eliminar`
- `/src/actividades/tipo_activ_lista`
- `/src/actividades/tipo_activ_nuevo`
- `/src/actividades/tipo_activ_update`

### Eliminar un tipo de actividad

Pasos propuestos:
1. Seleccionar el tipo y pulsar **eliminar**.
2. Confirmar el aviso.

Endpoints asociados:
- `/src/actividades/tipo_activ_eliminar`
- `/src/actividades/tipo_activ_lista`
- `/src/actividades/tipo_activ_nuevo`
- `/src/actividades/tipo_activ_update`

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
