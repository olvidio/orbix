---
id: "dbextern.sincro_crear_todos.gestionar"
tipo: "capacidad"
modulo: "dbextern"
nombre: "Gestionar Sincro Crear Todos"
entidades: ["CrearPersonaDesdeListasUseCase", "CrearTodosDesdeListasUseCase"]
acciones: ["ejecutar"]
endpoints: ["/src/dbextern/sincro_crear_todos"]
pantallas: ["frontend/dbextern/controller/ver_listas.php"]
casos_uso: ["src\\dbextern\\application\\CrearPersonaDesdeListasUseCase", "src\\dbextern\\application\\CrearTodosDesdeListasUseCase"]
tags: ["crear", "dbextern", "sincro", "sincro_crear_todos", "todos"]
estado_revision: "generado"
---

# Gestionar Sincro Crear Todos

Propuesta generada automaticamente a partir de endpoints con prefijo comun `sincro_crear_todos`.

## Objetivo Funcional

Gestiona CrearPersonaDesdeListasUseCase, CrearTodosDesdeListasUseCase. Crea una persona en Orbix desde la BDU y la vincula.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/dbextern/sincro_crear_todos`

## Pantallas Relacionadas

- `frontend/dbextern/controller/ver_listas.php`

## Casos De Uso Detectados

- `src\dbextern\application\CrearPersonaDesdeListasUseCase`
- `src\dbextern\application\CrearTodosDesdeListasUseCase`

## Pistas Desde Endpoints

- Crea una persona en Orbix desde la BDU y la vincula.

## Errores Conocidos

- `hay un error, no se ha guardado`
- `no se encontró la persona en la BDU`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
