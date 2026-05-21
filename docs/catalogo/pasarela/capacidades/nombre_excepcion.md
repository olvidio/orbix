---
id: "pasarela.nombre_excepcion.gestionar"
tipo: "capacidad"
modulo: "pasarela"
nombre: "Gestionar Nombre Excepcion"
entidades: ["NombreExcepcion"]
acciones: ["eliminar", "guardar"]
endpoints: ["/src/pasarela/nombre_excepcion_eliminar", "/src/pasarela/nombre_excepcion_guardar"]
pantallas: ["frontend/pasarela/controller/nombre_ajax.php", "frontend/pasarela/controller/nombre_lista.php"]
casos_uso: ["src\\pasarela\\application\\NombreExcepcionEliminar", "src\\pasarela\\application\\NombreExcepcionGuardar"]
tags: ["eliminar", "excepcion", "guardar", "nombre", "nombre_excepcion", "pasarela"]
estado_revision: "generado"
---

# Gestionar Nombre Excepcion

Propuesta generada automaticamente a partir de endpoints con prefijo comun `nombre_excepcion`.

## Objetivo Funcional

Gestiona NombreExcepcion. Elimina una excepción del parámetro nombre para un id_tipo_activ concreto. Inserta o actualiza una excepción del parámetro nombre para un id_tipo_activ concreto.

## Acciones Detectadas

- `eliminar`
- `guardar`

## Endpoints

- `/src/pasarela/nombre_excepcion_eliminar`
- `/src/pasarela/nombre_excepcion_guardar`

## Pantallas Relacionadas

- `frontend/pasarela/controller/nombre_ajax.php`
- `frontend/pasarela/controller/nombre_lista.php`

## Casos De Uso Detectados

- `src\pasarela\application\NombreExcepcionEliminar`
- `src\pasarela\application\NombreExcepcionGuardar`

## Pistas Desde Endpoints

- Elimina una excepción del parámetro `nombre` para un `id_tipo_activ` concreto.
- Inserta o actualiza una excepción del parámetro `nombre` para un `id_tipo_activ` concreto.

## Errores Conocidos

- `Falta id_tipo_activ`
- `Falta nombre`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
