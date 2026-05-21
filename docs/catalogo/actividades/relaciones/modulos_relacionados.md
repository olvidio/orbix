---
tipo: relaciones_modulos
modulo: actividades
---

**Hub central.** Dependientes documentados:

actividadplazas, actividadcargos, actividadtarifas, actividadessacd, actividadescentro, actividadestudios, actividadessacd, asistentes, procesos, planning, pasarela, casas, notas, misas, cambios (objetos).

Huérfanos API → forms JS (`_actividad_form.js`, `actividad_select.phtml`, calendario): `actividad_nuevo`, `actividad_editar`, `actividad_eliminar`, `actividad_duplicar`, `actividad_importar`, `actividad_publicar`, `actividad_cambiar_tipo`, fases completadas.

Consumidores externos: `actividades/actividad_que_datos` ← actividadtarifas (relacion tarifa).
