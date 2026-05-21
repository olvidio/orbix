---
id: "ubiscamas.habitacion.gestionar"
tipo: "capacidad"
modulo: "ubiscamas"
nombre: "Gestionar Habitacion"
entidades: ["Habitacion"]
acciones: ["crear_actualizar", "eliminar", "ver_formulario"]
endpoints: ["/src/ubiscamas/habitacion_delete", "/src/ubiscamas/habitacion_form_data", "/src/ubiscamas/habitacion_update"]
pantallas: ["frontend/ubiscamas/controller/habitacion_form.php"]
casos_uso: ["src\\ubiscamas\\application\\HabitacionFormData"]
tags: ["data", "delete", "form", "habitacion", "ubiscamas", "update"]
estado_revision: "generado"
---

# Gestionar Habitacion

Propuesta generada automaticamente a partir de endpoints con prefijo comun `habitacion`.

## Objetivo Funcional

Gestiona Habitacion. Datos para frontend/ubiscamas/controller/habitacion_form.php. La composición de HashFront ocurre en {. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `crear_actualizar`
- `eliminar`
- `ver_formulario`

## Endpoints

- `/src/ubiscamas/habitacion_delete`
- `/src/ubiscamas/habitacion_form_data`
- `/src/ubiscamas/habitacion_update`

## Pantallas Relacionadas

- `frontend/ubiscamas/controller/habitacion_form.php`

## Casos De Uso Detectados

- `src\ubiscamas\application\HabitacionFormData`

## Pistas Desde Endpoints

- Datos para `frontend/ubiscamas/controller/habitacion_form.php`. La composición de `HashFront` ocurre en {
- Descripcion funcional pendiente de revisar.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
