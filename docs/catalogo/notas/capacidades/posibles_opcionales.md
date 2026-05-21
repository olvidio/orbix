---
id: "notas.posibles_opcionales.gestionar"
tipo: "capacidad"
modulo: "notas"
nombre: "Gestionar Posibles Opcionales"
entidades: ["PosiblesOpcionales"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/posibles_opcionales_data"]
pantallas: ["frontend/actividadestudios/controller/form_matriculas_de_una_persona.php", "frontend/notas/controller/form_notas_de_una_persona.php"]
casos_uso: ["src\\notas\\application\\PosiblesOpcionalesData"]
tags: ["data", "notas", "opcionales", "posibles", "posibles_opcionales"]
estado_revision: "generado"
---

# Gestionar Posibles Opcionales

Propuesta generada automaticamente a partir de endpoints con prefijo comun `posibles_opcionales`.

## Objetivo Funcional

Gestiona PosiblesOpcionales. Devuelve las asignaturas opcionales que puede cursar la persona con el contrato estandar de desplegable (ver refactor.md §"Desplegables devueltos por endpoints AJAX: payload + constructor en frontend").

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/notas/posibles_opcionales_data`

## Pantallas Relacionadas

- `frontend/actividadestudios/controller/form_matriculas_de_una_persona.php`
- `frontend/notas/controller/form_notas_de_una_persona.php`

## Casos De Uso Detectados

- `src\notas\application\PosiblesOpcionalesData`

## Pistas Desde Endpoints

- Devuelve las asignaturas opcionales que puede cursar la persona con el contrato estandar de desplegable (ver `refactor.md` §"Desplegables devueltos por endpoints AJAX: payload + constructor en frontend").

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
