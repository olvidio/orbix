---
id: "inventario.equipajes_texto_listado_guardar"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/equipajes_texto_listado_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/inventario/infrastructure/ui/http/controllers/equipajes_texto_listado_guardar.php"
entrada: ["post.texto:string", "post.loc:string", "post.id_equipaje:integer"]
entrada_obligatoria: ["loc", "id_equipaje"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["hay un error, no se ha guardado"]
frontend_referencias: ["frontend/inventario/controller/equipajes_form_texto_listado.php"]
casos_uso: []
tags: ["inventario", "equipajes", "texto", "listado", "guardar"]
estado_revision: "revisado"
---

# Guardar textos de listado en equipaje

Persiste textos editables (cabecera, pie, listado por grupo) según `loc` (`cabecera`, `pie`, `docs_grupo_{id}`…).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Persiste textos editables (cabecera, pie, listado por grupo) según `loc` (`cabecera`, `pie`, `docs_grupo_{id}`…).

## Endpoint

- URL: `/src/inventario/equipajes_texto_listado_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/inventario/infrastructure/ui/http/controllers/equipajes_texto_listado_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `texto` | `string` | POST | No | |
| `loc` | `string` | POST | Si | |
| `id_equipaje` | `integer` | POST | Si | |


## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Éxito: `data: "ok"`.

## Errores conocidos

  - `hay un error, no se ha guardado`

## Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- Lógica inline en controller (sin `application/`).

## Frontend Relacionado

- `frontend/inventario/controller/equipajes_form_texto_listado.php`
