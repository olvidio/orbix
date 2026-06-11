---
id: "actividades.actividad_status_labels_datos"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_status_labels_datos"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_status_labels_datos.php"
entrada: ["post.with_all:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividades_ActividadStatusLabelsDatosData"
respuesta_data: ["id_to_label:array<int,string>"]
requiere_hashb: false
frontend_referencias: ["frontend/actividades/controller/actividad_ver.php", "frontend/actividades/controller/planning_casa_modificar.php", "frontend/actividades/controller/planning_casa_nueva.php"]
casos_uso: ["src\\actividades\\application\\ActividadStatusLabelsDatos"]
tags: ["actividades", "actividad", "status", "labels", "datos"]
estado_revision: "revisado"
---

# Actividad Status Labels Datos

Devuelve las etiquetas traducidas de los estados de actividad
(`StatusId::getArrayStatus`) para los formularios de ficha:

```json
{ "id_to_label": { "1": "proyecto", "2": "actual", "3": "terminada", "4": "borrable" } }
```

Con `with_all='t'` añade `"9": "cualquiera"` (modo editar; en modo nuevo se pide
sin `with_all`).

## Endpoint

- URL: `/src/actividades/actividad_status_labels_datos`
- Metodos registrados: `GET, POST` (solo lee POST)
- Operacion: `consulta` (sin efectos, sin permisos)
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_status_labels_datos.php`

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Entrada

| Campo | Tipo | Obligatorio | Notas |
|-------|------|-------------|-------|
| `with_all` | `string` | No | `'t'` ⇒ incluye `9 = cualquiera`. |

## Casos De Uso

- `src\actividades\application\ActividadStatusLabelsDatos`

## Frontend Relacionado

- `frontend/actividades/controller/actividad_ver.php`
- `frontend/actividades/controller/planning_casa_modificar.php`
- `frontend/actividades/controller/planning_casa_nueva.php`

## Revision Manual

- Revisado jun 2026: forma de `data` y semantica de `with_all` verificadas
  contra `StatusId::getArrayStatus`.
