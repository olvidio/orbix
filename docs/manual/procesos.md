---
tipo: manual_usuario
modulo: procesos
flujos: 17
estado_revision: revisado_parcial
---

# Manual De Usuario - procesos

**Procesos** y **fases** de actividades: definicion, asignacion a tipos de actividad y cambio de fase.

## Acceso Por Menu (rol 13 admin, 2–3 actividades)

| Texto en menu | Controller | Uso |
|---------------|------------|-----|
| **Procesos activ.** (raiz) | — | Agrupador |
| **Procesos** | `procesos_select.php` | Catalogo procesos |
| **Cambiar de fase** | `fases_activ_cambio.php` | Cambio fase en lote (`sactividad=crt/ca/cve…`) |
| Tipo actividad ↔ proceso | `tipo_activ_proceso.php` | Asignar procesos a tipos |
| Proceso de actividad | `actividad_proceso.php` | Tareas/fases de una actividad |
| Permisos usuario | `usuario_perm_activ.php` | Permisos por actividad |

## Gestionar Catalogo De Procesos

1. **Procesos** → listado.
2. **Ver** / **clonar** / **regenerar** / dependencias segun botones.
3. Editar definicion en `procesos_ver`.

## Asignar Proceso A Tipo De Actividad

1. `tipo_activ_proceso` — elegir tipo actividad.
2. Asignar procesos posibles y permisos por fase.

## Cambiar Fase De Actividades

1. Menu **Cambiar de fase** (contexto crt, ca, cve…).
2. Seleccionar actividades y fase destino.
3. Confirmar — actualiza `ActividadProceso`.

## Modulos Relacionados

- **actividades** — entidad y fases
- **cambios** — avisos usan fases de proceso
- **usuarios** — permisos actividad

Legacy: `documentacion/procesos_migracion_baseline.md`
