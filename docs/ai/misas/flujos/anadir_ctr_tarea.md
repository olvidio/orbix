---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Añadir o quitar un centro en la plantilla"
flujo: "misas.anadir_ctr_tarea.gestionar.flujo"
preguntas: ["Como añadir o quitar un centro en la plantilla en Añadir o quitar un centro en la plantilla?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/misas/anadir_ctr_tarea"]
source: "docs/catalogo/misas/flujos/anadir_ctr_tarea.md"
estado_revision: "generado"
---

# Ayuda IA - Añadir o quitar un centro en la plantilla

Usa este documento para responder preguntas de usuario sobre como trabajar con `Añadir o quitar un centro en la plantilla`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como añadir o quitar un centro en la plantilla en Añadir o quitar un centro en la plantilla?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Añadir o quitar un centro en la plantilla

1. En **Modificar plantilla**, usar el botón de añadir fila de la tarea y elegir el centro.
2. Para quitarlo, pulsar quitar en la fila.

Referencias tecnicas para verificar la respuesta:
- `/src/misas/anadir_ctr_tarea`

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Añadir o quitar, en la plantilla, un centro asociado a una tarea.

## Errores Documentados

- `Error: falta el id_item`
- `No se encuentra la plantilla %d`
- `opción no definida en switch en %s, linea %s`
- `<repositorio getErrorTxt()>`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
