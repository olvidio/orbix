---
id: "usuarios.grupo_lista"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/grupo_lista"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/usuarios/infrastructure/ui/http/controllers/grupo_lista.php"
entrada: ["post.username:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "usuarios_GruposListaData"
respuesta_data: ["a_cabeceras:list<mixed>, a_botones: list<array<string, string>>, a_valores: array<int, array<int|string, mixed>>"]
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/controller/grupo_lista.php"]
casos_uso: ["src\\usuarios\\application\\GruposLista"]
tags: ["usuarios", "grupo", "lista"]
estado_revision: "revisado"
errores: ["Usuario no encontrado", "no tiene permisos para ver esto"]
---

# Grupo Lista

Lista grupos de permisos (id_usuario ~ ^5) con filtro opcional por nombre.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Lista grupos de permisos (id_usuario ~ ^5) con filtro opcional por nombre.

## Endpoint

- URL: `/src/usuarios/grupo_lista`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/grupo_lista.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `username` | `string` | application | No | |

## Salida

- Helper: `ContestarJson::enviar` / `ContestarJson::send` (según endpoint).
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse` salvo JsonResponse directo):
  - `a_cabeceras`: cabeceras tabla
  - `a_botones`: botones
  - `a_valores`: filas con sel id# y link editar

## Errores conocidos
- `Usuario no encontrado`
- `no tiene permisos para ver esto`

## Permisos

id_role≤3; si >3 devuelve error de permisos.

## Casos De Uso

- `src\usuarios\application\GruposLista`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/usuarios/controller/grupo_lista.php"]`).
