---
id: "cambios.cambio_usuario_eliminar_hasta_fecha.gestionar"
tipo: "capacidad"
modulo: "cambios"
nombre: "Gestionar Cambio Usuario Eliminar Hasta Fecha"
entidades: ["CambioUsuarioEliminarHastaFecha"]
acciones: ["ejecutar"]
endpoints: ["/src/cambios/cambio_usuario_eliminar_hasta_fecha"]
pantallas: []
casos_uso: ["src\\cambios\\application\\CambioUsuarioEliminarHastaFecha"]
tags: ["cambio", "cambio_usuario_eliminar_hasta_fecha", "cambios", "eliminar", "fecha", "hasta", "usuario"]
estado_revision: "generado"
---

# Gestionar Cambio Usuario Eliminar Hasta Fecha

Propuesta generada automaticamente a partir de endpoints con prefijo comun `cambio_usuario_eliminar_hasta_fecha`.

## Objetivo Funcional

Gestiona CambioUsuarioEliminarHastaFecha. Elimina los CambioUsuario con fecha <= f_fin.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/cambios/cambio_usuario_eliminar_hasta_fecha`

## Pantallas Relacionadas

No se han detectado pantallas relacionadas.

## Casos De Uso Detectados

- `src\cambios\application\CambioUsuarioEliminarHastaFecha`

## Pistas Desde Endpoints

- Endpoint backend: elimina los `CambioUsuario` con fecha <= `f_fin`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
