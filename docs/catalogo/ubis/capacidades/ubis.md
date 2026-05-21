---
id: "ubis.ubis.gestionar"
tipo: "capacidad"
modulo: "ubis"
nombre: "Gestionar Ubis"
entidades: ["Ubis"]
acciones: ["eliminar", "guardar", "listar"]
endpoints: ["/src/ubis/ubis_eliminar", "/src/ubis/ubis_guardar", "/src/ubis/ubis_lista_data"]
pantallas: ["frontend/ubis/controller/ubis_eliminar.php", "frontend/ubis/controller/ubis_lista.php", "frontend/ubis/controller/ubis_update.php"]
casos_uso: ["src\\ubis\\application\\UbisEliminar", "src\\ubis\\application\\UbisGuardar", "src\\ubis\\application\\UbisListaData"]
tags: ["data", "eliminar", "guardar", "lista", "ubis"]
estado_revision: "generado"
---

# Gestionar Ubis

Propuesta generada automaticamente a partir de endpoints con prefijo comun `ubis`.

## Objetivo Funcional

Gestiona Ubis. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `eliminar`
- `guardar`
- `listar`

## Endpoints

- `/src/ubis/ubis_eliminar`
- `/src/ubis/ubis_guardar`
- `/src/ubis/ubis_lista_data`

## Pantallas Relacionadas

- `frontend/ubis/controller/ubis_eliminar.php`
- `frontend/ubis/controller/ubis_lista.php`
- `frontend/ubis/controller/ubis_update.php`

## Casos De Uso Detectados

- `src\ubis\application\UbisEliminar`
- `src\ubis\application\UbisGuardar`
- `src\ubis\application\UbisListaData`

## Pistas Desde Endpoints

- Descripcion funcional pendiente de revisar.

## Errores Conocidos

- `hay un error, no se ha eliminado`
- `hay un error, no se ha guardado`
- `no se encuentra el ubi a borrar`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
