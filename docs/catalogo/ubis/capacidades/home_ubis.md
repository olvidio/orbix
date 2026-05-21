---
id: "ubis.home_ubis.gestionar"
tipo: "capacidad"
modulo: "ubis"
nombre: "Gestionar Home Ubis"
entidades: ["HomeUbis"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/home_ubis_data"]
pantallas: ["frontend/ubis/controller/home_ubis.php"]
casos_uso: ["src\\ubis\\application\\HomeUbisData"]
tags: ["data", "home", "home_ubis", "ubis"]
estado_revision: "generado"
---

# Gestionar Home Ubis

Propuesta generada automaticamente a partir de endpoints con prefijo comun `home_ubis`.

## Objetivo Funcional

Gestiona HomeUbis. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/ubis/home_ubis_data`

## Pantallas Relacionadas

- `frontend/ubis/controller/home_ubis.php`

## Casos De Uso Detectados

- `src\ubis\application\HomeUbisData`

## Pistas Desde Endpoints

- Descripcion funcional pendiente de revisar.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
