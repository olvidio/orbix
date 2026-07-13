---
id: "encargossacd.encargo_horario_select_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/encargo_horario_select_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/encargossacd/infrastructure/ui/http/controllers/encargo_horario_select_data.php"
entrada: ["post.id_enc:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_EncargoHorarioSelectDataData"
respuesta_data: ["desc_enc:string", "filas:list<array{", "id_enc:integer", "id_item_h:integer", "dia_num:string", "dia_ref:string", "mas_menos:string", "dia_inc:string", "h_ini:string", "h_fin:string", "n_sacd:string", "mes:string", "f_ini:?string", "f_fin:?string", "excep:string", "texto_horario:string"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/encargo_horario_select.php"]
casos_uso: ["src\\encargossacd\\application\\EncargoHorarioSelectData"]
tags: ["encargossacd", "encargo", "horario", "select", "data"]
estado_revision: "revisado"
---
# Encargo Horario Select Data

Datos para la lista de horarios de un encargo (`encargo_horario_select`). Se devuelven ya precalculados el texto descriptivo del horario y las fechas formateadas para que el frontend solo arme `frontend\shared\web\Lista`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Datos del listado de horarios de un encargo (`id_enc`) para `encargo_horario_select`.

## Endpoint

- URL: `/src/encargossacd/encargo_horario_select_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/encargo_horario_select_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_enc` | `mixed` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`.
- Claves: filas de horario estructuradas para la `Lista` frontend (doble `JSON.parse`).


## Permisos

Sin control propio; frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\encargossacd\application\EncargoHorarioSelectData`

## Frontend Relacionado

- `frontend/encargossacd/controller/encargo_horario_select.php`

