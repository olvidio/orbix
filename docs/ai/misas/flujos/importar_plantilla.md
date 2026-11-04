---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Importar otra plantilla"
flujo: "misas.importar_plantilla.gestionar.flujo"
preguntas: ["Como importar otra plantilla en Importar otra plantilla?"]
pantallas_principales: ["misas.pantalla.importar_plantilla"]
fragmentos: ["misas.pantalla.importar_plantilla"]
endpoints: ["/src/misas/importar_plantilla_data"]
source: "docs/catalogo/misas/flujos/importar_plantilla.md"
estado_revision: "generado"
---

# Ayuda IA - Importar otra plantilla

Usa este documento para responder preguntas de usuario sobre como trabajar con `Importar otra plantilla`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como importar otra plantilla en Importar otra plantilla?

## Donde Entrar

- Importar Plantilla (`misas.pantalla.importar_plantilla`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Importar otra plantilla

1. En **Modificar plantilla**, elegir la plantilla origen en **Importar de la plantilla**.
2. Pulsar **importar** y confirmar el aviso de que se borra lo que hay.
3. Se muestra la plantilla con los datos copiados.

Referencias tecnicas para verificar la respuesta:
- `/src/misas/importar_plantilla_data`

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.importar_plantilla`
- `misas.pantalla.importar_plantilla`

## Objetivo

Copiar las asignaciones de una plantilla a otra de la misma zona.

## Errores Documentados

- `solo deberia haber uno`
- `<repositorio getErrorTxt() acumulado>`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
