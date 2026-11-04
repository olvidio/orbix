---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Cambiar el tipo de una actividad"
flujo: "actividades.actividad_cambiar_tipo.gestionar.flujo"
preguntas: ["Como cambiar el tipo de una actividad en Cambiar el tipo de una actividad?"]
pantallas_principales: ["actividades.pantalla.actividad_ver"]
fragmentos: []
endpoints: ["/src/actividades/actividad_cambiar_tipo"]
source: "docs/catalogo/actividades/flujos/actividad_cambiar_tipo.md"
estado_revision: "generado"
---

# Ayuda IA - Cambiar el tipo de una actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Cambiar el tipo de una actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como cambiar el tipo de una actividad en Cambiar el tipo de una actividad?

## Donde Entrar

- Ficha de actividad (ver/editar/nueva/cambiar tipo) (`actividades.pantalla.actividad_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Cambiar el tipo de una actividad

1. Marcar la actividad en la lista y pulsar **cambiar tipo**.
2. Se abre un formulario con los datos de la actividad ya rellenados (aunque el título dice «Nueva actividad»).
3. Elegir el nuevo tipo en **sf/sv**, **asistentes**, **actividad** y **tipo de actividad**.
4. Pulsar **Guardar ficha**.

Referencias tecnicas para verificar la respuesta:
- `/src/actividades/actividad_cambiar_tipo`

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.actividad_ver`

## Objetivo

Cambiar el tipo de una actividad ya creada (sf/sv, asistentes, actividad o tipo).

## Errores Documentados

- `debe seleccionar un tipo de actividad`
- `actividad no encontrada`
- `hay un error, no se ha guardado + detalle`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
