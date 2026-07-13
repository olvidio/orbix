---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Guardar edición de actividad"
flujo: "actividades.actividad_editar.gestionar.flujo"
preguntas: []
pantallas_principales: ["actividades.pantalla.actividad_ver"]
fragmentos: ["actividades.pantalla.planning_casa_modificar"]
endpoints: ["/src/actividades/actividad_editar"]
source: "docs/catalogo/actividades/flujos/actividad_editar.md"
estado_revision: "generado"
---

# Ayuda IA - Guardar edición de actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Guardar edición de actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Ficha de actividad (ver/editar/nueva/cambiar tipo) (`actividades.pantalla.actividad_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.actividad_ver`
- `actividades.pantalla.planning_casa_modificar`

## Objetivo

Modificar campos de la actividad (fechas, lugar, plazas, observaciones, etc.) y guardar sin cambiar el tipo.

## Errores Documentados

- `sesión de permisos no disponible`
- `debe seleccionar un tipo de actividad`
- `actividad no encontrada`
- `hay un error, no se ha guardado + detalle`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
