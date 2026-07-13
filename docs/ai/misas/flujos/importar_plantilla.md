---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Importar Plantilla"
flujo: "misas.importar_plantilla.gestionar.flujo"
preguntas: ["Como obtener datos en Importar Plantilla?"]
pantallas_principales: []
fragmentos: ["misas.pantalla.importar_plantilla"]
endpoints: ["/src/misas/importar_plantilla_data"]
source: "docs/catalogo/misas/flujos/importar_plantilla.md"
estado_revision: "generado"
---

# Ayuda IA - Importar Plantilla

Usa este documento para responder preguntas de usuario sobre como trabajar con `Importar Plantilla`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Importar Plantilla?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.importar_plantilla`

## Objetivo

Copia asignaciones de plantilla origen a destino para una zona, creando/actualizando EncargoDia en el rango correspondiente.

## Errores Documentados

- `solo deberia haber uno`
- `<repositorio getErrorTxt() acumulado>`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
