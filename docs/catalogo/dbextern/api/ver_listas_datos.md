---
id: "dbextern.ver_listas_datos"
tipo: "endpoint"
modulo: "dbextern"
url: "/src/dbextern/ver_listas_datos"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/dbextern/infrastructure/ui/http/controllers/ver_listas_datos.php"
entrada: ["post.dl:string", "post.first_load:boolean", "post.id_nom_bdu:integer", "post.region:string", "post.tipo_persona:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/dbextern/controller/ver_listas.php"]
casos_uso: ["src\\dbextern\\application\\VerListasData"]
tags: ["dbextern", "ver", "listas", "datos"]
estado_revision: "revisado"
---

# Ver Listas Datos

Lista personas de la BDU sin correspondencia en Aquinate (punto 4 del dashboard) o busca posibles
coincidencias Orbix para una persona concreta.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Dos modos según `id_nom_bdu`:

- **`id_nom_bdu` = 0**: devuelve `lista` de personas BDU no unidas. Con `first_load=true` intenta
  `union_automatico` y omite las unidas (incrementa `cont_sync`).
- **`id_nom_bdu` > 0**: devuelve `posibles_misma_dl` y `posibles_otra_dl` (candidatos Orbix).

## Endpoint

- URL: `/src/dbextern/ver_listas_datos`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/ver_listas_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `region` | `string` | controller | Sí | Región actual |
| `dl` | `string` | controller | Sí | DL en nomenclatura listas |
| `tipo_persona` | `string` | controller | Sí | `n`/`a`/`s`/`sssc` |
| `first_load` | `boolean` | controller | No | Solo en carga inicial (`1` activa uniones automáticas) |
| `id_nom_bdu` | `integer` | controller | No | Si > 0, modo búsqueda de coincidencias |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse` en front).
- Modo lista: `lista` (índice → fila con `id_nom_listas`, `ape_nom`, `nombre`, apellidos, `f_nacimiento`),
  `cont_sync`.
- Modo matches: `posibles_misma_dl`, `posibles_otra_dl` (arrays de candidatos Orbix).

## Permisos

- Sin control propio; acceso desde pantalla `ver_listas` abierta desde `sincro_index` (permisos ya
  validados en el bootstrap).

## Casos De Uso

- `src\dbextern\application\VerListasData`

## Frontend Relacionado

- `frontend/dbextern/controller/ver_listas.php` (guarda `lista` en `$_SESSION['DBListas']` en primera carga)
