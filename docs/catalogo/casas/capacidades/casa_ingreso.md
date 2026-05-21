---
id: "casas.casa_ingreso.gestionar"
tipo: "capacidad"
modulo: "casas"
nombre: "Gestionar Casa Ingreso"
entidades: ["CasaIngreso"]
acciones: ["crear_actualizar", "eliminar", "ver_formulario"]
endpoints: ["/src/casas/casa_ingreso_eliminar", "/src/casas/casa_ingreso_form_data", "/src/casas/casa_ingreso_update"]
pantallas: ["frontend/casas/controller/casa.php", "frontend/casas/controller/casa_ingreso_form.php"]
casos_uso: ["src\\casas\\application\\CasaIngresoEliminar", "src\\casas\\application\\CasaIngresoFormData", "src\\casas\\application\\CasaIngresoUpdate"]
tags: ["casa", "casa_ingreso", "casas", "data", "eliminar", "form", "ingreso", "update"]
estado_revision: "generado"
---

# Gestionar Casa Ingreso

Propuesta generada automaticamente a partir de endpoints con prefijo comun `casa_ingreso`.

## Objetivo Funcional

Gestiona CasaIngreso. Crear/actualizar el Ingreso de una actividad. Datos para el formulario de ingreso de una actividad (casa_ingreso_form). Eliminar el Ingreso de una actividad.

## Acciones Detectadas

- `crear_actualizar`
- `eliminar`
- `ver_formulario`

## Endpoints

- `/src/casas/casa_ingreso_eliminar`
- `/src/casas/casa_ingreso_form_data`
- `/src/casas/casa_ingreso_update`

## Pantallas Relacionadas

- `frontend/casas/controller/casa.php`
- `frontend/casas/controller/casa_ingreso_form.php`

## Casos De Uso Detectados

- `src\casas\application\CasaIngresoEliminar`
- `src\casas\application\CasaIngresoFormData`
- `src\casas\application\CasaIngresoUpdate`

## Pistas Desde Endpoints

- Endpoint backend: crear/actualizar el Ingreso de una actividad.
- Endpoint backend: datos para el formulario de ingreso de una actividad (`casa_ingreso_form`).
- Endpoint backend: eliminar el Ingreso de una actividad.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
