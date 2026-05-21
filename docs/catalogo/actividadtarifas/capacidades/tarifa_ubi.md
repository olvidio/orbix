---
id: "actividadtarifas.tarifa_ubi.gestionar"
tipo: "capacidad"
modulo: "actividadtarifas"
nombre: "Gestionar Tarifa Ubi"
entidades: ["TarifaUbi"]
acciones: ["actualizar_incremento", "copiar", "crear_actualizar", "eliminar", "listar", "ver_formulario"]
endpoints: ["/src/actividadtarifas/tarifa_ubi_copiar", "/src/actividadtarifas/tarifa_ubi_eliminar", "/src/actividadtarifas/tarifa_ubi_form_data", "/src/actividadtarifas/tarifa_ubi_lista_data", "/src/actividadtarifas/tarifa_ubi_update", "/src/actividadtarifas/tarifa_ubi_update_inc"]
pantallas: ["frontend/actividadtarifas/controller/tarifa_ubi.php", "frontend/actividadtarifas/controller/tarifa_ubi_form.php", "frontend/actividadtarifas/controller/tarifa_ubi_lista.php", "frontend/actividadtarifas/view/tarifa_ubi.phtml", "frontend/casas/controller/calendario_ubi_resumen.php"]
casos_uso: ["src\\actividadtarifas\\application\\TarifaUbiCopiar", "src\\actividadtarifas\\application\\TarifaUbiEliminar", "src\\actividadtarifas\\application\\TarifaUbiFormData", "src\\actividadtarifas\\application\\TarifaUbiListaData", "src\\actividadtarifas\\application\\TarifaUbiUpdate", "src\\actividadtarifas\\application\\TarifaUbiUpdateInc"]
tags: ["actividadtarifas", "copiar", "data", "eliminar", "form", "inc", "lista", "tarifa", "tarifa_ubi", "ubi", "update"]
estado_revision: "generado"
---

# Gestionar Tarifa Ubi

Propuesta generada automaticamente a partir de endpoints con prefijo comun `tarifa_ubi`.

## Objetivo Funcional

Gestiona TarifaUbi. actualiza en lote las cantidades de varias TarifaUbi desde el estudio economico de casa. copiar tarifas del año anterior. crea o actualiza una TarifaUbi. datos del formulario modificar/nuevo de TarifaUbi. elimina una TarifaUbi. listado de TarifaUbi por id_ubi + year.

## Acciones Detectadas

- `actualizar_incremento`
- `copiar`
- `crear_actualizar`
- `eliminar`
- `listar`
- `ver_formulario`

## Endpoints

- `/src/actividadtarifas/tarifa_ubi_copiar`
- `/src/actividadtarifas/tarifa_ubi_eliminar`
- `/src/actividadtarifas/tarifa_ubi_form_data`
- `/src/actividadtarifas/tarifa_ubi_lista_data`
- `/src/actividadtarifas/tarifa_ubi_update`
- `/src/actividadtarifas/tarifa_ubi_update_inc`

## Pantallas Relacionadas

- `frontend/actividadtarifas/controller/tarifa_ubi.php`
- `frontend/actividadtarifas/controller/tarifa_ubi_form.php`
- `frontend/actividadtarifas/controller/tarifa_ubi_lista.php`
- `frontend/actividadtarifas/view/tarifa_ubi.phtml`
- `frontend/casas/controller/calendario_ubi_resumen.php`

## Casos De Uso Detectados

- `src\actividadtarifas\application\TarifaUbiCopiar`
- `src\actividadtarifas\application\TarifaUbiEliminar`
- `src\actividadtarifas\application\TarifaUbiFormData`
- `src\actividadtarifas\application\TarifaUbiListaData`
- `src\actividadtarifas\application\TarifaUbiUpdate`
- `src\actividadtarifas\application\TarifaUbiUpdateInc`

## Pistas Desde Endpoints

- Endpoint backend: actualiza en lote las cantidades de varias `TarifaUbi` desde el estudio economico de casa.
- Endpoint backend: copiar tarifas del año anterior.
- Endpoint backend: crea o actualiza una `TarifaUbi`.
- Endpoint backend: datos del formulario modificar/nuevo de `TarifaUbi`.
- Endpoint backend: elimina una `TarifaUbi`.
- Endpoint backend: listado de `TarifaUbi` por `id_ubi` + `year`.

## Errores Conocidos

- `Operación no autorizada`
- `función de copiar tarifas pendiente de reimplementar`
- `hay un error, no se ha borrado`
- `hay un error, no se ha guardado`
- `no se encuentra la tarifa`
- `no sé cuál he de borrar`
- `no sé qué casa/año tengo que copiar`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
