---
id: "procesos.fases_activ_cambio.gestionar"
tipo: "capacidad"
modulo: "procesos"
nombre: "Gestionar Fases Activ Cambio"
entidades: ["FasesActivCambio", "FasesActivCambioGet", "FasesActivCambioLista"]
acciones: ["crear_actualizar", "listar", "obtener"]
endpoints: ["/src/procesos/fases_activ_cambio_get", "/src/procesos/fases_activ_cambio_lista", "/src/procesos/fases_activ_cambio_update"]
pantallas: ["frontend/procesos/controller/fases_activ_cambio.php", "frontend/procesos/controller/fases_activ_cambio_lista.php"]
casos_uso: ["src\\procesos\\application\\FasesActivCambioGet", "src\\procesos\\application\\FasesActivCambioLista", "src\\procesos\\application\\FasesActivCambioUpdate"]
tags: ["activ", "cambio", "fases", "fases_activ_cambio", "get", "lista", "procesos", "update"]
estado_revision: "generado"
---

# Gestionar Fases Activ Cambio

Propuesta generada automaticamente a partir de endpoints con prefijo comun `fases_activ_cambio`.

## Objetivo Funcional

Gestiona FasesActivCambio, FasesActivCambioGet, FasesActivCambioLista. Caso de uso: aplica setCompletado(t|f) a la tarea de la fase nueva para cada id_activ seleccionado, respetando permisos de oficina del responsable. Caso de uso: devuelve las fases posibles para el id_tipo_activ y la dl_propia actual, incluyendo la opcion seleccionada por id_fase_sel. Respuesta conforme al contrato de refactor.md para desplegables (payload JSON con id, opciones, selected, blanco, action). El frontend construye el <select> con el helper JS estandar. Caso de uso: devuelve los datos estructurados para la tabla de actividades candidatas a cambiar de fase, segun filtros de tipo de actividad, dl_propia, periodo y accion (marcar/desmarcar). El frontend renderiza el formulario con frontend\shared\web\Lista + web\Hash.

## Acciones Detectadas

- `crear_actualizar`
- `listar`
- `obtener`

## Endpoints

- `/src/procesos/fases_activ_cambio_get`
- `/src/procesos/fases_activ_cambio_lista`
- `/src/procesos/fases_activ_cambio_update`

## Pantallas Relacionadas

- `frontend/procesos/controller/fases_activ_cambio.php`
- `frontend/procesos/controller/fases_activ_cambio_lista.php`

## Casos De Uso Detectados

- `src\procesos\application\FasesActivCambioGet`
- `src\procesos\application\FasesActivCambioLista`
- `src\procesos\application\FasesActivCambioUpdate`

## Pistas Desde Endpoints

- Caso de uso: aplica setCompletado(t|f) a la tarea de la fase nueva para cada id_activ seleccionado, respetando permisos de oficina del responsable.
- Caso de uso: devuelve las fases posibles para el `id_tipo_activ` y la `dl_propia` actual, incluyendo la opcion seleccionada por `id_fase_sel`. Respuesta conforme al contrato de `refactor.md` para desplegables (payload JSON con `id`, `opciones`, `selected`, `blanco`, `action`). El frontend construye el `<select>` con el helper JS estandar.
- Caso de uso: devuelve los datos estructurados para la tabla de actividades candidatas a cambiar de fase, segun filtros de tipo de actividad, dl_propia, periodo y accion (marcar/desmarcar). El frontend renderiza el formulario con `frontend\shared\web\Lista` + `web\Hash`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
