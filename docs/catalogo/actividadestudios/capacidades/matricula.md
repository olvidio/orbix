---
id: "actividadestudios.matricula.gestionar"
tipo: "capacidad"
modulo: "actividadestudios"
nombre: "Gestionar Matricula"
entidades: ["Matricula"]
acciones: ["crear", "eliminar"]
endpoints: ["/src/actividadestudios/matricula_eliminar", "/src/actividadestudios/matricula_nueva"]
pantallas: ["frontend/actividadestudios/controller/form_matriculas_de_una_persona.php", "frontend/actividadestudios/controller/matriculas_pendientes.php", "frontend/actividadestudios/view/matriculas.phtml"]
casos_uso: ["src\\actividadestudios\\application\\MatriculaEliminar", "src\\actividadestudios\\application\\MatriculaNueva"]
tags: ["actividadestudios", "eliminar", "matricula", "nueva"]
estado_revision: "generado"
---

# Gestionar Matricula

Propuesta generada automaticamente a partir de endpoints con prefijo comun `matricula`.

## Objetivo Funcional

Gestiona Matricula. Crea una matricula. Elimina una o varias matriculas.

## Acciones Detectadas

- `crear`
- `eliminar`

## Endpoints

- `/src/actividadestudios/matricula_eliminar`
- `/src/actividadestudios/matricula_nueva`

## Pantallas Relacionadas

- `frontend/actividadestudios/controller/form_matriculas_de_una_persona.php`
- `frontend/actividadestudios/controller/matriculas_pendientes.php`
- `frontend/actividadestudios/view/matriculas.phtml`

## Casos De Uso Detectados

- `src\actividadestudios\application\MatriculaEliminar`
- `src\actividadestudios\application\MatriculaNueva`

## Pistas Desde Endpoints

- Crea una matricula.
- Elimina una o varias matriculas.

## Errores Conocidos

- `falta id_activ o id_nom`
- `hay un error, no se ha borrado`
- `hay un error, no se ha guardado`
- `no encuentro asignatura para ese nivel`
- `no encuentro la matricula`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
