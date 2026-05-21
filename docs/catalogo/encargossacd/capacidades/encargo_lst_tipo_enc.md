---
id: "encargossacd.encargo_lst_tipo_enc.gestionar"
tipo: "capacidad"
modulo: "encargossacd"
nombre: "Gestionar Encargo Lst Tipo Enc"
entidades: ["EncargoLstTipoEnc"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/encargo_lst_tipo_enc_data"]
pantallas: ["frontend/encargossacd/controller/encargo_ver.php"]
casos_uso: ["src\\encargossacd\\application\\EncargoLstTipoEncData"]
tags: ["data", "enc", "encargo", "encargo_lst_tipo_enc", "encargossacd", "lst", "tipo"]
estado_revision: "generado"
---

# Gestionar Encargo Lst Tipo Enc

Propuesta generada automaticamente a partir de endpoints con prefijo comun `encargo_lst_tipo_enc`.

## Objetivo Funcional

Gestiona EncargoLstTipoEnc. Payload de desplegable de tipos de encargo filtrados por prefijo de grupo (id_tipo_enc ~ ^grupo).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/encargossacd/encargo_lst_tipo_enc_data`

## Pantallas Relacionadas

- `frontend/encargossacd/controller/encargo_ver.php`

## Casos De Uso Detectados

- `src\encargossacd\application\EncargoLstTipoEncData`

## Pistas Desde Endpoints

- Payload de desplegable de tipos de encargo filtrados por prefijo de grupo (`id_tipo_enc ~ ^grupo`).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
