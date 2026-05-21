---
tipo: manual_usuario
modulo: dossiers
flujos: 5
estado_revision: revisado_parcial
---

# Manual De Usuario - dossiers

Capa transversal de **fichas (dossiers)** embebidas en personas, actividades, ubis, etc.

## Acceso

- **Ficha persona** → `lista_dossiers` en `home_persona`
- **Ficha actividad / ubi** — listado dossiers lateral
- **Admin permisos dossier** → `perm_dossiers.php` (menu permisos)

Controller central: `frontend/dossiers/controller/dossiers_ver.php` — carga widgets (`Select_*`, forms) segun `id_tipo_dossier`.

## Ver Fichas De Un Registro

1. Abrir persona, actividad o ubi.
2. Listado de dossiers disponibles (3101 asistentes, 3102 cargos, 1301, 1302…).
3. Pulsar ficha → `dossiers_ver` renderiza widget del modulo correspondiente.

## Configurar Tipos De Dossier (Admin)

1. **Perm dossiers** — listado tipos.
2. **Ver** tipo → `perm_dossier_ver` — editar permisos, formulario, botones.
3. **Guardar** / **Eliminar** → `/src/dossiers/tipo_dossier_guardar`, `tipo_dossier_eliminar` (JSON desde ficha).

## Modulos Que Aportan Widgets

| id ejemplo | Modulo |
|------------|--------|
| 3101/1301 | asistentes |
| 3102/1302 | actividadcargos |
| … | notas, inventario, etc. |

Mapa completo: ampliar en `docs/catalogo/dossiers/relaciones/tipos_dossier.md` (pendiente).

Legacy: `documentacion/dossiers_migracion_baseline.md`
