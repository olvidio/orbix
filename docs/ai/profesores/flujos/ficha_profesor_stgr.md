---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "profesores"
titulo: "Ver ficha profesor STGR"
flujo: "profesores.ficha_profesor_stgr.gestionar.flujo"
preguntas: ["Como consultar en Ver ficha profesor STGR?", "Como imprimir en Ver ficha profesor STGR?", "Como modificar bloque en Ver ficha profesor STGR?"]
pantallas_principales: []
fragmentos: ["profesores.pantalla.ficha_profesor_stgr"]
endpoints: ["/src/profesores/ficha_profesor_stgr"]
source: "docs/catalogo/profesores/flujos/ficha_profesor_stgr.md"
estado_revision: "generado"
---

# Ayuda IA - Ver ficha profesor STGR

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver ficha profesor STGR`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar en Ver ficha profesor STGR?
- Como imprimir en Ver ficha profesor STGR?
- Como modificar bloque en Ver ficha profesor STGR?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar

1. Buscar persona y abrir **ficha profesor stgr**.
2. Revisar bloques visibles según `aPerm`.

Referencias tecnicas para verificar la respuesta:
- `/src/profesores/ficha_profesor_stgr`

## Imprimir

1. Pulsar **[imprimir]** → recarga con `print=1` (forzado en RSTGR).

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Modificar bloque

1. Con permiso de escritura, pulsar **[modificar]** en un bloque → `tablaDB_lista_ver`.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `profesores.pantalla.ficha_profesor_stgr`

## Objetivo

Consultar (e imprimir o modificar con permiso) la ficha STGR de un profesor: nombramientos, curriculum, docencia, congresos, etc.

## Errores Documentados

- `No encuentro a nadie con id_nom: %s`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
