---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividades"
titulo: "Cambiar tipo de actividad"
flujo: "actividades.actividad_cambiar_tipo.gestionar.flujo"
preguntas: []
pantallas_principales: ["actividades.pantalla.actividad_ver"]
fragmentos: []
endpoints: ["/src/actividades/actividad_cambiar_tipo"]
source: "docs/catalogo/actividades/flujos/actividad_cambiar_tipo.md"
estado_revision: "generado"
---

# Ayuda IA - Cambiar tipo de actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Cambiar tipo de actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:

## Donde Entrar

- Ficha de actividad (ver/editar/nueva/cambiar tipo) (`actividades.pantalla.actividad_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Pantallas Y Fragmentos Relacionados

- `actividades.pantalla.actividad_ver`

## Objetivo

Seleccionar un nuevo tipo en la cascada, confirmar aviso de vuelta a *proyecto* y guardar.

## Errores Documentados

- `debe seleccionar un tipo de actividad`
- `actividad no encontrada`
- `hay un error, no se ha guardado + detalle`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
