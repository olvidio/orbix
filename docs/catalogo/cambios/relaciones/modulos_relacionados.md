---
tipo: relaciones_modulos
modulo: cambios
estado_revision: revisado_parcial
---

# Modulos relacionados — cambios

| Modulo | Uso |
|--------|-----|
| procesos | Fases en preferencias objeto (`cambio_usuario_objeto_pref_fases_data`) |
| usuarios | Avisos por usuario |
| actividades / personas / … | Objetos que generan cambios (dominio `Cambio*`) |

Endpoints AJAX del formulario preferencias (sin controller listado en generador):

- `cambio_usuario_eliminar`, `cambio_usuario_eliminar_hasta_fecha` — desde `avisos_generar`
- `cambio_usuario_objeto_pref_guardar`, `_eliminar`, `cambio_usuario_propiedad_pref_*` — desde `usuario_avisos_pref` y fragmentos `usuario_avisos_pref_*.php`

CLI: `avisos_generar_tabla`, `avisos_generar_mails` (infra admin).

Legacy: `documentacion/cambios_migracion_baseline.md`
