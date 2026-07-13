---
id: "misas.update_iniciales"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/update_iniciales"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/update_iniciales.php"
entrada: ["post.id_sacd:integer", "post.iniciales:string", "post.color:string"]
entrada_obligatoria: ["id_sacd"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/ver_iniciales_zona.php"]
casos_uso: ["src\\misas\\application\\UpdateIniciales"]
tags: ["misas", "update", "iniciales"]
estado_revision: "revisado"
errores: ["<repositorio getErrorTxt()>"]
---

# Update iniciales

Inserta o actualiza iniciales y color de un sacerdote en la tabla InicialesSacd.

Linaje: Slice 3 — migrado desde apps/misas/controller/update_iniciales.php.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Inserta o actualiza iniciales y color de un sacerdote en la tabla InicialesSacd.

## Endpoint

- URL: `/src/misas/update_iniciales`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/update_iniciales.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_sacd` | `integer` | application | Si | |
| `iniciales` | `string` | application | No | |
| `color` | `string` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Exito: payload en `data`:
  - `id_sacd`: integer

## Errores conocidos
- `<repositorio getErrorTxt()>`

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `IdNomJefeResolver` (rol p-sacd/jefe calendario), rol ctr/sv/sf en planes y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\misas\application\UpdateIniciales`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/misas/controller/ver_iniciales_zona.php"]`).
