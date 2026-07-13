---
id: "inventario.equipajes_nuevo_guardar"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/equipajes_nuevo_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/inventario/infrastructure/ui/http/controllers/equipajes_nuevo_guardar.php"
entrada: ["post.id_ubi_activ:integer", "post.nom_equipaje:string", "post.ids_activ:string", "post.f_ini:string", "post.f_fin:string", "post.lugar:string"]
entrada_obligatoria: ["nom_equipaje"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["hay un error, no se ha guardado"]
frontend_referencias: ["frontend/inventario/controller/equipajes_nuevo.php"]
casos_uso: []
tags: ["inventario", "equipajes", "nuevo", "guardar"]
estado_revision: "revisado"
---

# Crear equipaje

Alta de equipaje con nombre, fechas, lugar, ubi de actividades e ids de actividades seleccionadas.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Alta de equipaje con nombre, fechas, lugar, ubi de actividades e ids de actividades seleccionadas.

## Endpoint

- URL: `/src/inventario/equipajes_nuevo_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/inventario/infrastructure/ui/http/controllers/equipajes_nuevo_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ubi_activ` | `integer` | POST | No | |
| `nom_equipaje` | `string` | POST | Si | |
| `ids_activ` | `string` | POST | No | |
| `f_ini` | `string` | POST | No | |
| `f_fin` | `string` | POST | No | |
| `lugar` | `string` | POST | No | |


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

- `frontend/inventario/controller/equipajes_nuevo.php`
