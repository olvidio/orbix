---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubis"
titulo: "Calendario Periodos"
flujo: "ubis.calendario_periodos.gestionar.flujo"
preguntas: ["Como eliminar en Calendario Periodos?", "Como guardar en Calendario Periodos?"]
pantallas_principales: ["ubis.pantalla.calendario_periodos"]
fragmentos: []
endpoints: ["/src/ubis/calendario_periodos_eliminar", "/src/ubis/calendario_periodos_guardar"]
source: "docs/catalogo/ubis/flujos/calendario_periodos.md"
estado_revision: "generado"
---

# Ayuda IA - Calendario Periodos

Usa este documento para responder preguntas de usuario sobre como trabajar con `Calendario Periodos`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como eliminar en Calendario Periodos?
- Como guardar en Calendario Periodos?

## Donde Entrar

- Calendario Periodos (`ubis.pantalla.calendario_periodos`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Referencias tecnicas para verificar la respuesta:
- `/src/ubis/calendario_periodos_eliminar`

## Guardar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `ubis.pantalla.calendario_periodos`

## Objetivo

Elimina un periodo de calendario CDC identificado por id_item.

## Errores Documentados

- `no sé cuál he de borar`
- `no se encuentra el periodo a borrar`
- `hay un error, no se ha eliminado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
