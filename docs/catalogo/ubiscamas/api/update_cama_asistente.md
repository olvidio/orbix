---
id: "ubiscamas.update_cama_asistente"
tipo: "endpoint"
modulo: "ubiscamas"
url: "/src/ubiscamas/update_cama_asistente"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubiscamas/infrastructure/ui/http/controllers/update_cama_asistente.php"
entrada: ["post.ctx:string", "post.id_cama:string"]
entrada_obligatoria: ["ctx"]
respuesta: "raw_response"
respuesta_data_schema: "ubiscamas_UpdateCamaAsistenteData"
respuesta_data: ["success:bool, mensaje: string"]
requiere_hashb: true
frontend_referencias: ["frontend/ubiscamas/view/lista_habitaciones.phtml"]
casos_uso: ["src\ubiscamas\application\UpdateCamaAsistente"]
tags: ["ubiscamas", "update", "cama", "asistente"]
estado_revision: "revisado"
errores: ["Operación no autorizada", "Asistencia no encontrada para id_nom", "Error al guardar la asignación de la cama"]
---

# Update Cama Asistente

Asigna o reasigna la cama de un asistente en una actividad. `id_activ` e `id_nom` se extraen de
la cápsula `ctx` firmada; `id_cama` vacío desasigna.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Asigna o reasigna la cama de un asistente en una actividad. `id_activ` e `id_nom` se extraen de
la cápsula `ctx` firmada (`HashB::sign('update_cama_asistente', {id_activ, id_nom})`); `id_cama`
vacío desasigna.

## Endpoint

- URL: `/src/ubiscamas/update_cama_asistente`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubiscamas/infrastructure/ui/http/controllers/update_cama_asistente.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `ctx` | `string` | application | Si | Cápsula HashB; `id_activ` e `id_nom` se leen del contexto abierto |
| `id_cama` | `string` | application | No |  |

## Salida

- Helper: `echo json_encode` (JSON directo, sin sobre de `ContestarJson`).
- Forma: `raw_response`.
- Exito: `success: true`, `mensaje: "ok"`.
  - `success`: boolean
  - `mensaje`: ok o texto error

## Errores conocidos
- `Operación no autorizada`
- `Asistencia no encontrada para id_nom`
- `Error al guardar la asignación de la cama`

## Permisos

Autorización vía cápsula HashB `ctx`; sin perm_* en caso de uso.

## Casos De Uso

- `src\ubiscamas\application\UpdateCamaAsistente`

## Frontend Relacionado

- `frontend/ubiscamas/view/lista_habitaciones.phtml`
