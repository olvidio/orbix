---
id: "dbextern.ver_orbix_otradl.gestionar"
tipo: "capacidad"
modulo: "dbextern"
nombre: "Gestionar Ver Orbix Otradl"
entidades: ["VerOrbixOtraDl"]
acciones: ["obtener_datos"]
endpoints: ["/src/dbextern/ver_orbix_otradl_datos"]
pantallas: ["frontend/dbextern/controller/ver_orbix_otradl.php"]
casos_uso: ["src\\dbextern\\application\\VerOrbixOtraDlData"]
tags: ["datos", "dbextern", "orbix", "otradl", "ver", "ver_orbix_otradl"]
estado_revision: "generado"
---

# Gestionar Ver Orbix Otradl

Propuesta generada automaticamente a partir de endpoints con prefijo comun `ver_orbix_otradl`.

## Objetivo Funcional

Gestiona VerOrbixOtraDl. Obtiene datos de personas BDU que están en otra DL en Orbix.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/dbextern/ver_orbix_otradl_datos`

## Pantallas Relacionadas

- `frontend/dbextern/controller/ver_orbix_otradl.php`

## Casos De Uso Detectados

- `src\dbextern\application\VerOrbixOtraDlData`

## Pistas Desde Endpoints

- Obtiene datos de personas BDU que están en otra DL en Orbix.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
