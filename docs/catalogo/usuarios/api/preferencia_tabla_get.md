---
id: "usuarios.preferencia_tabla_get"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/preferencia_tabla_get"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/preferencia_tabla_get.php"
entrada: ["post.id_tabla:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/shared/web/Lista.php", "frontend/shared/web/TablaEditable.php"]
casos_uso: ["src\\usuarios\\application\\PreferenciaTablaData"]
tags: ["usuarios", "preferencia", "tabla", "get"]
estado_revision: "generado"
---

# Preferencia Tabla Get

Devuelve las preferencias de usuario necesarias para renderizar una tabla (HTML simple o SlickGrid) en el front. Entrada: - `id_tabla` (opcional): identificador del grid. Si viene vacío, no se devolverán preferencias específicas del grid (útil cuando sólo se necesita saber si el usuario prefiere HTML o SlickGrid). Salida: array asociativo con la forma: [ 'formato_tabla' => ''|'html'|'slickgrid', // prefs 'tabla_presentacion' 'slickgrid' => null|array, // prefs 'slickGrid_<id_tabla>_<idioma>' ] Para slickgrid se busca primero la preferencia del usuario actual; si no existe, se usa la del usuario 44 (default).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/usuarios/preferencia_tabla_get`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/preferencia_tabla_get.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tabla` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\usuarios\application\PreferenciaTablaData`

## Frontend Relacionado

- `frontend/shared/web/Lista.php`
- `frontend/shared/web/TablaEditable.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.