---
id: "actividadescentro.centros_disponibles_data"
tipo: "endpoint"
modulo: "actividadescentro"
url: "/src/actividadescentro/centros_disponibles_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/actividadescentro/infrastructure/ui/http/controllers/centros_disponibles_data.php"
entrada: ["post.f_ini_act:string", "post.fin:string", "post.id_activ:integer", "post.inicio:string", "post.tipo:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadescentro_CentrosDisponiblesDataData"
respuesta_data: ["tipo:string", "id_activ:integer", "centros:list<array<string, mixed>>"]
requiere_hashb: false
errores: ["tipo no valido"]
frontend_referencias: ["frontend/actividadescentro/controller/activ_ctr.php"]
casos_uso: ["src\\actividadescentro\\application\\CentrosDisponiblesData"]
tags: ["actividadescentro", "centros", "disponibles", "data"]
estado_revision: "revisado"
---

# Centros Disponibles Data

Devuelve los centros candidatos para asignar como encargados de una actividad, filtrados por `tipo`
(`sg` / `sr` / `nagd` / `sssc` / `sfsg` / `sfsr` / `sfnagd`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Sucesor de las ramas `nuevo_*` del dispatcher legacy `activ_ctr_ajax.php` (agrupadas en un único caso
de uso con parámetro `tipo`, por la excepción de `refactor.md` para dispatchers de lectura). Según el
`tipo` elige repositorio (`CentroDl` o `CentroEllas` para variantes `sf*`) y aplica un filtro sobre
`tipo_ctr` / `tipo_labor`. Para `tipo=sg` enriquece cada centro con el número de actividades en el
periodo `[inicio, fin]` y la diferencia de días con la actividad más próxima del centro respecto a
`f_ini_act`.

## Endpoint

- URL: `/src/actividadescentro/centros_disponibles_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/actividadescentro/infrastructure/ui/http/controllers/centros_disponibles_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `tipo` | `string` | controller+application | No | Debe estar en `TIPOS_VALIDOS`; si no, devuelve `error` y `centros: []` |
| `id_activ` | `integer` | controller+application | No | Actividad destino (se devuelve en el payload) |
| `inicio` | `string` | controller+application | No | ISO; solo `tipo=sg`, para contar actividades del periodo |
| `fin` | `string` | controller+application | No | ISO; solo `tipo=sg` |
| `f_ini_act` | `string` | controller+application | No | Fecha local; solo `tipo=sg`, para calcular `dif_dias` |

El controller construye el `$input` con los cinco campos (`inputString`/`inputInt`).

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Payload en `data` (schema `actividadescentro_CentrosDisponiblesDataData`):
  - `tipo` (`string`): tipo recibido.
  - `id_activ` (`integer`): actividad destino.
  - `centros` (`list<array<string, mixed>>`): cada centro con `id_ubi` (int) y `nombre_ubi` (string);
    para `tipo=sg` además `num_actividades_periodo` (int) y `dif_dias` (string).
- Si `tipo` no es válido, el payload es `{tipo, id_activ, centros: [], error: "tipo no valido"}`
  (el error viaja como clave del payload, no como `mensaje` del envelope).

## Errores conocidos

- `tipo no valido` (clave `error` dentro del payload, no rompe el envelope `success: true`).

## Permisos

- El caso de uso no aplica control de permisos propio; la autorización de oficina se resuelve en el
  frontend (`activ_ctr.php`) y en `$_SESSION['oPerm']`. La celda "nuevo" solo se muestra si la fila
  del listado trae `perm_crear_ctr === true`.

## Casos De Uso

- `src\actividadescentro\application\CentrosDisponiblesData`

## Frontend Relacionado

- `frontend/actividadescentro/controller/activ_ctr.php` (vista `activ_ctr.phtml`): la función
  `fnjs_nuevo_ctr` invoca este endpoint (URL firmada `url_disponibles`) y pinta el desplegable de
  candidatos con `fnjs_construir_tabla_disponibles` (columnas extra `num` / `dif días` cuando
  `tipo === 'sg'`).
