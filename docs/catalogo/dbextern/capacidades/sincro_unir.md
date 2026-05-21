---
id: "dbextern.sincro_unir.gestionar"
tipo: "capacidad"
modulo: "dbextern"
nombre: "Gestionar Sincro Unir"
entidades: ["UnirPersonaUseCase"]
acciones: ["ejecutar"]
endpoints: ["/src/dbextern/sincro_unir"]
pantallas: ["frontend/dbextern/controller/ver_listas.php", "frontend/dbextern/controller/ver_orbix.php"]
casos_uso: ["src\\dbextern\\application\\UnirPersonaUseCase"]
tags: ["dbextern", "sincro", "sincro_unir", "unir"]
estado_revision: "generado"
---

# Gestionar Sincro Unir

Propuesta generada automaticamente a partir de endpoints con prefijo comun `sincro_unir`.

## Objetivo Funcional

Gestiona UnirPersonaUseCase. Vincula una persona de BDU con una persona de Orbix.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/dbextern/sincro_unir`

## Pantallas Relacionadas

- `frontend/dbextern/controller/ver_listas.php`
- `frontend/dbextern/controller/ver_orbix.php`

## Casos De Uso Detectados

- `src\dbextern\application\UnirPersonaUseCase`

## Pistas Desde Endpoints

- Vincula una persona de BDU con una persona de Orbix.

## Errores Conocidos

- `hay un error, no se ha guardado`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
