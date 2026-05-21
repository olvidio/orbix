---
id: "dbextern.sincro.gestionar"
tipo: "capacidad"
modulo: "dbextern"
nombre: "Gestionar Sincro"
entidades: ["CrearPersonaDesdeListasUseCase"]
acciones: ["crear"]
endpoints: ["/src/dbextern/sincro_crear"]
pantallas: ["frontend/dbextern/controller/ver_listas.php"]
casos_uso: ["src\\dbextern\\application\\CrearPersonaDesdeListasUseCase"]
tags: ["crear", "dbextern", "sincro"]
estado_revision: "generado"
---

# Gestionar Sincro

Propuesta generada automaticamente a partir de endpoints con prefijo comun `sincro`.

## Objetivo Funcional

Gestiona CrearPersonaDesdeListasUseCase. Crea una persona en Orbix desde la BDU y la vincula.

## Acciones Detectadas

- `crear`

## Endpoints

- `/src/dbextern/sincro_crear`

## Pantallas Relacionadas

- `frontend/dbextern/controller/ver_listas.php`

## Casos De Uso Detectados

- `src\dbextern\application\CrearPersonaDesdeListasUseCase`

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
