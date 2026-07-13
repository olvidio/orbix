---
id: "actividadescentro.activ_ctr_shell_data"
tipo: "endpoint"
modulo: "actividadescentro"
url: "/src/actividadescentro/activ_ctr_shell_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/actividadescentro/infrastructure/ui/http/controllers/activ_ctr_shell_data.php"
entrada: ["post.periodo:string", "post.tipo:string", "post.year:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadescentro_ActivCtrShellDataData"
respuesta_data: ["tipo:string", "url_lista:array", "url_encargados:array", "url_disponibles:array", "url_asignar:array", "url_reordenar:array", "url_eliminar:array"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadescentro/controller/activ_ctr.php"]
casos_uso: ["src\\actividadescentro\\application\\ActivCtrShellData"]
tags: ["actividadescentro", "activ", "ctr", "shell", "data"]
estado_revision: "revisado"
---

# Activ Ctr Shell Data

Resuelve el `tipo` efectivo y devuelve las especificaciones de URL (`path` + `campos_form`) de los
demás endpoints del módulo, para que la shell frontend `activ_ctr` monte los enlaces AJAX. La firma
`HashFront::linkSinVal()` se aplica en el controller frontend `frontend\actividadescentro\controller\activ_ctr`,
no en `src/`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Es el bootstrap de la pantalla: no toca base de datos. Ajusta el `tipo` cuando el usuario opera en el
semestre de formación (`ConfigGlobal::mi_sfsv() === 2`: `sg`→`sfsg`, `sr`→`sfsr`, `nagd`→`sfnagd`) y
emite, por cada acción (listar, encargados, disponibles, asignar, reordenar, eliminar), el `path` del
endpoint y la lista `campos_form` que la firma `HashFront` debe cubrir.

## Endpoint

- URL: `/src/actividadescentro/activ_ctr_shell_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/actividadescentro/infrastructure/ui/http/controllers/activ_ctr_shell_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `tipo` | `string` | controller+application | No | Colectivo; puede remaparse a `sf*` según `mi_sfsv()` |
| `year` | `string` | controller | No | Se propaga a la shell (no se usa en `build`) |
| `periodo` | `string` | controller | No | Se propaga a la shell (no se usa en `build`) |

El controller construye el `$input` con los tres campos vía `FuncTablasSupport::inputString`.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Payload en `data` (schema `actividadescentro_ActivCtrShellDataData`):
  - `tipo` (`string`): tipo efectivo tras el remapeo `sf*`.
  - `url_lista`, `url_encargados`, `url_disponibles`, `url_asignar`, `url_reordenar`, `url_eliminar`
    (`array`): cada uno con `path` (ruta del endpoint) y `campos_form` (campos separados por `!` que
    firma `HashFront`).

## Permisos

- El caso de uso no aplica control de permisos propio: solo compone rutas. La autorización real la
  ejerce cada endpoint destino y se resuelve en frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadescentro\application\ActivCtrShellData`

## Frontend Relacionado

- `frontend/actividadescentro/controller/activ_ctr.php`: llama a este endpoint vía
  `PostRequest::getDataFromUrl`, firma cada `url_*` con `HashFront::linkSinVal()` y las expone a la
  vista `activ_ctr.phtml` como variables `URL_LISTA`, `URL_ENCARGADOS`, etc.
