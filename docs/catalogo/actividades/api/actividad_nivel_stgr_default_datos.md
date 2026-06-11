---
id: "actividades.actividad_nivel_stgr_default_datos"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_nivel_stgr_default_datos"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_nivel_stgr_default_datos.php"
entrada: ["post.id_tipo_activ:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividades_ActividadNivelStgrDefaultData"
respuesta_data: ["nivel_stgr_default:integer"]
requiere_hashb: false
frontend_referencias: ["frontend/actividades/controller/actividad_ver.php"]
casos_uso: ["src\\actividades\\application\\ActividadVerDatos"]
tags: ["actividades", "actividad", "nivel", "stgr", "default", "datos"]
estado_revision: "revisado"
---

# Actividad Nivel Stgr Default Datos

Devuelve el nivel STGR por defecto para un tipo de actividad
(`ActividadVerDatos::nivelStgrPorDefectoParaIdTipoActividad`):

```json
{ "nivel_stgr_default": 9 }
```

Regla (sobre el texto de actividad de 2 digitos del tipo, parsing extendido):

| Texto del tipo contiene | Nivel | Valor |
|------------------------|-------|-------|
| `est` | Cuadrienio Año I (`C1`) | 2 |
| `repaso` | Repaso (`R`) | 4 |
| `semestre` | Cuadrienio Año I (`C1`) | 2 |
| (resto / vacio) | sin estudios (`N`) | 9 |

Mismo calculo que `salida=nivel_stgr_defecto` de
[`actividad_tipo_get`](actividad_tipo_get.md) (este endpoint usa `id_tipo_activ`
como nombre de parametro; aquel usa `entrada`).

## Endpoint

- URL: `/src/actividades/actividad_nivel_stgr_default_datos`
- Metodos registrados: `GET, POST` (solo lee POST)
- Operacion: `consulta` (sin efectos, sin permisos, sin acceso a BD: regla estatica)
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_nivel_stgr_default_datos.php`

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Entrada

| Campo | Tipo | Obligatorio | Notas |
|-------|------|-------------|-------|
| `id_tipo_activ` | `string` | No | Tipo completo o parcial (admite `ca-repaso`, `2[789]...`, etc.). Vacio ⇒ 9. |

## Casos De Uso

- `src\actividades\application\ActividadVerDatos` (metodo estatico)

## Frontend Relacionado

- `frontend/actividades/controller/actividad_ver.php` (modo nuevo). El JS de la
  cascada usa la salida equivalente de `actividad_tipo_get`
  (`fnjs_actualizar_nivel_stgr`).

## Revision Manual

- Revisado jun 2026: regla y forma de `data` verificadas contra
  `nivelStgrPorDefectoParaIdTipoActividad` y `NivelStgrId`.
