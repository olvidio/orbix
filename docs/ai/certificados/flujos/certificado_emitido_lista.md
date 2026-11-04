---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "certificados"
titulo: "Buscar y ver los certificados emitidos"
flujo: "certificados.certificado_emitido_lista.gestionar.flujo"
preguntas: ["Como buscar y ver los certificados emitidos en Buscar y ver los certificados emitidos?", "Como modificar un certificado en Buscar y ver los certificados emitidos?", "Como eliminar, subir el pdf firmado o enviar un certificado en Buscar y ver los certificados emitidos?", "Como descargar el pdf desde la lista en Buscar y ver los certificados emitidos?"]
pantallas_principales: ["certificados.pantalla.certificado_emitido_lista"]
fragmentos: ["certificados.pantalla.certificado_emitido_lista"]
endpoints: ["/src/certificados/certificado_emitido_lista_datos"]
source: "docs/catalogo/certificados/flujos/certificado_emitido_lista.md"
estado_revision: "generado"
---

# Ayuda IA - Buscar y ver los certificados emitidos

Usa este documento para responder preguntas de usuario sobre como trabajar con `Buscar y ver los certificados emitidos`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como buscar y ver los certificados emitidos en Buscar y ver los certificados emitidos?
- Como modificar un certificado en Buscar y ver los certificados emitidos?
- Como eliminar, subir el pdf firmado o enviar un certificado en Buscar y ver los certificados emitidos?
- Como descargar el pdf desde la lista en Buscar y ver los certificados emitidos?

## Donde Entrar

- Certificado Emitido Lista (`certificados.pantalla.certificado_emitido_lista`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Buscar y ver los certificados emitidos

1. Entrar en **Certificados**. Arriba hay un recuadro **Buscar un certificado** con el botón **Buscar** y un **?** con la ayuda.
2. Debajo aparece la lista de los certificados emitidos en el curso actual (por ejemplo «entre 1/10/2026 y 30/9/2027»). Si no hay ninguno, sale «no hay ninguna fila». Cuando se busca, el título pasa a «Certificados emitidos que coinciden con …» y la lista incluye también los ya enviados.
3. Para buscar, escribir el número y año (por ejemplo «12/15»). Se admiten comodines: un punto (.) es cualquier carácter y .* una cantidad cualquiera; dejar un espacio tras la sigla, por ejemplo «H .*/15», devuelve todos los certificados de H del año 15.
4. La tabla tiene las columnas **Certificado**, **Fecha**, **alumno**, **firmado digitalmente**, **adjunto**, **idioma**, **destino** y **enviado**; se ordena pulsando la cabecera.

Referencias tecnicas para verificar la respuesta:
- `/src/certificados/certificado_emitido_lista_datos`

## Modificar un certificado

1. Marcar la casilla de un certificado y pulsar **modificar**.
2. Se abre el formulario con «Certificado para: …» (la persona) y los campos **Nombre del alumno**, **idioma** (en blanco = latín), **destino**, **Número de certificado**, **Fecha del certificado**, **firmado digitalmente** y **Fecha del envío**.
3. Cambiar lo que haga falta y pulsar **Guardar**.

Referencias tecnicas para verificar la respuesta:
- `/src/certificados/certificado_emitido_lista_datos`

## Eliminar, subir el pdf firmado o enviar un certificado

1. Marcar el certificado.
2. Pulsar **eliminar**, **subir pdf firmado** o **enviar** (no probados en pantalla: pueden cambiar datos y enviar correos).

Referencias tecnicas para verificar la respuesta:
- `/src/certificados/certificado_emitido_lista_datos`

## Descargar el pdf desde la lista

1. Marcar el certificado y pulsar **descargar pdf**: se abre el PDF en otra pestaña.
2. Si no tiene PDF aparece «No hay URL de descarga para el certificado seleccionado».

Referencias tecnicas para verificar la respuesta:
- `/src/certificados/certificado_emitido_lista_datos`

## Pantallas Y Fragmentos Relacionados

- `certificados.pantalla.certificado_emitido_lista`
- `certificados.pantalla.certificado_emitido_lista`

## Objetivo

Consultar los certificados emitidos de la región y, desde la lista, modificarlos, eliminarlos, subir el PDF firmado, enviarlos o descargar el PDF.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
