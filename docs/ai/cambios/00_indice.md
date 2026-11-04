---
tipo: "ayuda_ia"
subtipo: "indice"
modulo: "cambios"
flujos: 11
pantallas: 6
endpoints: 13
estado_revision: "generado"
---

# Ayuda IA - cambios

Indice para una IA local. Estos documentos estan pensados para busqueda semantica y respuestas de ayuda funcional.

## Como Debe Responder La IA

- Priorizar pasos de usuario sobre detalles tecnicos.
- Si falta ruta de menu, decir que esta pendiente de documentar.
- No inventar permisos, errores o validaciones que no aparezcan en la documentacion.
- Usar referencias tecnicas solo para verificar, no como respuesta principal al usuario final.

## Flujos Disponibles

- Consultar y purgar cambios -> `flujos/avisos_generar.md`
- Eliminar cambio anotado -> `flujos/cambio_usuario.md`
- Purgar cambios hasta fecha -> `flujos/cambio_usuario_eliminar_hasta_fecha.md`
- Crear un aviso de cambios -> `flujos/cambio_usuario_objeto_pref.md`
- Elegir la fase de un aviso -> `flujos/cambio_usuario_objeto_pref_fases.md`
- Elegir qué campos vigilar -> `flujos/cambio_usuario_objeto_pref_propiedades.md`
- Guardar los avisos -> `flujos/cambio_usuario_propiedad_pref_guardar_todas.md`
- Poner condición a un campo -> `flujos/cambio_usuario_propiedad_pref_item.md`
- Previsualizar una condición -> `flujos/cambio_usuario_propiedad_pref_preview.md`
- Configurar un aviso de cambios en actividades -> `flujos/usuario_avisos_pref.md`
- los avisos de cambios de un usuario -> `flujos/usuario_form_avisos.md`

## Pantallas Disponibles

- Lista de cambios -> `pantallas/avisos_generar.md`
- Configurar aviso -> `pantallas/usuario_avisos_pref.md`
- Modal de condición -> `pantallas/usuario_avisos_pref_condicion.md`
- Desplegable de fases -> `pantallas/usuario_avisos_pref_fases.md`
- Tabla de propiedades -> `pantallas/usuario_avisos_pref_propiedades.md`
- Avisos del usuario -> `pantallas/usuario_form_avisos.md`
