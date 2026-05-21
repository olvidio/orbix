---
id: "actividades.actividad_tipo.gestionar"
tipo: "capacidad"
modulo: "actividades"
nombre: "Gestionar Actividad Tipo"
entidades: ["ActividadTipoGetActividad", "ActividadTipoGetAsistentes", "ActividadTipoGetDlOrg", "ActividadTipoGetFiltroLugar", "ActividadTipoGetIdTarifa", "ActividadTipoGetLugar", "ActividadTipoGetNivelStgrDefecto", "ActividadTipoGetNomTipo", "ActividadTipoGetNomTipoTabla"]
acciones: ["obtener"]
endpoints: ["/src/actividades/actividad_tipo_get"]
pantallas: ["frontend/actividades/controller/actividad_que.php", "frontend/actividades/controller/actividad_select_ubi.php", "frontend/actividades/helpers/ActividadTipo.php", "frontend/actividades/view/actividad_select_ubi.phtml", "frontend/pasarela/controller/nombre_form.php", "frontend/procesos/controller/fases_activ_cambio.php"]
casos_uso: ["src\\actividades\\application\\ActividadTipoGetActividad", "src\\actividades\\application\\ActividadTipoGetAsistentes", "src\\actividades\\application\\ActividadTipoGetDlOrg", "src\\actividades\\application\\ActividadTipoGetFiltroLugar", "src\\actividades\\application\\ActividadTipoGetIdTarifa", "src\\actividades\\application\\ActividadTipoGetLugar", "src\\actividades\\application\\ActividadTipoGetNivelStgrDefecto", "src\\actividades\\application\\ActividadTipoGetNomTipo", "src\\actividades\\application\\ActividadTipoGetNomTipoTabla"]
tags: ["actividad", "actividad_tipo", "actividades", "get", "tipo"]
estado_revision: "generado"
---

# Gestionar Actividad Tipo

Propuesta generada automaticamente a partir de endpoints con prefijo comun `actividad_tipo`.

## Objetivo Funcional

Gestiona ActividadTipoGetActividad, ActividadTipoGetAsistentes, ActividadTipoGetDlOrg, ActividadTipoGetFiltroLugar, ActividadTipoGetIdTarifa, ActividadTipoGetLugar, ActividadTipoGetNivelStgrDefecto, ActividadTipoGetNomTipo, ActividadTipoGetNomTipoTabla. Endpoint backend que devuelve el payload necesario (datos de desplegable, tabla HTML o valor escalar) segun el parametro POST salida.

## Acciones Detectadas

- `obtener`

## Endpoints

- `/src/actividades/actividad_tipo_get`

## Pantallas Relacionadas

- `frontend/actividades/controller/actividad_que.php`
- `frontend/actividades/controller/actividad_select_ubi.php`
- `frontend/actividades/helpers/ActividadTipo.php`
- `frontend/actividades/view/actividad_select_ubi.phtml`
- `frontend/pasarela/controller/nombre_form.php`
- `frontend/procesos/controller/fases_activ_cambio.php`

## Casos De Uso Detectados

- `src\actividades\application\ActividadTipoGetActividad`
- `src\actividades\application\ActividadTipoGetAsistentes`
- `src\actividades\application\ActividadTipoGetDlOrg`
- `src\actividades\application\ActividadTipoGetFiltroLugar`
- `src\actividades\application\ActividadTipoGetIdTarifa`
- `src\actividades\application\ActividadTipoGetLugar`
- `src\actividades\application\ActividadTipoGetNivelStgrDefecto`
- `src\actividades\application\ActividadTipoGetNomTipo`
- `src\actividades\application\ActividadTipoGetNomTipoTabla`

## Pistas Desde Endpoints

- Endpoint backend que devuelve el payload necesario (datos de desplegable, tabla HTML o valor escalar) segun el parametro POST `salida`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
