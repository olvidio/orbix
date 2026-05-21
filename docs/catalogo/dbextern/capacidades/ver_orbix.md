---
id: "dbextern.ver_orbix.gestionar"
tipo: "capacidad"
modulo: "dbextern"
nombre: "Gestionar Ver Orbix"
entidades: ["VerOrbix"]
acciones: ["obtener_datos"]
endpoints: ["/src/dbextern/ver_orbix_datos"]
pantallas: ["frontend/dbextern/controller/ver_orbix.php"]
casos_uso: ["src\\dbextern\\application\\VerOrbixData"]
tags: ["datos", "dbextern", "orbix", "ver", "ver_orbix"]
estado_revision: "generado"
---

# Gestionar Ver Orbix

Propuesta generada automaticamente a partir de endpoints con prefijo comun `ver_orbix`.

## Objetivo Funcional

Gestiona VerOrbix. Obtiene la lista de personas Orbix sin unir a la BDU.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/dbextern/ver_orbix_datos`

## Pantallas Relacionadas

- `frontend/dbextern/controller/ver_orbix.php`

## Casos De Uso Detectados

- `src\dbextern\application\VerOrbixData`

## Pistas Desde Endpoints

- Obtiene la lista de personas Orbix sin unir a la BDU.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
