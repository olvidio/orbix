---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubiscamas"
titulo: "Filtrar las camas VIP"
flujo: "ubiscamas.update_solo_vip.gestionar.flujo"
preguntas: ["Como filtrar las camas vip en Filtrar las camas VIP?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/ubiscamas/update_solo_vip"]
source: "docs/catalogo/ubiscamas/flujos/update_solo_vip.md"
estado_revision: "generado"
---

# Ayuda IA - Filtrar las camas VIP

Usa este documento para responder preguntas de usuario sobre como trabajar con `Filtrar las camas VIP`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como filtrar las camas vip en Filtrar las camas VIP?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Filtrar las camas vip

1. Marcar la casilla «solo VIP».
2. La lista muestra solo las camas VIP; desmarcarla para ver todas.

Referencias tecnicas para verificar la respuesta:
- `/src/ubiscamas/update_solo_vip`

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Mostrar solo las camas VIP en la distribución de habitaciones.

## Errores Documentados

- `Operación no autorizada`
- `Actividad no encontrada`
- `Error al guardar el estado VIP de la actividad`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
