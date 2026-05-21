---
id: "dbextern.sincro_desunir.gestionar"
tipo: "capacidad"
modulo: "dbextern"
nombre: "Gestionar Sincro Desunir"
entidades: ["DesunirPersonaUseCase"]
acciones: ["ejecutar"]
endpoints: ["/src/dbextern/sincro_desunir"]
pantallas: ["frontend/dbextern/controller/ver_desaparecidos_de_orbix.php"]
casos_uso: ["src\\dbextern\\application\\DesunirPersonaUseCase"]
tags: ["dbextern", "desunir", "sincro", "sincro_desunir"]
estado_revision: "generado"
---

# Gestionar Sincro Desunir

Propuesta generada automaticamente a partir de endpoints con prefijo comun `sincro_desunir`.

## Objetivo Funcional

Gestiona DesunirPersonaUseCase. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/dbextern/sincro_desunir`

## Pantallas Relacionadas

- `frontend/dbextern/controller/ver_desaparecidos_de_orbix.php`

## Casos De Uso Detectados

- `src\dbextern\application\DesunirPersonaUseCase`

## Pistas Desde Endpoints

- Descripcion funcional pendiente de revisar.

## Errores Conocidos

- `hay un error, no se ha eliminado`
- `no se encontró el registro a desunir`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
