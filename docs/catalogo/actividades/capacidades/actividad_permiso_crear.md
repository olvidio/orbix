---
id: "actividades.actividad_permiso_crear.gestionar"
tipo: "capacidad"
modulo: "actividades"
nombre: "Gestionar Actividad Permiso Crear"
entidades: ["ActividadPermisoCrear"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/actividad_permiso_crear_datos"]
pantallas: ["frontend/actividades/controller/actividad_ver.php"]
casos_uso: []
tags: ["actividad", "actividad_permiso_crear", "actividades", "crear", "datos", "permiso"]
estado_revision: "generado"
---

# Gestionar Actividad Permiso Crear

Propuesta generada automaticamente a partir de endpoints con prefijo comun `actividad_permiso_crear`.

## Objetivo Funcional

Gestiona ActividadPermisoCrear. JSON: resultado de {.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/actividades/actividad_permiso_crear_datos`

## Pantallas Relacionadas

- `frontend/actividades/controller/actividad_ver.php`

## Casos De Uso Detectados

No se han detectado casos de uso de aplicacion.

## Pistas Desde Endpoints

- JSON: resultado de {

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
