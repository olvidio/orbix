---
id: "pasarela.exportar_que_actividad_tipo_data"
tipo: "endpoint"
modulo: "pasarela"
url: "/src/pasarela/exportar_que_actividad_tipo_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/pasarela/infrastructure/ui/http/controllers/exportar_que_actividad_tipo_data.php"
entrada:
  - "post.id_tipo_activ:string"
  - "post.sasistentes:string"
  - "post.sactividad:string"
  - "post.snom_tipo:string"
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores:[]
frontend_referencias:
  - "frontend\/pasarela\/controller\/exportar_que.php"
casos_uso: ["src\pasarela\application\ExportarQueActividadTipoData"]
tags: ["pasarela"]
estado_revision: "revisado"
---

# Exportar Que Actividad Tipo Html

Configuración del widget selector de tipo de actividad en «exportar actividades» (`actividad_tipo`); lo pinta el frontend con `ActividadTipo::htmlDesdeConfig()`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Monta `ActividadTipo` con permisos `pffe`/`vcsd`/`calendario` o jefe calendario según sesión.

## Endpoint

- URL: `/src/pasarela/exportar_que_actividad_tipo_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/exportar_que_actividad_tipo_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_activ` | `string` | controller | No | |
| `sasistentes` | `string` | controller | No | |
| `sactividad` | `string` | controller | No | |
| `snom_tipo` | `string` | controller | No | |


## Salida

- Payload: `{actividad_tipo: object}` (`perm_jefe`, `id_tipo_activ`, `sfsv`, `asistentes`, `actividad`, `nom_tipo`, `evitar_procesos`, `sfsv_all`, …).

## Errores conocidos

No devuelve errores `_()` propios (solo validación vacía en mutaciones).

## Permisos

Resuelve permisos en el caso de uso vía `$_SESSION['oPerm']` y `ConfigGlobal::mi_sfsv()`.

## Casos De Uso

- `src\pasarela\application\ExportarQueActividadTipoData`

## Frontend Relacionado

- `frontend/pasarela/controller/exportar_que.php`