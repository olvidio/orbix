---
id: "actividadcargos.form_cargos_personas_en_actividad.gestionar"
tipo: "capacidad"
modulo: "actividadcargos"
nombre: "Gestionar Form Cargos Personas En Actividad"
entidades: ["FormCargosPersonasEnActividad"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadcargos/form_cargos_personas_en_actividad_data"]
pantallas: ["frontend/actividadcargos/controller/form_cargos_personas_en_actividad.php"]
casos_uso: ["src\\actividadcargos\\application\\FormCargosPersonasEnActividadData"]
tags: ["actividad", "actividadcargos", "cargos", "data", "en", "form", "form_cargos_personas_en_actividad", "personas"]
estado_revision: "generado"
---

# Gestionar Form Cargos Personas En Actividad

Propuesta generada automaticamente a partir de endpoints con prefijo comun `form_cargos_personas_en_actividad`.

## Objetivo Funcional

Gestiona FormCargosPersonasEnActividad. Datos para form_cargos_personas_en_actividad (vista por persona).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/actividadcargos/form_cargos_personas_en_actividad_data`

## Pantallas Relacionadas

- `frontend/actividadcargos/controller/form_cargos_personas_en_actividad.php`

## Casos De Uso Detectados

- `src\actividadcargos\application\FormCargosPersonasEnActividadData`

## Pistas Desde Endpoints

- Datos para `form_cargos_personas_en_actividad` (vista por persona).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
