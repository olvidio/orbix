---
id: "ubiscamas.update_solo_vip"
tipo: "endpoint"
modulo: "ubiscamas"
url: "/src/ubiscamas/update_solo_vip"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubiscamas/infrastructure/ui/http/controllers/update_solo_vip.php"
entrada: ["post.ctx:string", "post.solo_vip:string"]
entrada_obligatoria: ["ctx"]
respuesta: "raw_response"
requiere_hashb: true
frontend_referencias: ["frontend/ubiscamas/view/lista_habitaciones.phtml"]
casos_uso: []
tags: ["ubiscamas", "update", "solo", "vip"]
estado_revision: "revisado"
errores: ["Operación no autorizada", "Actividad no encontrada", "Error al guardar el estado VIP de la actividad"]
---

# Update Solo Vip

Marca o desmarca el modo «solo camas VIP» de una actividad (`desc_activ=camasVIP` si `solo_vip=true`). `id_activ` viene de la cápsula `ctx` (`HashB::sign('update_solo_vip', {id_activ})`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Marca o desmarca el modo «solo camas VIP» de una actividad (`desc_activ=camasVIP` si `solo_vip=true`). `id_activ` viene de la cápsula `ctx` (`HashB::sign('update_solo_vip', {id_activ})`).

## Endpoint

- URL: `/src/ubiscamas/update_solo_vip`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubiscamas/infrastructure/ui/http/controllers/update_solo_vip.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `ctx` | `string` | application | Si | Cápsula HashB; `id_activ` se lee del contexto abierto |
| `solo_vip` | `string` | application | No | String `"true"` activa modo VIP |

## Salida

- Helper: `echo json_encode` (JSON directo, sin sobre de `ContestarJson`).
- Forma: `raw_response`.
- Exito: `success: true`, `mensaje: "ok"`.
  - `success`: boolean
  - `mensaje`: ok o texto error

## Errores conocidos
- `Operación no autorizada`
- `Actividad no encontrada`
- `Error al guardar el estado VIP de la actividad`

## Permisos

Autorización vía cápsula HashB `ctx`; sin perm_* en controller.

## Casos De Uso

- Lógica inline en el controller (sin caso de uso en `application/`).

## Frontend Relacionado

- `frontend/ubiscamas/view/lista_habitaciones.phtml`
