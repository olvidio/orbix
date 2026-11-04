---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "profesores"
titulo: "Consultar la ficha de un profesor"
flujo: "profesores.ficha_profesor_stgr.gestionar.flujo"
preguntas: ["Como consultar la ficha de un profesor en Consultar la ficha de un profesor?", "Como imprimir la ficha en Consultar la ficha de un profesor?", "Como modificar un bloque de la ficha en Consultar la ficha de un profesor?"]
pantallas_principales: ["profesores.pantalla.ficha_profesor_stgr"]
fragmentos: ["profesores.pantalla.ficha_profesor_stgr"]
endpoints: ["/src/profesores/ficha_profesor_stgr"]
source: "docs/catalogo/profesores/flujos/ficha_profesor_stgr.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar la ficha de un profesor

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar la ficha de un profesor`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar la ficha de un profesor en Consultar la ficha de un profesor?
- Como imprimir la ficha en Consultar la ficha de un profesor?
- Como modificar un bloque de la ficha en Consultar la ficha de un profesor?

## Donde Entrar

- Ficha profesor STGR (`profesores.pantalla.ficha_profesor_stgr`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar la ficha de un profesor

1. Buscar a la persona y pulsar **ficha profesor stgr**.
2. Se muestran los bloques de la ficha que el usuario tiene permiso para ver.

Referencias tecnicas para verificar la respuesta:
- `/src/profesores/ficha_profesor_stgr`

## Imprimir la ficha

1. Pulsar **[imprimir]**: la ficha se vuelve a mostrar en formato de impresión (en las regiones del stgr siempre sale así).

Referencias tecnicas para verificar la respuesta:
- `/src/profesores/ficha_profesor_stgr`

## Modificar un bloque de la ficha

1. Con permiso de escritura, pulsar **[modificar]** en el bloque que se quiere cambiar.
2. Se abre la tabla de ese bloque para editarla.

Referencias tecnicas para verificar la respuesta:
- `/src/profesores/ficha_profesor_stgr`

## Pantallas Y Fragmentos Relacionados

- `profesores.pantalla.ficha_profesor_stgr`
- `profesores.pantalla.ficha_profesor_stgr`

## Objetivo

Consultar la ficha stgr de un profesor (nombramientos, curriculum, docencia, congresos…) y, con permiso, imprimirla o modificarla.

## Errores Documentados

- `No encuentro a nadie con id_nom: %s`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
