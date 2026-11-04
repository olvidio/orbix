---
id: "certificados.certificado_emitido_lista.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "certificados"
nombre: "Flujo - Buscar y ver los certificados emitidos"
capacidad: "certificados.certificado_emitido_lista.gestionar"
pantallas_principales: ["certificados.pantalla.certificado_emitido_lista"]
fragmentos: ["certificados.pantalla.certificado_emitido_lista"]
acciones: ["obtener_datos"]
endpoints: ["/src/certificados/certificado_emitido_lista_datos"]
estado_revision: "revisado"
---

# Flujo - Gestionar Certificado Emitido Lista

Flujo revisado contra `src/certificados/` y `frontend/certificados/`.

## Objetivo De Usuario

Consultar los certificados emitidos de la región y, desde la lista, modificarlos, eliminarlos, subir el PDF firmado, enviarlos o descargar el PDF.

## Punto De Entrada

Menú **ESTUDIOS > Actas y certificados > Certificados** (solo en las regiones del stgr).

## Fragmentos O Pantallas Auxiliares

- `certificados.pantalla.certificado_emitido_lista`

## Escenarios Inferidos

### Buscar y ver los certificados emitidos

Pasos propuestos:
1. Entrar en **Certificados**. Arriba hay un recuadro **Buscar un certificado** con el botón **Buscar** y un **?** con la ayuda.
2. Debajo aparece la lista de los certificados emitidos en el curso actual (por ejemplo «entre 1/10/2026 y 30/9/2027»). Si no hay ninguno, sale «no hay ninguna fila». Cuando se busca, el título pasa a «Certificados emitidos que coinciden con …» y la lista incluye también los ya enviados.
3. Para buscar, escribir el número y año (por ejemplo «12/15»). Se admiten comodines: un punto (.) es cualquier carácter y .* una cantidad cualquiera; dejar un espacio tras la sigla, por ejemplo «H .*/15», devuelve todos los certificados de H del año 15.
4. La tabla tiene las columnas **Certificado**, **Fecha**, **alumno**, **firmado digitalmente**, **adjunto**, **idioma**, **destino** y **enviado**; se ordena pulsando la cabecera.

Endpoints asociados:
- `/src/certificados/certificado_emitido_lista_datos`

### Modificar un certificado

Pasos propuestos:
1. Marcar la casilla de un certificado y pulsar **modificar**.
2. Se abre el formulario con «Certificado para: …» (la persona) y los campos **Nombre del alumno**, **idioma** (en blanco = latín), **destino**, **Número de certificado**, **Fecha del certificado**, **firmado digitalmente** y **Fecha del envío**.
3. Cambiar lo que haga falta y pulsar **Guardar**.

Endpoints asociados:
- `/src/certificados/certificado_emitido_lista_datos`

### Eliminar, subir el PDF firmado o enviar un certificado

Pasos propuestos:
1. Marcar el certificado.
2. Pulsar **eliminar**, **subir pdf firmado** o **enviar** (no probados en pantalla: pueden cambiar datos y enviar correos).

Endpoints asociados:
- `/src/certificados/certificado_emitido_lista_datos`

### Descargar el PDF desde la lista

Pasos propuestos:
1. Marcar el certificado y pulsar **descargar pdf**: se abre el PDF en otra pestaña.
2. Si no tiene PDF aparece «No hay URL de descarga para el certificado seleccionado».

Endpoints asociados:
- `/src/certificados/certificado_emitido_lista_datos`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.certificado`
- `form.mod`
- `form.sel`
- `html.btn_ok`
- `html.certificado`
- `html.mod`
- `html.refresh`
- `post.certificado`
- `post.refresh`
- `post.stack`
- `post.titulo`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_descargar_pdf`
- `fnjs_eliminar`
- `fnjs_enviar`
- `fnjs_enviar_certificado`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`
- `fnjs_modificar`
- `fnjs_nuevo`
- `fnjs_solo_uno`
- `fnjs_upload_certificado`

## Endpoints Del Flujo

- `/src/certificados/certificado_emitido_lista_datos`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** —
- **Pills2:** ESTUDIOS > Actas y certificados > Certificados
