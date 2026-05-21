---
id: "actividadtarifas.relacion_tarifa.gestionar"
tipo: "capacidad"
modulo: "actividadtarifas"
nombre: "Gestionar Relacion Tarifa"
entidades: ["RelacionTarifa"]
acciones: ["crear_actualizar", "eliminar", "listar", "ver_formulario"]
endpoints: ["/src/actividadtarifas/relacion_tarifa_eliminar", "/src/actividadtarifas/relacion_tarifa_form_data", "/src/actividadtarifas/relacion_tarifa_lista_data", "/src/actividadtarifas/relacion_tarifa_update"]
pantallas: ["frontend/actividadtarifas/controller/tarifa_tipo_actividad.php", "frontend/actividadtarifas/controller/tarifa_tipo_actividad_form.php", "frontend/actividadtarifas/controller/tarifa_tipo_actividad_lista.php", "frontend/pasarela/controller/nombre_form.php"]
casos_uso: ["src\\actividadtarifas\\application\\RelacionTarifaEliminar", "src\\actividadtarifas\\application\\RelacionTarifaFormData", "src\\actividadtarifas\\application\\RelacionTarifaListaData", "src\\actividadtarifas\\application\\RelacionTarifaUpdate"]
tags: ["actividadtarifas", "data", "eliminar", "form", "lista", "relacion", "relacion_tarifa", "tarifa", "update"]
estado_revision: "generado"
---

# Gestionar Relacion Tarifa

Propuesta generada automaticamente a partir de endpoints con prefijo comun `relacion_tarifa`.

## Objetivo Funcional

Gestiona RelacionTarifa. crea o actualiza una RelacionTarifaTipoActividad. datos del formulario modificar/nuevo de RelacionTarifaTipoActividad. elimina una RelacionTarifaTipoActividad. listado de relaciones tarifa ↔ tipo actividad.

## Acciones Detectadas

- `crear_actualizar`
- `eliminar`
- `listar`
- `ver_formulario`

## Endpoints

- `/src/actividadtarifas/relacion_tarifa_eliminar`
- `/src/actividadtarifas/relacion_tarifa_form_data`
- `/src/actividadtarifas/relacion_tarifa_lista_data`
- `/src/actividadtarifas/relacion_tarifa_update`

## Pantallas Relacionadas

- `frontend/actividadtarifas/controller/tarifa_tipo_actividad.php`
- `frontend/actividadtarifas/controller/tarifa_tipo_actividad_form.php`
- `frontend/actividadtarifas/controller/tarifa_tipo_actividad_lista.php`
- `frontend/pasarela/controller/nombre_form.php`

## Casos De Uso Detectados

- `src\actividadtarifas\application\RelacionTarifaEliminar`
- `src\actividadtarifas\application\RelacionTarifaFormData`
- `src\actividadtarifas\application\RelacionTarifaListaData`
- `src\actividadtarifas\application\RelacionTarifaUpdate`

## Pistas Desde Endpoints

- Endpoint backend: crea o actualiza una `RelacionTarifaTipoActividad`.
- Endpoint backend: datos del formulario modificar/nuevo de `RelacionTarifaTipoActividad`.
- Endpoint backend: elimina una `RelacionTarifaTipoActividad`.
- Endpoint backend: listado de relaciones tarifa ↔ tipo actividad.

## Errores Conocidos

- `debe indicar el tipo de actividad`
- `debe indicar la tarifa`
- `hay un error, no se ha borrado`
- `hay un error, no se ha guardado`
- `no se encuentra la relación`
- `no sé cuál he de borrar`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
