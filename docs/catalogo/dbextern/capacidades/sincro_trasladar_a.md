---
id: "dbextern.sincro_trasladar_a.gestionar"
tipo: "capacidad"
modulo: "dbextern"
nombre: "Gestionar Sincro Trasladar A"
entidades: ["TrasladarPersonaUseCase"]
acciones: ["ejecutar"]
endpoints: ["/src/dbextern/sincro_trasladar_a"]
pantallas: ["frontend/dbextern/controller/ver_orbix_otradl.php"]
casos_uso: ["src\\dbextern\\application\\TrasladarPersonaUseCase"]
tags: ["a", "dbextern", "sincro", "sincro_trasladar_a", "trasladar"]
estado_revision: "generado"
---

# Gestionar Sincro Trasladar A

Propuesta generada automaticamente a partir de endpoints con prefijo comun `sincro_trasladar_a`.

## Objetivo Funcional

Gestiona TrasladarPersonaUseCase. Trasladar persona desde otra DL a la DL actual.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/dbextern/sincro_trasladar_a`

## Pantallas Relacionadas

- `frontend/dbextern/controller/ver_orbix_otradl.php`

## Casos De Uso Detectados

- `src\dbextern\application\TrasladarPersonaUseCase`

## Pistas Desde Endpoints

- Trasladar persona desde otra DL a la DL actual.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
