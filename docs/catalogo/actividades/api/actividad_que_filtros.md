---
id: "actividades.actividad_que_filtros"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_que_filtros"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_que_filtros.php"
entrada: ["post.dl_org:string", "post.filtro_lugar:string", "post.id_ubi:integer", "post.modo:string", "post.publicado:integer", "post.sfsv:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/actividades/controller/actividad_que.php"]
casos_uso: ["src\\actividades\\application\\ActividadQueFiltrosBloque"]
tags: ["actividades", "actividad", "que", "filtros"]
estado_revision: "generado"
---

# Actividad Que Filtros

Genera el HTML del bloque "filtros extra" (filtro_lugar + lugar + organiza + publicada) en la pantalla `actividad_que`. El bloque solo se muestra a usuarios con permiso de control (`perm_ctr`); para el resto devuelve cadena vacia. Encapsula todos los accesos a repositorios y entidades de dominio necesarios (`Role`, `DelegacionDropdown`, `ActividadLugar`) de forma que el frontend controller no tenga que depender directamente de `src/`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividades/actividad_que_filtros`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_que_filtros.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dl_org` | `string` | controller | No | controller |
| `filtro_lugar` | `string` | controller | No | controller |
| `id_ubi` | `integer` | controller | No | controller |
| `modo` | `string` | controller | No | controller |
| `publicado` | `integer` | controller | No | controller |
| `sfsv` | `integer` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\actividades\application\ActividadQueFiltrosBloque`

## Frontend Relacionado

- `frontend/actividades/controller/actividad_que.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.