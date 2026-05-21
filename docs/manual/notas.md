---
tipo: manual_usuario
modulo: notas
flujos: 34
estado_revision: revisado_parcial
---

# Manual De Usuario - notas

**Notas STGR**, actas, asignaturas pendientes, tessera.

## Acceso Por Menu (rol 12 STGR, 4)

| Texto | Controller |
|-------|------------|
| **Actas** | `acta_select.php` |
| Asignaturas pendientes | `asignaturas_pendientes.php`, resumen |
| Acta listado anual | `acta_listado_anual.php` |
| Faltan que… | `asig_faltan_que.php` |

Ver 34 flujos en `docs/catalogo/notas/flujos/`.

## Actas

1. **Actas** — seleccionar contexto (curso, persona, acta).
2. Consultar/editar notas por asignatura.
3. Generar listados anuales si aplica.

## Asignaturas Pendientes

1. Resumen o detalle pendientes.
2. Marcar/cerrar asignaturas segun pantalla.

## Modulos Relacionados

personas, profesores, actividadestudios, certificados.

Legacy: `documentacion/notas_migracion_baseline.md`, Obix `notas/mapa_*`.
