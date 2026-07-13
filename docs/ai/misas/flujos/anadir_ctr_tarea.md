---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Anadir Ctr Tarea"
flujo: "misas.anadir_ctr_tarea.gestionar.flujo"
preguntas: ["Como ejecutar en Anadir Ctr Tarea?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/misas/anadir_ctr_tarea"]
source: "docs/catalogo/misas/flujos/anadir_ctr_tarea.md"
estado_revision: "generado"
---

# Ayuda IA - Anadir Ctr Tarea

Usa este documento para responder preguntas de usuario sobre como trabajar con `Anadir Ctr Tarea`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Anadir Ctr Tarea?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Añade o elimina una fila de plantilla (centro asociado a tarea) en el editor de plantillas. Rama que=anadir crea Plantilla con semana=-1; rama quitar elimina por id_item.

## Errores Documentados

- `Error: falta el id_item`
- `No se encuentra la plantilla %d`
- `opción no definida en switch en %s, linea %s`
- `<repositorio getErrorTxt()>`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
