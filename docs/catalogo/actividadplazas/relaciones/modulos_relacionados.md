---
tipo: relaciones_modulos
modulo: actividadplazas
estado_revision: revisado_parcial
---

# Modulos relacionados — actividadplazas

| Modulo | Uso |
|--------|-----|
| actividades | Contexto actividad; `resumen_plazas` desde JS actividades |
| asistentes | `posibles_propietarios_data` en forms asistente/actividad |
| personas | `peticiones_activ` — peticiones de plaza por persona |
| planning / grupo estudios | Delegaciones del grupo para balance y ceder |

Huérfano justificado:

- `/src/actividadplazas/posibles_propietarios_data` → `FormActividadesDeUnaPersonaRender`, `FormAsistentesAUnaActividadRender` (**asistentes**)

Legacy: `documentacion/actividadplazas_migracion_baseline.md`
