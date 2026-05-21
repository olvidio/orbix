---
id: "cambios.cambio_usuario_objeto_pref.gestionar"
tipo: "capacidad"
modulo: "cambios"
nombre: "Gestionar Cambio Usuario Objeto Pref"
entidades: ["CambioUsuarioObjetoPref"]
acciones: ["eliminar", "guardar"]
endpoints: ["/src/cambios/cambio_usuario_objeto_pref_eliminar", "/src/cambios/cambio_usuario_objeto_pref_guardar"]
pantallas: []
casos_uso: ["src\\cambios\\application\\CambioUsuarioObjetoPrefEliminar", "src\\cambios\\application\\CambioUsuarioObjetoPrefGuardar"]
tags: ["cambio", "cambio_usuario_objeto_pref", "cambios", "eliminar", "guardar", "objeto", "pref", "usuario"]
estado_revision: "generado"
---

# Gestionar Cambio Usuario Objeto Pref

Propuesta generada automaticamente a partir de endpoints con prefijo comun `cambio_usuario_objeto_pref`.

## Objetivo Funcional

Gestiona CambioUsuarioObjetoPref. Endpoint JSON: crea o actualiza un CambioUsuarioObjetoPref. Endpoint JSON: elimina un CambioUsuarioObjetoPref.

## Acciones Detectadas

- `eliminar`
- `guardar`

## Endpoints

- `/src/cambios/cambio_usuario_objeto_pref_eliminar`
- `/src/cambios/cambio_usuario_objeto_pref_guardar`

## Pantallas Relacionadas

No se han detectado pantallas relacionadas.

## Casos De Uso Detectados

- `src\cambios\application\CambioUsuarioObjetoPrefEliminar`
- `src\cambios\application\CambioUsuarioObjetoPrefGuardar`

## Pistas Desde Endpoints

- Endpoint JSON: crea o actualiza un `CambioUsuarioObjetoPref`.
- Endpoint JSON: elimina un `CambioUsuarioObjetoPref`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
