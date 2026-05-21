---
id: "dbextern.sincro_baja.gestionar"
tipo: "capacidad"
modulo: "dbextern"
nombre: "Gestionar Sincro Baja"
entidades: ["BajaPersonaUseCase"]
acciones: ["ejecutar"]
endpoints: ["/src/dbextern/sincro_baja"]
pantallas: ["frontend/dbextern/controller/ver_desaparecidos_de_listas.php"]
casos_uso: ["src\\dbextern\\application\\BajaPersonaUseCase"]
tags: ["baja", "dbextern", "sincro", "sincro_baja"]
estado_revision: "generado"
---

# Gestionar Sincro Baja

Propuesta generada automaticamente a partir de endpoints con prefijo comun `sincro_baja`.

## Objetivo Funcional

Gestiona BajaPersonaUseCase. Da de baja a una persona (fallecido o traslado a otra región).

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/dbextern/sincro_baja`

## Pantallas Relacionadas

- `frontend/dbextern/controller/ver_desaparecidos_de_listas.php`

## Casos De Uso Detectados

- `src\dbextern\application\BajaPersonaUseCase`

## Pistas Desde Endpoints

- Da de baja a una persona (fallecido o traslado a otra región).

## Errores Conocidos

- `OJO: Debería cambiar el campo situación. No se ha hecho ningún cambio.`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
