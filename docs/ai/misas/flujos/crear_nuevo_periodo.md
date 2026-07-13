---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Crear Nuevo Periodo"
flujo: "misas.crear_nuevo_periodo.gestionar.flujo"
preguntas: ["Como obtener datos en Crear Nuevo Periodo?"]
pantallas_principales: []
fragmentos: ["misas.pantalla.crear_nuevo_periodo"]
endpoints: ["/src/misas/crear_nuevo_periodo_data"]
source: "docs/catalogo/misas/flujos/crear_nuevo_periodo.md"
estado_revision: "generado"
---

# Ayuda IA - Crear Nuevo Periodo

Usa este documento para responder preguntas de usuario sobre como trabajar con `Crear Nuevo Periodo`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Crear Nuevo Periodo?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.crear_nuevo_periodo`

## Objetivo

Crea asignaciones EncargoDia para un nuevo periodo de plan de misas a partir de plantilla y devuelve el payload de cuadrícula para renderizar ver_cuadricula_zona.phtml.

## Errores Documentados

- `solo deberia haber uno`
- `<repositorio getErrorTxt() acumulado en error_txt>`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
