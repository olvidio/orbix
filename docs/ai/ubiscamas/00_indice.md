---
tipo: "ayuda_ia"
subtipo: "indice"
modulo: "ubiscamas"
flujos: 5
pantallas: 5
endpoints: 9
estado_revision: "generado"
---

# Ayuda IA - ubiscamas

Indice para una IA local. Estos documentos estan pensados para busqueda semantica y respuestas de ayuda funcional.

## Como Debe Responder La IA

- Priorizar pasos de usuario sobre detalles tecnicos.
- Si falta ruta de menu, decir que esta pendiente de documentar.
- No inventar permisos, errores o validaciones que no aparezcan en la documentacion.
- Usar referencias tecnicas solo para verificar, no como respuesta principal al usuario final.

## Flujos Disponibles

- Asignar camas a los asistentes -> `flujos/actividad_habitaciones.md`
- camas -> `flujos/cama.md`
- Ver las habitaciones de una casa -> `flujos/habitacion.md`
- Asignar una cama a un asistente -> `flujos/update_cama_asistente.md`
- Filtrar las camas VIP -> `flujos/update_solo_vip.md`

## Pantallas Disponibles

- Cama Form -> `pantallas/cama_form.md`
- Habitacion Form -> `pantallas/habitacion_form.md`
- Lista Habitaciones -> `pantallas/lista_habitaciones.md`
- Lista Habitaciones Distribucion -> `pantallas/lista_habitaciones_distribucion.md`
- Lista Habitaciones Nombres -> `pantallas/lista_habitaciones_nombres.md`
