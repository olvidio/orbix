---
id: "actividadescentro.centros_encargados_data"
tipo: "endpoint"
modulo: "actividadescentro"
url: "/src/actividadescentro/centros_encargados_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadescentro/infrastructure/ui/http/controllers/centros_encargados_data.php"
entrada: ["post.dl_org:string", "post.id_activ:integer", "post.id_tipo_activ:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\actividadescentro\\application\\CentrosEncargadosData"]
tags: ["actividadescentro", "centros", "encargados", "data"]
estado_revision: "generado"
---

# Centros Encargados Data

Endpoint backend: devuelve los centros encargados actuales de una actividad en un array serializable, junto con los flags de permiso.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadescentro/centros_encargados_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadescentro/infrastructure/ui/http/controllers/centros_encargados_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dl_org` | `string` | controller+application | No | controller+application |
| `id_activ` | `integer` | controller+application | No | controller+application |
| `id_tipo_activ` | `string` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\actividadescentro\application\CentrosEncargadosData`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.