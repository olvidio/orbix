---
id: "misas.cuadricula_update"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/cuadricula_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/cuadricula_update.php"
entrada: ["post.uuid_item:string", "post.key:string", "post.tstart:string", "post.tend:string", "post.observ:string", "post.id_enc:integer", "post.dia:string", "post.tipo_plantilla:string", "post.id_zona:integer"]
entrada_obligatoria: ["uuid_item"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "misas_CuadriculaUpdateData"
respuesta_data: ["error:string, meta: array<string, mixed>"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/support/CuadriculaZonaRenderer.php"]
casos_uso: ["src\\misas\\application\\CuadriculaUpdate"]
tags: ["misas", "cuadricula", "update"]
estado_revision: "revisado"
errores: ["Falta el id_item", "Este día tiene más de dos Misas", "Este día tiene dos Misas", "Este día no tiene ninguna Misa", "Tiene dos Misas a primera hora", "No está en la zona y tiene Misa a primera hora", "Está en ", "<repositorio getErrorTxt()>"]
---

# Cuadricula update

Asigna, actualiza o borra un EncargoDia en una celda de la cuadrícula y recalcula metadatos de color/texto para la fila SACD y la celda misa.

Linaje: Slice 6a — migrado desde apps/misas/controller/cuadricula_update.php.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Asigna, actualiza o borra un EncargoDia en una celda de la cuadrícula y recalcula metadatos de color/texto para la fila SACD y la celda misa.

## Endpoint

- URL: `/src/misas/cuadricula_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/cuadricula_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `uuid_item` | `string` | application | Si | UUID del `EncargoDia`. Si la celda estaba vacía, el front genera uno nuevo. |
| `key` | `string` | application | No | `iniciales#id_nom` (p. ej. `JP#1234`). Vacío → elimina la asignación. El backend usa la 2ª parte como `id_nom`. |
| `tstart` | `string` | application | No | Hora inicio, p. ej. `08:00` (puede vacío). |
| `tend` | `string` | application | No | Hora fin, p. ej. `08:30`. |
| `observ` | `string` | application | No | Observaciones; la UI marca `*` en iniciales si no vacío. |
| `id_enc` | `integer` | application | No | Id del encargo (fila de misa). |
| `dia` | `string` | application | No | Día ISO `Y-m-d` (p. ej. `2026-08-10`). |
| `tipo_plantilla` | `string` | application | No | `p` (plan) o plantilla `s1`/`s3`/`d1`/`d3`/`m1`/`m3`. Condiciona colores de estado. |
| `id_zona` | `integer` | application | No | Zona de la cuadrícula. |

### Ejemplo POST (form-urlencoded, como la web)

```
uuid_item=a1b2c3d4-e5f6-7890-abcd-ef1234567890
&key=JP%231234
&id_enc=42
&dia=2026-08-10
&tstart=08%3A00
&tend=08%3A30
&observ=con%20organista
&tipo_plantilla=p
&id_zona=3
```

Origen web: `modificar_cuadricula_zona.phtml` → `commitCurrentEdit`. Flujo narrativo: [`flujos/cuadricula.md`](../flujos/cuadricula.md).

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Exito: payload en `data`:
  - `meta`: {"color_misa":"string","id_sacd_anterior":"int|null","texto_anterior":"string","color_fondo_anterior":"string","texto_sacd_anterior":"string","texto":"string","color_fondo":"string","texto_sacd":"string","comprobacion":"string"}

## Errores conocidos
- `Falta el id_item`
- `Este día tiene más de dos Misas`
- `Este día tiene dos Misas`
- `Este día no tiene ninguna Misa`
- `Tiene dos Misas a primera hora`
- `No está en la zona y tiene Misa a primera hora`
- `Está en `
- `<repositorio getErrorTxt()>`

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `IdNomJefeResolver` (rol p-sacd/jefe calendario), rol ctr/sv/sf en planes y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\misas\application\CuadriculaUpdate`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/misas/support/CuadriculaZonaRenderer.php"]`).
