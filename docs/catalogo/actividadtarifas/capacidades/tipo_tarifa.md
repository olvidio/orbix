---
id: "actividadtarifas.tipo_tarifa.gestionar"
tipo: "capacidad"
modulo: "actividadtarifas"
nombre: "Gestionar Tipo Tarifa"
entidades: ["TipoTarifa"]
acciones: ["crear_actualizar", "eliminar", "listar", "ver_formulario"]
endpoints: ["/src/actividadtarifas/tipo_tarifa_eliminar", "/src/actividadtarifas/tipo_tarifa_form_data", "/src/actividadtarifas/tipo_tarifa_lista_data", "/src/actividadtarifas/tipo_tarifa_update"]
pantallas: ["frontend/actividadtarifas/controller/tarifa.php", "frontend/actividadtarifas/controller/tarifa_form.php", "frontend/actividadtarifas/controller/tarifa_lista.php", "frontend/actividadtarifas/view/tarifa_form.phtml"]
casos_uso: ["src\\actividadtarifas\\application\\TipoTarifaEliminar", "src\\actividadtarifas\\application\\TipoTarifaFormData", "src\\actividadtarifas\\application\\TipoTarifaListaData", "src\\actividadtarifas\\application\\TipoTarifaUpdate"]
tags: ["actividadtarifas", "data", "eliminar", "form", "lista", "tarifa", "tipo", "tipo_tarifa", "update"]
estado_revision: "generado"
---

# Gestionar Tipo Tarifa

Propuesta generada automaticamente a partir de endpoints con prefijo comun `tipo_tarifa`.

## Objetivo Funcional

Gestiona TipoTarifa. crea o actualiza un TipoTarifa. datos del formulario modificar/nuevo de TipoTarifa. elimina un TipoTarifa. listado del catalogo de tipos de tarifa.

## Acciones Detectadas

- `crear_actualizar`
- `eliminar`
- `listar`
- `ver_formulario`

## Endpoints

- `/src/actividadtarifas/tipo_tarifa_eliminar`
- `/src/actividadtarifas/tipo_tarifa_form_data`
- `/src/actividadtarifas/tipo_tarifa_lista_data`
- `/src/actividadtarifas/tipo_tarifa_update`

## Pantallas Relacionadas

- `frontend/actividadtarifas/controller/tarifa.php`
- `frontend/actividadtarifas/controller/tarifa_form.php`
- `frontend/actividadtarifas/controller/tarifa_lista.php`
- `frontend/actividadtarifas/view/tarifa_form.phtml`

## Casos De Uso Detectados

- `src\actividadtarifas\application\TipoTarifaEliminar`
- `src\actividadtarifas\application\TipoTarifaFormData`
- `src\actividadtarifas\application\TipoTarifaListaData`
- `src\actividadtarifas\application\TipoTarifaUpdate`

## Pistas Desde Endpoints

- Endpoint backend: crea o actualiza un `TipoTarifa`.
- Endpoint backend: datos del formulario modificar/nuevo de `TipoTarifa`.
- Endpoint backend: elimina un `TipoTarifa`.
- Endpoint backend: listado del catalogo de tipos de tarifa.

## Errores Conocidos

- `hay un error, no se ha borrado`
- `hay un error, no se ha guardado`
- `no se encuentra la tarifa`
- `no sé cuál he de borrar`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
