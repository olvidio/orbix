---
id: "pasarela.activacion_excepcion.gestionar"
tipo: "capacidad"
modulo: "pasarela"
nombre: "Gestionar Activacion Excepcion"
entidades: ["ActivacionExcepcion"]
acciones: ["eliminar", "guardar"]
endpoints: ["/src/pasarela/activacion_excepcion_eliminar", "/src/pasarela/activacion_excepcion_guardar"]
pantallas: ["frontend/pasarela/controller/activacion_ajax.php", "frontend/pasarela/controller/activacion_lista.php"]
casos_uso: ["src\\pasarela\\application\\ActivacionExcepcionEliminar", "src\\pasarela\\application\\ActivacionExcepcionGuardar"]
tags: ["activacion", "activacion_excepcion", "eliminar", "excepcion", "guardar", "pasarela"]
estado_revision: "generado"
---

# Gestionar Activacion Excepcion

Propuesta generada automaticamente a partir de endpoints con prefijo comun `activacion_excepcion`.

## Objetivo Funcional

Gestiona ActivacionExcepcion. Elimina una excepción del parámetro fecha_activacion para un id_tipo_activ concreto. Inserta o actualiza una excepción del parámetro fecha_activacion para un id_tipo_activ concreto.

## Acciones Detectadas

- `eliminar`
- `guardar`

## Endpoints

- `/src/pasarela/activacion_excepcion_eliminar`
- `/src/pasarela/activacion_excepcion_guardar`

## Pantallas Relacionadas

- `frontend/pasarela/controller/activacion_ajax.php`
- `frontend/pasarela/controller/activacion_lista.php`

## Casos De Uso Detectados

- `src\pasarela\application\ActivacionExcepcionEliminar`
- `src\pasarela\application\ActivacionExcepcionGuardar`

## Pistas Desde Endpoints

- Elimina una excepción del parámetro `fecha_activacion` para un `id_tipo_activ` concreto.
- Inserta o actualiza una excepción del parámetro `fecha_activacion` para un `id_tipo_activ` concreto.

## Errores Conocidos

- `Falta id_tipo_activ`
- `Falta valor de activación`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
