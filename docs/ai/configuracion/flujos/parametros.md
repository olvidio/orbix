---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "configuracion"
titulo: "Ver los parámetros del esquema"
flujo: "configuracion.parametros.gestionar.flujo"
preguntas: ["Como ver los parámetros del esquema en Ver los parámetros del esquema?", "Como cambiar un parámetro en Ver los parámetros del esquema?"]
pantallas_principales: ["configuracion.pantalla.parametros"]
fragmentos: []
endpoints: ["/src/configuracion/parametros_lista", "/src/configuracion/parametros_update"]
source: "docs/catalogo/configuracion/flujos/parametros.md"
estado_revision: "generado"
---

# Ayuda IA - Ver los parámetros del esquema

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver los parámetros del esquema`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver los parámetros del esquema en Ver los parámetros del esquema?
- Como cambiar un parámetro en Ver los parámetros del esquema?

## Donde Entrar

- Configuración del esquema (`configuracion.pantalla.parametros`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver los parámetros del esquema

1. Abrir **config esquema**: aparece un bloque por cada parámetro con su valor actual.

Referencias tecnicas para verificar la respuesta:
- `/src/configuracion/parametros_lista`
- `/src/configuracion/parametros_update`

## Cambiar un parámetro

1. Editar el bloque que interese.
2. Los periodos del stgr y del crt se escriben como día y mes de inicio y de fin; el resto de parámetros llevan un solo valor (texto, opción o desplegable).
3. Pulsar **Guardar**: sale el aviso «se ha guardado correctamente».

Referencias tecnicas para verificar la respuesta:
- `/src/configuracion/parametros_lista`
- `/src/configuracion/parametros_update`

## Pantallas Y Fragmentos Relacionados

- `configuracion.pantalla.parametros`

## Objetivo

Consultar y cambiar los parámetros generales del esquema: curso escolar, certificados, idioma, ámbito territorial, gestión de calendario, etc.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
