---
id: "ubis.ubis_editar_normalize_dl.gestionar"
tipo: "capacidad"
modulo: "ubis"
nombre: "Gestionar Ubis Editar Normalize Dl"
entidades: ["UbisEditarNormalizeDl"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/ubis_editar_normalize_dl_data"]
pantallas: []
casos_uso: ["src\\ubis\\application\\UbisEditarNormalizeDlData"]
tags: ["data", "dl", "editar", "normalize", "ubis", "ubis_editar_normalize_dl"]
estado_revision: "generado"
---

# Gestionar Ubis Editar Normalize Dl

Propuesta generada automaticamente a partir de endpoints con prefijo comun `ubis_editar_normalize_dl`.

## Objetivo Funcional

Gestiona UbisEditarNormalizeDl. Ajusta obj_pau a CentroDl/CasaDl cuando la ficha es de la delegación actual.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/ubis/ubis_editar_normalize_dl_data`

## Pantallas Relacionadas

No se han detectado pantallas relacionadas.

## Casos De Uso Detectados

- `src\ubis\application\UbisEditarNormalizeDlData`

## Pistas Desde Endpoints

- Ajusta `obj_pau` a CentroDl/CasaDl cuando la ficha es de la delegación actual.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
