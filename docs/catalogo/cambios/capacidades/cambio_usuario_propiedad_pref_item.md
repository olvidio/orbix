---
id: "cambios.cambio_usuario_propiedad_pref_item.gestionar"
tipo: "capacidad"
modulo: "cambios"
nombre: "Gestionar Cambio Usuario Propiedad Pref Item"
entidades: ["CambioUsuarioPropiedadPrefItem"]
acciones: ["obtener_datos"]
endpoints: ["/src/cambios/cambio_usuario_propiedad_pref_item_data"]
pantallas: ["frontend/cambios/controller/usuario_avisos_pref_condicion.php"]
casos_uso: ["src\\cambios\\application\\CambioUsuarioPropiedadPrefItemData"]
tags: ["cambio", "cambio_usuario_propiedad_pref_item", "cambios", "data", "item", "pref", "propiedad", "usuario"]
estado_revision: "generado"
---

# Gestionar Cambio Usuario Propiedad Pref Item

Propuesta generada automaticamente a partir de endpoints con prefijo comun `cambio_usuario_propiedad_pref_item`.

## Objetivo Funcional

Gestiona CambioUsuarioPropiedadPrefItem. Endpoint JSON: devuelve los datos de una condicion por id_item (si existe) y la lista de casas cuando la propiedad es id_ubi.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/cambios/cambio_usuario_propiedad_pref_item_data`

## Pantallas Relacionadas

- `frontend/cambios/controller/usuario_avisos_pref_condicion.php`

## Casos De Uso Detectados

- `src\cambios\application\CambioUsuarioPropiedadPrefItemData`

## Pistas Desde Endpoints

- Endpoint JSON: devuelve los datos de una condicion por `id_item` (si existe) y la lista de casas cuando la propiedad es `id_ubi`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
