---
tipo: "indice"
alcance: "cliente_movil"
estado_revision: "revisado"
---

# Endpoints revisados para cliente móvil

Fichas API con ejemplos verificados contra `orbix-android` (mayo 2026). Estado `estado_revision: revisado` en cada ficha.

## Autenticación y shell

| Endpoint | Ficha | Uso en app |
|----------|-------|------------|
| `/src/usuarios/app_login` | [usuarios/api/app_login.md](usuarios/api/app_login.md) | Login JSON + 2FA |
| `/src/usuarios/app_session` | [usuarios/api/app_session.md](usuarios/api/app_session.md) | Restaurar sesión al abrir |
| `/src/menus/grupmenu_coleccion` | [menus/api/grupmenu_coleccion.md](menus/api/grupmenu_coleccion.md) | Menú ☰ / drawer |

## Misas — plan de misas (nativo)

Índice nativo: `PlanMisasScreen` (`misas_index.php`). Pantallas en `MisasApi.kt` / `MisasNavigation.kt`.

### Cuadrícula zona (ver, modificar, preparar, plantilla, cambiar estado)

| Endpoint | Ficha | Uso en app |
|----------|-------|------------|
| `/src/misas/plan_de_misas_pantalla_data` | [misas/api/plan_de_misas_pantalla_data.md](misas/api/plan_de_misas_pantalla_data.md) | Filtros zona / orden / plantilla (`pantalla=ver\|modificar\|preparar\|modificar_plantilla`) |
| `/src/misas/ver_cuadricula_zona_data` | [misas/api/ver_cuadricula_zona_data.md](misas/api/ver_cuadricula_zona_data.md) | Cuadrícula encargo × días |
| `/src/misas/cambiar_status_data` | [misas/api/cambiar_status_data.md](misas/api/cambiar_status_data.md) | Filtros extra estado (pantalla cambiar status; cuadrícula igual que arriba) |

Flujo típico: `plan_de_misas_pantalla_data` → `ver_cuadricula_zona_data` al pulsar «Ver cuadrícula». Mutaciones (preparar periodo, cambiar estado, editar celdas) **no** están en móvil.

### Plan de un sacerdote

| Endpoint | Ficha | Uso en app |
|----------|-------|------------|
| `/src/misas/buscar_plan_sacd_data` | [misas/api/buscar_plan_sacd_data.md](misas/api/buscar_plan_sacd_data.md) | Desplegable SACD |
| `/src/misas/ver_plan_sacd_data` | [misas/api/ver_plan_sacd_data.md](misas/api/ver_plan_sacd_data.md) | Listado por fechas |

### Plan de un centro

| Endpoint | Ficha | Uso en app |
|----------|-------|------------|
| `/src/misas/buscar_plan_ctr_data` | [misas/api/buscar_plan_ctr_data.md](misas/api/buscar_plan_ctr_data.md) | Zonas y centros |
| `/src/misas/ver_plan_ctr_data` | [misas/api/ver_plan_ctr_data.md](misas/api/ver_plan_ctr_data.md) | Cuadrícula centro × días |

### Encargos e iniciales (consulta)

| Endpoint | Ficha | Uso en app |
|----------|-------|------------|
| `/src/misas/modificar_encargos_data` | [misas/api/modificar_encargos_data.md](misas/api/modificar_encargos_data.md) | Zonas encargos zona |
| `/src/misas/ver_encargos_zona_data` | [misas/api/ver_encargos_zona_data.md](misas/api/ver_encargos_zona_data.md) | Tabla encargos zona |
| `/src/misas/modificar_encargos_centros_data` | [misas/api/modificar_encargos_centros_data.md](misas/api/modificar_encargos_centros_data.md) | Zonas encargos centros |
| `/src/misas/ver_encargos_centros_data` | [misas/api/ver_encargos_centros_data.md](misas/api/ver_encargos_centros_data.md) | Tabla encargo ↔ centro |
| `/src/misas/modificar_iniciales_sacd_zona_data` | [misas/api/modificar_iniciales_sacd_zona_data.md](misas/api/modificar_iniciales_sacd_zona_data.md) | Desplegable zonas iniciales |
| `/src/misas/ver_iniciales_zona_data` | [misas/api/ver_iniciales_zona_data.md](misas/api/ver_iniciales_zona_data.md) | Tabla iniciales SACD |

## Actividades SACD — atención actividades

| Endpoint | Ficha | Uso en app |
|----------|-------|------------|
| `/src/actividadessacd/comunicacion_activ_sacd_data` | [actividadessacd/api/comunicacion_activ_sacd_data.md](actividadessacd/api/comunicacion_activ_sacd_data.md) | Listado por periodo |

Pantalla web: `frontend/actividadessacd/controller/com_sacd_activ_periodo.php`.

## Planning — zonas

| Endpoint | Ficha | Uso en app |
|----------|-------|------------|
| `/src/planning/planning_zones_que_data` | [planning/api/planning_zones_que_data.md](planning/api/planning_zones_que_data.md) | Desplegable de zonas |
| `/src/planning/planning_zones_select_data` | [planning/api/planning_zones_select_data.md](planning/api/planning_zones_select_data.md) | Calendario / actividades |

Flujo: `planning_zones_que` → `planning_zones_select_data` al pulsar «Ver planning».

## Planning — nuevo plan (por casas)

Menú **Nuevo planing** → `planning_casa_que.php?propuesta_calendario=1`. Pantalla nativa: `NuevoPlanScreen`.

| Endpoint | Ficha | Uso en app |
|----------|-------|------------|
| `/src/planning/planning_casa_que_data` | [planning/api/planning_casa_que_data.md](planning/api/planning_casa_que_data.md) | Grupos de casas según rol |
| `/src/ubis/casas_opciones_data` | [ubis/api/casas_opciones_data.md](ubis/api/casas_opciones_data.md) | Desplegable casa (`cdc_sel=9`) |
| `/src/planning/planning_casa_ver_data` | [planning/api/planning_casa_ver_data.md](planning/api/planning_casa_ver_data.md) | Actividades por casa |

Flujo: `planning_casa_que_data` → (opcional `casas_opciones_data`) → `planning_casa_ver_data` con `f_ini_iso`/`f_fin_iso`.

## Encargos SACD — ausencias

Pantalla nativa: `AusenciasScreen` (`sacd_ausencias.php` o `sacd_ausencias_jefe_zona.php`).

| Endpoint | Ficha | Uso en app |
|----------|-------|------------|
| `/src/encargossacd/sacd_ausencias_jefe_zona_data` | [encargossacd/api/sacd_ausencias_jefe_zona_data.md](encargossacd/api/sacd_ausencias_jefe_zona_data.md) | Desplegable sacerdotes (jefe de zona) |
| `/src/encargossacd/sacd_select_data` | [encargossacd/api/sacd_select_data.md](encargossacd/api/sacd_select_data.md) | Tipo SACD + lista (menú «Ausencias sacd») |
| `/src/encargossacd/sacd_ausencias_get_data` | [encargossacd/api/sacd_ausencias_get_data.md](encargossacd/api/sacd_ausencias_get_data.md) | Filas de ausencias / permisos |

Flujo jefe: `sacd_ausencias_jefe_zona_data` → `sacd_ausencias_get_data`. Flujo SACD: `sacd_select_data` → `sacd_ausencias_get_data`. Edición (`sacd_ausencias_update`) solo web.

## Guía transversal

- [Clientes nativos](_clientes_nativos.md) — URLs, cookies, parseo de `data`.
- [Convenciones API](_convenciones_api.md) — envelope, HashB, tipos de operación.
