---
id: "misas.importar_plantilla_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/importar_plantilla_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/importar_plantilla_data.php"
entrada: ["post.id_zona:integer", "post.tipo_plantilla_origen:string", "post.tipo_plantilla_destino:string"]
entrada_obligatoria: ["id_zona", "tipo_plantilla_origen", "tipo_plantilla_destino"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/importar_plantilla.php"]
casos_uso: ["src\\misas\\application\\ImportarPlantillaData", "src\\misas\\application\\support\\MisasBuildInput"]
tags: ["misas", "importar", "plantilla", "data"]
estado_revision: "revisado"
errores: ["solo deberia haber uno", "<repositorio getErrorTxt() acumulado>"]
---

# Importar plantilla Data

Copia asignaciones de plantilla origen a destino para una zona, creando/actualizando EncargoDia en el rango correspondiente.

Linaje: Slice 9 — migrado desde apps/misas/controller/importar_plantilla.php.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Copia asignaciones de plantilla origen a destino para una zona, creando/actualizando EncargoDia en el rango correspondiente.

## Endpoint

- URL: `/src/misas/importar_plantilla_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/importar_plantilla_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_zona` | `integer` | application | Si | Zona. |
| `tipo_plantilla_origen` | `string` | application | Si | Select «Importar de la plantilla» (`#importar_de_plantilla`), p. ej. `s1`. |
| `tipo_plantilla_destino` | `string` | application | Si | Plantilla que se está editando (`#tipo_plantilla`), p. ej. `d1`. |

### Flujo web (botón importar)

`modificar_plantilla.phtml` → `fnjs_importar_de_plantilla_zona()` → `importar_plantilla.php` → este endpoint. Copia asignaciones origen→destino (borra destino en su rango de fechas de plantilla). La UI deshabilita orígenes con la misma letra inicial (`s`/`d`/`m`) que el destino.

Ejemplo POST:

```
id_zona=3&tipo_plantilla_origen=s1&tipo_plantilla_destino=d1
```

Flujo narrativo: [`flujos/cuadricula.md`](../flujos/cuadricula.md).

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Exito: payload en `data`:
  - `success`: boolean

## Errores conocidos
- `solo deberia haber uno`
- `<repositorio getErrorTxt() acumulado>`

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `IdNomJefeResolver` (rol p-sacd/jefe calendario), rol ctr/sv/sf en planes y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\misas\application\ImportarPlantillaData`
- `src\misas\application\support\MisasBuildInput`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/misas/controller/importar_plantilla.php"]`).
