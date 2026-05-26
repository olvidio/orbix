---
id: "actividadessacd.comunicacion_activ_sacd_data"
tipo: "endpoint"
modulo: "actividadessacd"
url: "/src/actividadessacd/comunicacion_activ_sacd_data"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/actividadessacd/infrastructure/ui/http/controllers/comunicacion_activ_sacd_data.php"
entrada: ["post.empiezamax:string", "post.empiezamin:string", "post.id_nom:integer", "post.periodo:string", "post.propuesta:string", "post.que:string", "post.sacd:string", "post.sel:mixed", "post.year:string"]
entrada_obligatoria: ["periodo", "year"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadessacd_ComunicacionActividadesSacdDataData"
respuesta_data: ["que:string", "propuesta:string", "mi_dele:string", "lugar_fecha:string", "periodo_txt:string", "mensaje_periodo?:string", "sacds:array", "sacds_paso:array"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadessacd/controller/com_sacd_activ_periodo.php", "frontend/actividadessacd/view/com_sacd_activ_periodo.phtml"]
casos_uso: ["src\\actividadessacd\\application\\ComunicacionActividadesSacdData"]
tags: ["actividadessacd", "comunicacion", "activ", "sacd", "data", "cliente_movil"]
estado_revision: "revisado"
---

# Comunicacion Activ Sacd Data

Listado de **atención de actividades** para comunicar a los SACD (principal + «sacd de paso» cuando aplica). Equivalente al botón «buscar» de la web.

Convenciones: [`_convenciones_api.md`](../_convenciones_api.md) · Pantalla: `com_sacd_activ_periodo.php`

## Endpoint

- URL: `/src/actividadessacd/comunicacion_activ_sacd_data`
- Métodos: `POST`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/comunicacion_activ_sacd_data.php`
- Pasa `$_POST` completo al caso de uso.

## Entrada

| Campo | Tipo | Obligatorio | Notas |
|-------|------|-------------|-------|
| `periodo` | string | **Sí** | Alias de rango (tabla abajo) |
| `year` | string/int | **Sí** | Año de referencia (`Periodo::setAny`) |
| `que` | string | No | Default: `nagd`. Ver valores |
| `id_nom` | int | Condicional | Obligatorio si `que=un_sacd` |
| `propuesta` | string | No | Propuesta de calendario (vacío en flujo periodo) |
| `empiezamin` | string | Condicional | Con `periodo=otro` |
| `empiezamax` | string | Condicional | Con `periodo=otro` |
| `sel` | array | No | Legacy personas_select: `id_nom#id_tabla` |
| `sacd` | string | No* | Hidden web: `uno`. La app móvil lo envía por compatibilidad |

\*No lo usa el caso de uso directamente; la web lo incluye en el formulario hash.

### Valores de `que`

| Valor | Alcance |
|-------|---------|
| `nagd` | SACD activos tabla N/AGD de la delegación (default menú periodo) |
| `sssc` | SACD tabla SSSC |
| `un_sacd` | Un solo SACD (`id_nom` o `sel[]`) |

Usuarios rol `p-sacd`: el backend fuerza `que=un_sacd` e `id_nom` del propio usuario.

### Valores de `periodo` (pantalla periodo)

Opciones del formulario `com_sacd_activ_periodo.php`:

| Valor | Etiqueta web |
|-------|----------------|
| `tot_any` | Todo el año |
| `trimestre_1` … `trimestre_4` | Trimestres naturales |
| `otro` | Rango custom (`empiezamin` / `empiezamax`) |

Para `que=un_sacd` desde personas_select, si `periodo` viene vacío la web usa `curso_crt` + año actual.

## Salida

- Helper: `ContestarJson::enviar`
- `data`: string JSON escapado.

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `periodo_txt` | string | Texto descriptivo del periodo |
| `lugar_fecha` | string | Pie de página («Población, dd.mm.yyyy») |
| `mi_dele` | string | Delegación del usuario |
| `que`, `propuesta` | string | Eco de entrada |
| `mensaje_periodo` | string | Solo si no se pudo calcular rango: `"falta determinar un periodo"` |
| `sacds` | array | Lista principal |
| `sacds_paso` | array | SACD de paso (vacío si `que=un_sacd`) |

### Elemento `sacds[]` / `sacds_paso[]`

| Campo | Tipo |
|-------|------|
| `id_nom` | int |
| `nom_ap` | string |
| `txt` | object | Textos i18n (`t_f_ini`, `com_sacd`, …) |
| `actividades` | array |

### Elemento `actividades[]`

| Campo | Tipo | Notas |
|-------|------|-------|
| `propio` | bool \| `"t"` | Actividad propia del SACD |
| `f_ini`, `f_fin` | string | Fechas localizadas |
| `nombre_ubi` | string | Lugar |
| `sfsv`, `actividad`, `asistentes`, `encargado` | string | Columnas tabla |
| `observ`, `cargo` | string | Pueden ser `"null"` en JSON legacy |
| `nom_tipo` | string | Tipo actividad |

## Ejemplo (flujo menú atención actividades)

**Request:**

```http
POST /orbix/src/actividadessacd/comunicacion_activ_sacd_data HTTP/1.1
Accept: application/json
Content-Type: application/x-www-form-urlencoded
Cookie: PHPSESSID=...

que=nagd&id_nom=0&propuesta=&periodo=trimestre_1&year=2026&empiezamin=&empiezamax=&sacd=uno
```

**Response (fragmento):**

```json
{
  "success": true,
  "data": "{\"periodo_txt\":\"atención actividades para el periodo ...\",\"lugar_fecha\":\"Barcelona, 25.05.2026\",\"sacds\":[{\"id_nom\":100,\"nom_ap\":\"García, Juan\",\"txt\":{\"t_f_ini\":\"Inicio\",\"com_sacd\":\"...\"},\"actividades\":[{\"propio\":true,\"f_ini\":\"01/01/2026\",\"f_fin\":\"31/03/2026\",\"nombre_ubi\":\"Parroquia\",\"sfsv\":\"SF\",\"actividad\":\"Misa\",\"asistentes\":\"120\",\"encargado\":\"\",\"observ\":\"\",\"cargo\":\"\",\"nom_tipo\":\"Liturgia\"}]}],\"sacds_paso\":[]}"
}
```

**Periodo inválido:**

```json
{
  "success": true,
  "data": "{\"mensaje_periodo\":\"falta determinar un periodo\",\"sacds\":[],\"sacds_paso\":[]}"
}
```

## Casos de uso

- `src\actividadessacd\application\ComunicacionActividadesSacdData`

## Cliente de referencia

- `orbix-android`: `fetchComunicacionActivSacd()` — `que=nagd`, `sacd=uno`.

## Endpoint relacionado

- [`comunicacion_activ_sacd_enviar.md`](comunicacion_activ_sacd_enviar.md) — encolar mails (no implementado aún en móvil).
