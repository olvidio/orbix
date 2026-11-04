---
tipo: "ayuda_ia"
subtipo: "indice"
modulo: "configuracion"
flujos: 4
pantallas: 4
endpoints: 6
estado_revision: "generado"
---

# Ayuda IA - configuracion

Indice para una IA local. Estos documentos estan pensados para busqueda semantica y respuestas de ayuda funcional.

## Como Debe Responder La IA

- Priorizar pasos de usuario sobre detalles tecnicos.
- Si falta ruta de menu, decir que esta pendiente de documentar.
- No inventar permisos, errores o validaciones que no aparezcan en la documentacion.
- Usar referencias tecnicas solo para verificar, no como respuesta principal al usuario final.

## Flujos Disponibles

- Añadir o modificar un módulo -> `flujos/modulos.md`
- Consultar los módulos definidos -> `flujos/modulos_select.md`
- Ver los parámetros del esquema -> `flujos/parametros.md`
- Cambiar las fechas del curso -> `flujos/periodo_calendario_escolar.md`

## Pantallas Disponibles

- Ficha de módulo -> `pantallas/modulos_form.md`
- Definir módulos -> `pantallas/modulos_select.md`
- Proxy AJAX modulos_update -> `pantallas/modulos_update.md`
- Configuración del esquema -> `pantallas/parametros.md`
