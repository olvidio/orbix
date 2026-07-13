---
tipo: "ayuda_ia"
subtipo: "indice"
modulo: "pasarela"
flujos: 14
pantallas: 12
endpoints: 21
estado_revision: "generado"
---

# Ayuda IA - pasarela

Indice para una IA local. Estos documentos estan pensados para busqueda semantica y respuestas de ayuda funcional.

## Como Debe Responder La IA

- Priorizar pasos de usuario sobre detalles tecnicos.
- Si falta ruta de menu, decir que esta pendiente de documentar.
- No inventar permisos, errores o validaciones que no aparezcan en la documentacion.
- Usar referencias tecnicas solo para verificar, no como respuesta principal al usuario final.

## Flujos Disponibles

- fecha de activación -> `flujos/activacion.md`
- Editar activación por defecto -> `flujos/activacion_default.md`
- Excepción de activación por tipo -> `flujos/activacion_excepcion.md`
- contribución no duerme -> `flujos/contribucion_no_duerme.md`
- Default contribución no duerme -> `flujos/contribucion_no_duerme_default.md`
- Excepción contribución no duerme -> `flujos/contribucion_no_duerme_excepcion.md`
- contribución reserva -> `flujos/contribucion_reserva.md`
- Default contribución reserva -> `flujos/contribucion_reserva_default.md`
- Excepción contribución reserva -> `flujos/contribucion_reserva_excepcion.md`
- Exportar actividades al exterior -> `flujos/exportar_actividades.md`
- Selector tipo en exportar -> `flujos/exportar_que_actividad_tipo_html.md`
- nombres particulares -> `flujos/nombre.md`
- Alta/edición nombre por tipo -> `flujos/nombre_excepcion.md`
- Texto descriptivo del tipo -> `flujos/tipo_activ_txt.md`

## Pantallas Disponibles

- Dispatcher AJAX activación -> `pantallas/activacion_ajax.md`
- Fecha de activación -> `pantallas/activacion_lista.md`
- Dispatcher AJAX contribución no duerme -> `pantallas/contribucion_no_duerme_ajax.md`
- Contribución no duerme -> `pantallas/contribucion_no_duerme_lista.md`
- Dispatcher AJAX contribución reserva -> `pantallas/contribucion_reserva_ajax.md`
- Contribución reserva -> `pantallas/contribucion_reserva_lista.md`
- Exportar actividades -> `pantallas/exportar_que.md`
- Resultado exportación actividades -> `pantallas/exportar_select.md`
- Dispatcher AJAX nombre -> `pantallas/nombre_ajax.md`
- Formulario nombre (Twig) -> `pantallas/nombre_form.md`
- Nombres de actividades particulares -> `pantallas/nombre_lista.md`
- Parámetros pasarela -> `pantallas/parametros_menu.md`
