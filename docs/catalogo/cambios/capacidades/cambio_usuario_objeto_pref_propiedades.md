---
id: "cambios.cambio_usuario_objeto_pref_propiedades.gestionar"
tipo: "capacidad"
modulo: "cambios"
nombre: "Gestionar Cambio Usuario Objeto Pref Propiedades"
entidades: ["CambioUsuarioObjetoPrefPropiedades"]
acciones: ["obtener_datos"]
endpoints: ["/src/cambios/cambio_usuario_objeto_pref_propiedades_data"]
pantallas: ["frontend/cambios/controller/usuario_avisos_pref_propiedades.php"]
casos_uso: ["src\\cambios\\application\\CambioUsuarioObjetoPrefPropiedadesData"]
tags: ["cambio", "cambio_usuario_objeto_pref_propiedades", "cambios", "data", "objeto", "pref", "propiedades", "usuario"]
estado_revision: "generado"
---

# Gestionar Cambio Usuario Objeto Pref Propiedades

Propuesta generada automaticamente a partir de endpoints con prefijo comun `cambio_usuario_objeto_pref_propiedades`.

## Objetivo Funcional

Gestiona CambioUsuarioObjetoPrefPropiedades. Endpoint JSON: listado de propiedades configurables del objeto indicado, preseleccionadas segun las preferencias ya guardadas.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/cambios/cambio_usuario_objeto_pref_propiedades_data`

## Pantallas Relacionadas

- `frontend/cambios/controller/usuario_avisos_pref_propiedades.php`

## Casos De Uso Detectados

- `src\cambios\application\CambioUsuarioObjetoPrefPropiedadesData`

## Pistas Desde Endpoints

- Endpoint JSON: listado de propiedades configurables del objeto indicado, preseleccionadas segun las preferencias ya guardadas.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
