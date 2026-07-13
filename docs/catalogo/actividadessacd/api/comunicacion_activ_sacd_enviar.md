---
id: "actividadessacd.comunicacion_activ_sacd_enviar"
tipo: "endpoint"
modulo: "actividadessacd"
url: "/src/actividadessacd/comunicacion_activ_sacd_enviar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadessacd/infrastructure/ui/http/controllers/comunicacion_activ_sacd_enviar.php"
entrada: ["post.que:string", "post.id_nom:integer", "post.propuesta:string", "post.periodo:string", "post.year:string", "post.empiezamin:string", "post.empiezamax:string", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["falta determinar un periodo", "falta el definir el jefe de calendario", "No hay un mail (jefe calendario) para enviar los errores. No se procesan los mails."]
frontend_referencias: ["frontend/actividadessacd/controller/com_sacd_activ_periodo.php", "frontend/actividadessacd/view/com_sacd_activ_periodo.phtml"]
casos_uso: ["src\\actividadessacd\\application\\ComunicacionActividadesSacdEnviar"]
tags: ["actividadessacd", "comunicacion", "activ", "sacd", "enviar"]
estado_revision: "revisado"
---

# Comunicacion Activ Sacd Enviar

Encola en `cola_mails` los correos de "atención actividades" para los sacd del periodo (uno por
sacd, con copia al jefe de calendario; otro para el ctr del sacd si tiene mail).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

- Resuelve el contexto (`resolverContexto`) reutilizando la lógica de `comunicacion_activ_sacd_data`:
  determina `que` (`nagd` / `sssc` / `un_sacd`), `id_nom`, `propuesta` y el periodo (`inicioIso`/`finIso`).
  Si el usuario tiene rol `p-sacd`, fuerza `que = un_sacd` sobre sí mismo.
- Si no hay periodo válido, error.
- Carga las personas sacd según `que` (numerarios+agregados activos, sssc, o un único sacd).
- Construye la estructura de comunicación (`ComunicarActividadesSacdService::getArrayComunicacion`)
  y encola los mails (`enviarMails`), devolviendo el texto de error del servicio si lo hay.

## Endpoint

- URL: `/src/actividadessacd/comunicacion_activ_sacd_enviar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/comunicacion_activ_sacd_enviar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `que` | `string` | application | No | `nagd` (por defecto) / `sssc` / `un_sacd` |
| `id_nom` | `integer` | application | No | Sacd concreto cuando `que = un_sacd` |
| `propuesta` | `string` | application | No | Modo propuesta (relaja el filtro de permisos por actividad) |
| `periodo` | `string` | application | No | Selector de periodo (`Periodo`) |
| `year` | `string` | application | No | Año del periodo |
| `empiezamin` | `string` | application | No | Límite inferior de `f_ini` |
| `empiezamax` | `string` | application | No | Límite superior de `f_ini` |
| `sel` | `array` | application | No | Token `id_nom#…`; si viene, fija el sacd y `que = un_sacd` |

El controller pasa `$_POST` completo al caso de uso (`execute($_POST)`); los campos se infieren de
`resolverContexto` en el application layer.

## Salida

- Helper: `ContestarJson::enviar($useCase->execute($_POST), 'ok')` — el caso de uso devuelve el
  texto de error (vacío en éxito); `data` es el literal `"ok"`.
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Inserta filas en `cola_mails` (el envío real lo hace el servidor exterior). Por cada sacd con
  actividades: un mail al jefe de calendario + sacd, y otro al ctr del sacd si tiene email.

## Errores conocidos

- `falta determinar un periodo`
- `falta el definir el jefe de calendario` (config `jefe_calendario` vacía)
- `No hay un mail (jefe calendario) para enviar los errores. No se procesan los mails.`

## Permisos

- Sin control propio de acceso en el caso de uso, pero el filtrado por actividad usa
  `$_SESSION['oPermActividades']->havePermisoSacd(...)` (salvo en modo `propuesta`). El rol `p-sacd`
  fuerza el envío únicamente sobre el propio sacd. URL firmada con `HashFront`.

## Casos De Uso

- `src\actividadessacd\application\ComunicacionActividadesSacdEnviar`

## Frontend Relacionado

- `frontend/actividadessacd/controller/com_sacd_activ_periodo.php` (emite `url_enviar`).
- `frontend/actividadessacd/view/com_sacd_activ_periodo.phtml` (`fnjs_enviar_mails` hace el POST).
