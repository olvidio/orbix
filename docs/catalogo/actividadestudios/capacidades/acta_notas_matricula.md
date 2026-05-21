---
id: "actividadestudios.acta_notas_matricula.gestionar"
tipo: "capacidad"
modulo: "actividadestudios"
nombre: "Gestionar Acta Notas Matricula"
entidades: ["ActaNotasMatricula"]
acciones: ["guardar"]
endpoints: ["/src/actividadestudios/acta_notas_matricula_guardar"]
pantallas: ["frontend/actividadestudios/controller/acta_notas.php"]
casos_uso: ["src\\actividadestudios\\application\\ActaNotasMatriculaGuardar"]
tags: ["acta", "acta_notas_matricula", "actividadestudios", "guardar", "matricula", "notas"]
estado_revision: "generado"
---

# Gestionar Acta Notas Matricula

Propuesta generada automaticamente a partir de endpoints con prefijo comun `acta_notas_matricula`.

## Objetivo Funcional

Gestiona ActaNotasMatricula. Guarda el borrador de notas sobre cada matricula (rama que=1 del legacy apps/actividadestudios/controller/acta_notas_update.php).

## Acciones Detectadas

- `guardar`

## Endpoints

- `/src/actividadestudios/acta_notas_matricula_guardar`

## Pantallas Relacionadas

- `frontend/actividadestudios/controller/acta_notas.php`

## Casos De Uso Detectados

- `src\actividadestudios\application\ActaNotasMatriculaGuardar`

## Pistas Desde Endpoints

- Guarda el borrador de notas sobre cada matricula (rama `que=1` del legacy `apps/actividadestudios/controller/acta_notas_update.php`).

## Errores Conocidos

- `Hay una nota mayor que el máximo`
- `hay un error, no se ha guardado`
- `no se puede definir cursada con preceptor`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
