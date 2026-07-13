---
id: "notas.tessera_copiar"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/tessera_copiar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/notas/infrastructure/ui/http/controllers/tessera_copiar.php"
entrada: ["post.id_nom_dst:integer", "post.id_nom_org:integer"]
entrada_obligatoria: ["id_nom_org", "id_nom_dst"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No se han recibido las personas de origen y destino", "no se ha guardado la nota"]
frontend_referencias: ["frontend/notas/controller/tessera_copiar_select.php", "frontend/notas/view/tessera_copiar_select.phtml"]
casos_uso: ["src\\notas\\application\\TesseraCopiar"]
tags: ["notas", "tessera", "copiar"]
estado_revision: "revisado"
---

# Tessera Copiar

Copia las notas/tessera de una persona origen a otra destino (mismo apellido).

Copia todas las `PersonaNota` de una persona origen hacia una persona destino. Utilizado por `personas_select.phtml` (pagina de traslado de tessera entre numerarios / supernumerarios). Devuelve una cadena con los errores (separados por `<br>`) o vacia si todo ha ido bien.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/tessera_copiar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/notas/infrastructure/ui/http/controllers/tessera_copiar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_nom_dst` | `integer` | application | No | application |
| `id_nom_org` | `integer` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Éxito: `success: true`, `data: "ok"`. Error en `mensaje`.

## Objetivo funcional

Replica notas de `id_nom_org` a `id_nom_dst` tras selección en `tessera_copiar_select`.

## Permisos

- Dossier tessera; frontend + `$_SESSION['oPerm']`.

## Errores conocidos

- `No se han recibido las personas de origen y destino`
- `no se ha guardado la nota`

## Casos De Uso

- `src\notas\application\TesseraCopiar`

## Frontend Relacionado

- `frontend/notas/controller/tessera_copiar_select.php`.