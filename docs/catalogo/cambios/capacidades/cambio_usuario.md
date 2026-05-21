---
id: "cambios.cambio_usuario.gestionar"
tipo: "capacidad"
modulo: "cambios"
nombre: "Gestionar Cambio Usuario"
entidades: ["CambioUsuario"]
acciones: ["eliminar"]
endpoints: ["/src/cambios/cambio_usuario_eliminar"]
pantallas: []
casos_uso: ["src\\cambios\\application\\CambioUsuarioEliminar"]
tags: ["cambio", "cambio_usuario", "cambios", "eliminar", "usuario"]
estado_revision: "generado"
---

# Gestionar Cambio Usuario

Propuesta generada automaticamente a partir de endpoints con prefijo comun `cambio_usuario`.

## Objetivo Funcional

Gestiona CambioUsuario. Elimina CambioUsuario por la clave compuesta id_item_cambio#id_usuario#sfsv#aviso_tipo recibida en sel[].

## Acciones Detectadas

- `eliminar`

## Endpoints

- `/src/cambios/cambio_usuario_eliminar`

## Pantallas Relacionadas

No se han detectado pantallas relacionadas.

## Casos De Uso Detectados

- `src\cambios\application\CambioUsuarioEliminar`

## Pistas Desde Endpoints

- Endpoint backend: elimina `CambioUsuario` por la clave compuesta `id_item_cambio#id_usuario#sfsv#aviso_tipo` recibida en `sel[]`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
