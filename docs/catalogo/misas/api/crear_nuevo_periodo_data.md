---
id: "misas.crear_nuevo_periodo_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/crear_nuevo_periodo_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/crear_nuevo_periodo_data.php"
entrada: ["post.id_zona:integer", "post.tipo_plantilla:string", "post.seleccion:integer", "post.periodo:string", "post.empiezamin:string", "post.empiezamax:string", "post.orden:string"]
entrada_obligatoria: ["id_zona", "tipo_plantilla", "periodo"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/crear_nuevo_periodo.php"]
casos_uso: ["src\\misas\\application\\CrearNuevoPeriodoData", "src\\misas\\application\\support\\MisasBuildInput"]
tags: ["misas", "crear", "nuevo", "periodo", "data"]
estado_revision: "revisado"
errores: ["solo deberia haber uno", "<repositorio getErrorTxt() acumulado en error_txt>"]
---

# Crear nuevo periodo Data

Crea asignaciones EncargoDia para un nuevo periodo de plan de misas a partir de plantilla y devuelve el payload de cuadrícula para renderizar ver_cuadricula_zona.phtml.

Linaje: Slice 8 — lógica extraída de apps/misas/controller/crear_nuevo_periodo.php.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Crea asignaciones EncargoDia para un nuevo periodo de plan de misas a partir de plantilla y devuelve el payload de cuadrícula para renderizar ver_cuadricula_zona.phtml.

## Endpoint

- URL: `/src/misas/crear_nuevo_periodo_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/crear_nuevo_periodo_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_zona` | `integer` | application | Si | Zona. |
| `tipo_plantilla` | `string` | application | Si | Plantilla origen: `s1`/`s3`/`d1`/`d3`/`m1`/`m3`. En el form web el campo se llama `tipoplantilla` y el controller lo mapea. |
| `seleccion` | `integer` | application | No | Filtro sacd (bitmask); suele ir 0. |
| `periodo` | `string` | application | Si | `proxima_semana` \| `proximo_mes` \| `otro`. |
| `empiezamin` | `string` | application | No | Inicio si `otro` (web: `dd/mm/yyyy`; se convierte a ISO). |
| `empiezamax` | `string` | application | No | Fin si `otro`. |
| `orden` | `string` | application | No | Default `desc_enc`. |

### Flujo web (botón preparar)

`preparar_plan_de_misas.phtml` → `fnjs_nuevo_periodo()` → `crear_nuevo_periodo.php` → este endpoint. **Borra** los `EncargoDia` del periodo en la zona y los recrea desde la plantilla. Luego el front recarga la cuadrícula con `tipo_plantilla=p`.

Ejemplo POST:

```
id_zona=3&tipo_plantilla=s1&periodo=proxima_semana&orden=desc_enc&empiezamin=&empiezamax=&seleccion=0
```

Flujo narrativo: [`flujos/cuadricula.md`](../flujos/cuadricula.md).

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Exito: payload en `data`:
  - `columns_cuadricula`: string (JSON)
  - `data_cuadricula`: array
  - `id_zona`: integer
  - `tipo_plantilla`: string
  - `orden`: string
  - `seleccion`: integer
  - `periodo`: string
  - `empieza_min`: string
  - `empieza_max`: string

## Errores conocidos
- `solo deberia haber uno`
- `<repositorio getErrorTxt() acumulado en error_txt>`

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `IdNomJefeResolver` (rol p-sacd/jefe calendario), rol ctr/sv/sf en planes y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\misas\application\CrearNuevoPeriodoData`
- `src\misas\application\support\MisasBuildInput`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/misas/controller/crear_nuevo_periodo.php"]`).
