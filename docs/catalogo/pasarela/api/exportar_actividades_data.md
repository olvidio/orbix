---
id: "pasarela.exportar_actividades_data"
tipo: "endpoint"
modulo: "pasarela"
url: "/src/pasarela/exportar_actividades_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/pasarela/infrastructure/ui/http/controllers/exportar_actividades_data.php"
entrada: ["post.fin_iso:string", "post.iactividad_val:string", "post.iasistentes_val:string", "post.id_cdc:array", "post.id_tipo_activ:string", "post.inicio_iso:string", "post.isfsv_val:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/pasarela/controller/exportar_select.php"]
casos_uso: ["src\\pasarela\\application\\ExportarActividadesData"]
tags: ["pasarela", "exportar", "actividades", "data"]
estado_revision: "generado"
---

# Exportar Actividades Data

Caso de uso "exportar actividades": dado un filtro (tipo de actividad, periodo y casas), devuelve cabeceras + filas para el listado de exportación, mezclando datos de actividades con las conversiones de pasarela. Devuelve un array serializable por {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/pasarela/exportar_actividades_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/exportar_actividades_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `fin_iso` | `string` | controller+application | No | controller+application |
| `iactividad_val` | `string` | controller+application | No | controller+application |
| `iasistentes_val` | `string` | controller+application | No | controller+application |
| `id_cdc` | `array` | controller+application | No | controller+application |
| `id_tipo_activ` | `string` | controller+application | No | controller+application |
| `inicio_iso` | `string` | controller+application | No | controller+application |
| `isfsv_val` | `string` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\pasarela\application\ExportarActividadesData`

## Frontend Relacionado

- `frontend/pasarela/controller/exportar_select.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.