---
tipo: manual_usuario
modulo: ubiscamas
flujos: 5
estado_revision: revisado_parcial
---

# Manual De Usuario - ubiscamas

Asignacion de **camas y habitaciones** en casas (CDC) a asistentes de actividades.

## Acceso

Sin entrada dedicada en `menus.csv` — habitualmente desde **ficha actividad/casa** o dossier habitaciones (integracion con **ubis** / actividades).

Pantallas API:

- `actividad_habitaciones` — rejilla habitaciones de actividad
- `cama` / `habitacion` — alta edicion
- `update_cama_asistente` — asignar persona a cama
- `update_solo_vip` — flag VIP

## Asignar Camas A Asistentes

1. Abrir pantalla habitaciones de la actividad en casa.
2. Revisar cuadricula camas/libres/ocupadas.
3. Asignar asistente a cama (drag o formulario segun UI).
4. Marcar VIP si procede.

## Modulos Relacionados

- **ubis** — ubi/casa
- **actividades** — actividad en casa
- **asistentes** — persona en cama
- **actividadcargos** — reutiliza id_tabla select en algunos widgets

Legacy: revisar `apps/ubiscamas` / baseline si existe.
