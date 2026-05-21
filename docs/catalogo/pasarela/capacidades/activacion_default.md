---
id: "pasarela.activacion_default.gestionar"
tipo: "capacidad"
modulo: "pasarela"
nombre: "Gestionar Activacion Default"
entidades: ["ActivacionDefault"]
acciones: ["guardar", "obtener_datos"]
endpoints: ["/src/pasarela/activacion_default_data", "/src/pasarela/activacion_default_guardar"]
pantallas: ["frontend/pasarela/controller/activacion_ajax.php"]
casos_uso: ["src\\pasarela\\application\\ActivacionDefaultData", "src\\pasarela\\application\\ActivacionDefaultGuardar"]
tags: ["activacion", "activacion_default", "data", "default", "guardar", "pasarela"]
estado_revision: "generado"
---

# Gestionar Activacion Default

Propuesta generada automaticamente a partir de endpoints con prefijo comun `activacion_default`.

## Objetivo Funcional

Gestiona ActivacionDefault. Actualiza el valor por defecto del parámetro fecha_activacion. Devuelve solo el valor por defecto del parámetro fecha_activacion, para alimentar el formulario form_default desde el frontend.

## Acciones Detectadas

- `guardar`
- `obtener_datos`

## Endpoints

- `/src/pasarela/activacion_default_data`
- `/src/pasarela/activacion_default_guardar`

## Pantallas Relacionadas

- `frontend/pasarela/controller/activacion_ajax.php`

## Casos De Uso Detectados

- `src\pasarela\application\ActivacionDefaultData`
- `src\pasarela\application\ActivacionDefaultGuardar`

## Pistas Desde Endpoints

- Actualiza el valor por defecto del parámetro `fecha_activacion`.
- Devuelve solo el valor por defecto del parámetro `fecha_activacion`, para alimentar el formulario `form_default` desde el frontend.

## Errores Conocidos

- `Falta valor por defecto`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
