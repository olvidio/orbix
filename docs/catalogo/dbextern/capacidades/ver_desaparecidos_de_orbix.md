---
id: "dbextern.ver_desaparecidos_de_orbix.gestionar"
tipo: "capacidad"
modulo: "dbextern"
nombre: "Gestionar Ver Desaparecidos De Orbix"
entidades: ["VerDesaparecidosDeOrbix"]
acciones: ["obtener_datos"]
endpoints: ["/src/dbextern/ver_desaparecidos_de_orbix_datos"]
pantallas: ["frontend/dbextern/controller/ver_desaparecidos_de_orbix.php"]
casos_uso: ["src\\dbextern\\application\\VerDesaparecidosDeOrbixData"]
tags: ["datos", "dbextern", "de", "desaparecidos", "orbix", "ver", "ver_desaparecidos_de_orbix"]
estado_revision: "generado"
---

# Gestionar Ver Desaparecidos De Orbix

Propuesta generada automaticamente a partir de endpoints con prefijo comun `ver_desaparecidos_de_orbix`.

## Objetivo Funcional

Gestiona VerDesaparecidosDeOrbix. Obtiene datos de personas BDU desaparecidas de Orbix.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/dbextern/ver_desaparecidos_de_orbix_datos`

## Pantallas Relacionadas

- `frontend/dbextern/controller/ver_desaparecidos_de_orbix.php`

## Casos De Uso Detectados

- `src\dbextern\application\VerDesaparecidosDeOrbixData`

## Pistas Desde Endpoints

- Obtiene datos de personas BDU desaparecidas de Orbix.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
