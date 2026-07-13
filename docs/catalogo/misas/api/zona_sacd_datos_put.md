---
id: "misas.zona_sacd_datos_put"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/zona_sacd_datos_put"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/zona_sacd_datos_put.php"
entrada: ["post.id_zona:integer", "post.id_sacd:integer", "post.propia:string", "post.dw1:string", "post.dw2:string", "post.dw3:string", "post.dw4:string", "post.dw5:string", "post.dw6:string", "post.dw7:string"]
entrada_obligatoria: ["id_zona", "id_sacd"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "misas_ZonaSacdDatosPutData"
respuesta_data: ["error:string"]
requiere_hashb: false
frontend_referencias: ["frontend/zonassacd/controller/zona_sacd.php"]
casos_uso: ["src\\misas\\application\\ZonaSacdDatosPut"]
tags: ["misas", "zona", "sacd", "datos", "put"]
estado_revision: "revisado"
errores: ["No existe", "<repositorio getErrorTxt()>"]
---

# Zona sacd datos put

Guarda flags de disponibilidad semanal de un SACD en una zona (ZonaSacd).

Linaje: Slice 10 — migrado desde apps/misas/controller/zona_sacd_datos_put.php.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Guarda flags de disponibilidad semanal de un SACD en una zona (ZonaSacd).

## Endpoint

- URL: `/src/misas/zona_sacd_datos_put`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/zona_sacd_datos_put.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_zona` | `integer` | application | Si | |
| `id_sacd` | `integer` | application | Si | |
| `propia` | `string→boolean` | application | No | |
| `dw1` | `string→boolean` | application | No | |
| `dw2` | `string→boolean` | application | No | |
| `dw3` | `string→boolean` | application | No | |
| `dw4` | `string→boolean` | application | No | |
| `dw5` | `string→boolean` | application | No | |
| `dw6` | `string→boolean` | application | No | |
| `dw7` | `string→boolean` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"` (string vacio serializado).

## Errores conocidos
- `No existe`
- `<repositorio getErrorTxt()>`

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `IdNomJefeResolver` (rol p-sacd/jefe calendario), rol ctr/sv/sf en planes y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\misas\application\ZonaSacdDatosPut`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/zonassacd/controller/zona_sacd.php"]`).
