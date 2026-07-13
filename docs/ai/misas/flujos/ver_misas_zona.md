---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Ver Misas Zona"
flujo: "misas.ver_misas_zona.gestionar.flujo"
preguntas: ["Como obtener datos en Ver Misas Zona?"]
pantallas_principales: []
fragmentos: ["misas.pantalla.ver_misas_zona"]
endpoints: ["/src/misas/ver_misas_zona_data"]
source: "docs/catalogo/misas/flujos/ver_misas_zona.md"
estado_revision: "generado"
---

# Ayuda IA - Ver Misas Zona

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver Misas Zona`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Ver Misas Zona?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.ver_misas_zona`

## Objetivo

Construye la cuadrícula de consulta de misas por zona y rango de fechas (solo lectura, con metadatos dia/tipo en celdas).

## Errores Documentados

- `solo deberia haber uno`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
