---
id: "asistentes.activ_pendientes_select.gestionar"
tipo: "capacidad"
modulo: "asistentes"
nombre: "Gestionar Activ Pendientes Select"
entidades: ["ActivPendientesSelect"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/activ_pendientes_select_data"]
pantallas: ["frontend/asistentes/controller/activ_pendientes_select.php"]
casos_uso: ["src\\asistentes\\application\\ActivPendientesSelectData"]
tags: ["activ", "activ_pendientes_select", "asistentes", "data", "pendientes", "select"]
estado_revision: "generado"
---

# Gestionar Activ Pendientes Select

Propuesta generada automaticamente a partir de endpoints con prefijo comun `activ_pendientes_select`.

## Objetivo Funcional

Gestiona ActivPendientesSelect. Actividades pendientes por curso (activ_pendientes_select.php). Datos y link_spec sin firmar; hash, firmas y tablas en {.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/asistentes/activ_pendientes_select_data`

## Pantallas Relacionadas

- `frontend/asistentes/controller/activ_pendientes_select.php`

## Casos De Uso Detectados

- `src\asistentes\application\ActivPendientesSelectData`

## Pistas Desde Endpoints

- Actividades pendientes por curso (`activ_pendientes_select.php`). Datos y `link_spec` sin firmar; hash, firmas y tablas en {

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
