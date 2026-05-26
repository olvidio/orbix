---
id: "menus.grupmenu_coleccion"
tipo: "endpoint"
modulo: "menus"
url: "/src/menus/grupmenu_coleccion"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/menus/infrastructure/ui/http/controllers/grupmenu_coleccion.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_nested_data"
respuesta_data: ["a_valores:object"]
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\menus\\application\\GrupMenuColeccionUseCase", "src\\menus\\application\\MenusVisiblesPorGrupoMenuUseCase"]
tags: ["menus", "grupmenu", "coleccion", "cliente_movil"]
estado_revision: "revisado"
---

# Grupmenu Coleccion

Lista los **grupmenus** visibles para el usuario de la sesión y, dentro de cada uno, las entradas de menú (mismo criterio que el menú lateral ☰ de la web).

Convenciones: [`_convenciones_api.md`](../_convenciones_api.md) · Clientes nativos: [`_clientes_nativos.md`](../_clientes_nativos.md)

## Endpoint

- URL: `/src/menus/grupmenu_coleccion`
- Métodos: `GET` o `POST` (sin cuerpo)
- Controller: `src/menus/infrastructure/ui/http/controllers/grupmenu_coleccion.php`
- Requiere sesión válida (`PHPSESSID`)

## Salida

- Helper: **`ContestarJson::enviarDataAnidado`** — `data` es un **objeto JSON nativo** (no string escapado).
- Forma: `{ "success": true, "data": { "a_valores": { … } } }`

### Estructura `data.a_valores`

Objeto indexado numéricamente (`"1"`, `"2"`, …). Cada entrada:

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `sel` | int | ID del grupmenu (`id_grupmenu`) |
| `grupmenu` | string | Etiqueta del apartado (p. ej. `exterior`) |
| `orden` | int | Orden de presentación |
| `menus` | array | Submenús visibles |

Cada elemento de `menus[]`:

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `menu` | string | Texto visible |
| `url` | string | Ruta frontend, p. ej. `frontend/misas/controller/ver_plan_de_misas.php` |
| `orden` | int[] | Jerarquía para indentar (campo `orden` en BD) |
| `id_menu` | int | ID opcional del menú |

Entradas con `url` vacía actúan como **sección** (cabecera) en la UI.

## Errores habituales

| Síntoma | Causa |
|---------|--------|
| `success: false`, `data` con `auth_required` | Cookie no enviada o sesión caducada |
| `a_valores` vacío | Usuario sin grupmenus asignados al rol |
| Lista parseada vacía con HTTP 200 | Todas las filas tienen `grupmenu` vacío |

## Ejemplo

**Request:**

```http
GET /orbix/src/menus/grupmenu_coleccion HTTP/1.1
Accept: application/json
Cookie: PHPSESSID=...
```

**Response (fragmento):**

```json
{
  "success": true,
  "data": {
    "a_valores": {
      "1": {
        "sel": 3,
        "grupmenu": "exterior",
        "orden": 10,
        "menus": [
          {
            "menu": "Plan Misas",
            "url": "frontend/misas/controller/misas_index.php",
            "orden": [1],
            "id_menu": 101
          },
          {
            "menu": "Misas",
            "url": "",
            "orden": [2]
          },
          {
            "menu": "Ver plan zona",
            "url": "frontend/misas/controller/ver_plan_de_misas.php",
            "orden": [2, 1],
            "id_menu": 102
          }
        ]
      }
    }
  }
}
```

## Casos de uso

- `src\menus\application\GrupMenuColeccionUseCase`
- `src\menus\application\MenusVisiblesPorGrupoMenuUseCase`

## Cliente de referencia

- `orbix-android`: `fetchGrupMenuColeccionDetailed()` — acepta `data` como objeto o string.

## Nota para PostRequest (web)

La web usa `PostRequest`, que espera `data` como string. Este endpoint usa `enviarDataAnidado` precisamente para clientes nativos; no mezclar el parser web sin adaptarlo.
