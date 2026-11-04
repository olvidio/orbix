---
tipo: "manual_usuario"
modulo: "certificados"
flujos: 18
estado_revision: "revisado"
---

# Manual De Usuario - certificados

Manual generado desde `docs/catalogo` con rutas de menú del catálogo. Úsalo como guía de usuario; los detalles técnicos están en el catálogo.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas Legacy/Pills2 vienen del catálogo (`## Ruta de menú`).

## Emitir, guardar o eliminar un certificado

### Para Que Sirve

Imprimir, guardar o eliminar un certificado emitido desde el formulario de impresión.

### Donde Entrar

- Certificado Emitido Imprimir (frontend/certificados/controller/certificado_emitido_imprimir.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Emitir, guardar o eliminar un certificado

1. Abrir el formulario de impresión del certificado.
2. Pulsar imprimir para generar el PDF y guardar para registrarlo con su número.
3. Para quitarlo, eliminarlo y confirmar.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Botón expuesto solo si `soy_region_stgr()` en el builder de lista; sin check adicional aquí.
- Sin control de permisos propio en el controller; acceso desde formularios de región STGR.

### Referencias Internas

- Flujo: `certificados.certificado_emitido.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/certificado_emitido.md`

## Adjuntar un certificado emitido

### Para Que Sirve

Adjuntar a una persona un PDF de un certificado ya emitido.

### Donde Entrar

- Certificado Emitido Adjuntar (frontend/certificados/controller/certificado_emitido_adjuntar.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Adjuntar un certificado emitido

1. Abrir la ficha de la persona y su dossier de certificados.
2. Pulsar **adjuntar nuevo certificado**.
3. Rellenar los datos y elegir el PDF.
4. Guardar.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio; formulario abierto desde dossier o flujo de persona.

### Referencias Internas

- Flujo: `certificados.certificado_emitido_adjuntar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/certificado_emitido_adjuntar.md`

## Enviar un certificado a otra delegación

### Para Que Sirve

Enviar un certificado emitido a la delegación destino del alumno.

### Donde Entrar

- Certificado Emitido Lista (frontend/certificados/controller/certificado_emitido_lista.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Enviar un certificado a otra delegación

1. En **Certificados**, marcar el certificado.
2. Pulsar enviar: se manda una copia a la delegación del alumno y se le avisa.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Botón expuesto solo si `soy_region_stgr()` en el listado; sin check adicional aquí.

### Referencias Internas

- Flujo: `certificados.certificado_emitido_enviar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/certificado_emitido_enviar.md`

## Guardar el certificado generado

### Para Que Sirve

Guardar en el sistema el PDF generado y el número del certificado.

### Donde Entrar

- Certificado Emitido 2 Mpdf (frontend/certificados/controller/certificado_emitido_2_mpdf.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Guardar el certificado generado

1. Tras generar el PDF, confirmar el guardado.
2. El certificado queda registrado con su número y su PDF.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio; invocado tras generar PDF en región STGR.

### Referencias Internas

- Flujo: `certificados.certificado_emitido_guardar_pdf.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/certificado_emitido_guardar_pdf.md`

## Emitir un certificado nuevo

### Para Que Sirve

Preparar los datos de un certificado nuevo de un alumno y empezar su impresión.

### Donde Entrar

- Certificado Emitido Imprimir (frontend/certificados/controller/certificado_emitido_imprimir.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Emitir un certificado nuevo

1. Abrir la ficha del alumno.
2. En el dossier de certificados elegir emitir un certificado nuevo.
3. Elegir el idioma y los datos del certificado y pulsar imprimir.
4. Se genera el PDF con las notas y los textos legales en el idioma elegido.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Requiere `$_SESSION['oConfig']`; sin permisos de oficina adicionales en el controller.

### Referencias Internas

- Flujo: `certificados.certificado_emitido_imprimir.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/certificado_emitido_imprimir.md`

## Obtener el PDF del certificado

### Para Que Sirve

Generar el PDF del certificado con las notas y los textos traducidos al idioma elegido.

### Donde Entrar

- Certificado Emitido Imprimir Mpdf (frontend/certificados/controller/certificado_emitido_imprimir_mpdf.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Obtener el PDF del certificado

1. Emitir el certificado desde la ficha del alumno.
2. El PDF se genera automáticamente y se abre para imprimir o guardar.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Requiere `$_SESSION['oConfig']` y esquema región STGR en sesión para notas de otras regiones.

### Referencias Internas

- Flujo: `certificados.certificado_emitido_imprimir_mpdf.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/certificado_emitido_imprimir_mpdf.md`

## Buscar y ver los certificados emitidos

### Para Que Sirve

Consultar los certificados emitidos de la región y, desde la lista, modificarlos, eliminarlos, subir el PDF firmado, enviarlos o descargar el PDF.

### Donde Entrar

- Certificado Emitido Lista (frontend/certificados/controller/certificado_emitido_lista.php)
- **Legacy:** —
- **Pills2:** ESTUDIOS > Actas y certificados > Certificados

### Tareas Habituales

#### Buscar y ver los certificados emitidos

1. Entrar en **Certificados**. Arriba hay un recuadro **Buscar un certificado** con el botón **Buscar** y un **?** con la ayuda.
2. Debajo aparece la lista de los certificados emitidos en el curso actual (por ejemplo «entre 1/10/2026 y 30/9/2027»). Si no hay ninguno, sale «no hay ninguna fila». Cuando se busca, el título pasa a «Certificados emitidos que coinciden con …» y la lista incluye también los ya enviados.
3. Para buscar, escribir el número y año (por ejemplo «12/15»). Se admiten comodines: un punto (.) es cualquier carácter y .* una cantidad cualquiera; dejar un espacio tras la sigla, por ejemplo «H .*/15», devuelve todos los certificados de H del año 15.
4. La tabla tiene las columnas **Certificado**, **Fecha**, **alumno**, **firmado digitalmente**, **adjunto**, **idioma**, **destino** y **enviado**; se ordena pulsando la cabecera.

#### Modificar un certificado

1. Marcar la casilla de un certificado y pulsar **modificar**.
2. Se abre el formulario con «Certificado para: …» (la persona) y los campos **Nombre del alumno**, **idioma** (en blanco = latín), **destino**, **Número de certificado**, **Fecha del certificado**, **firmado digitalmente** y **Fecha del envío**.
3. Cambiar lo que haga falta y pulsar **Guardar**.

#### Eliminar, subir el PDF firmado o enviar un certificado

1. Marcar el certificado.
2. Pulsar **eliminar**, **subir pdf firmado** o **enviar** (no probados en pantalla: pueden cambiar datos y enviar correos).

#### Descargar el PDF desde la lista

1. Marcar el certificado y pulsar **descargar pdf**: se abre el PDF en otra pestaña.
2. Si no tiene PDF aparece «No hay URL de descarga para el certificado seleccionado».

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Botones de mutación condicionados a `DelegacionRepository::soy_region_stgr()` en el dominio.
- La pantalla frontend restringe acceso a ámbito `rstgr` o `r` (`OrbixRuntime::miAmbito()`).

### Referencias Internas

- Flujo: `certificados.certificado_emitido_lista.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/certificado_emitido_lista.md`

## Descargar el PDF de un certificado emitido

### Para Que Sirve

Descargar el PDF de un certificado emitido.

### Donde Entrar

- Certificado Emitido Pdf Download (frontend/certificados/controller/certificado_emitido_pdf_download.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Descargar el PDF de un certificado emitido

1. En la lista o en el dossier, pulsar descargar.
2. El PDF se abre en una pestaña nueva; el enlace caduca a los pocos minutos.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Autorización vía token firmado generado en frontend (`SignedDownloadToken`); no comprueba sesión explícita.

### Referencias Internas

- Flujo: `certificados.certificado_emitido_pdf_download.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/certificado_emitido_pdf_download.md`

## Subir el PDF de un certificado

### Para Que Sirve

Subir el fichero PDF de un certificado emitido.

### Donde Entrar

- Certificado Emitido Pdf Upload (frontend/certificados/controller/certificado_emitido_pdf_upload.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Subir el PDF de un certificado

1. Elegir el fichero PDF en el formulario de adjuntar.
2. Guardar. Si es demasiado grande el servidor lo rechaza con un aviso de tamaño.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Validación HashF en formularios frontend; sin permisos adicionales en el controller.

### Referencias Internas

- Flujo: `certificados.certificado_emitido_pdf_upload.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/certificado_emitido_pdf_upload.md`

## Subir el certificado firmado

### Para Que Sirve

Subir el PDF firmado de un certificado ya emitido.

### Donde Entrar

- Certificado Emitido Upload Firmado (frontend/certificados/controller/certificado_emitido_upload_firmado.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Subir el certificado firmado

1. Imprimir y firmar el certificado.
2. Abrir el certificado en la lista y elegir el PDF firmado.
3. Subirlo; el certificado queda con su versión firmada.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio; acceso desde listado de emitidos (región STGR).

### Referencias Internas

- Flujo: `certificados.certificado_emitido_upload_firmado.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/certificado_emitido_upload_firmado.md`

## Consultar un certificado emitido

### Para Que Sirve

Ver el detalle de un certificado emitido.

### Donde Entrar

- Certificado Emitido Ver (frontend/certificados/controller/certificado_emitido_ver.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Consultar un certificado emitido

1. En **Certificados**, buscar y pulsar el certificado.
2. Se muestran sus datos (alumno, fecha, número) y el PDF si lo tiene.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio; autorización en frontend + `$_SESSION['oPerm']` al abrir desde el listado.

### Referencias Internas

- Flujo: `certificados.certificado_emitido_ver.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/certificado_emitido_ver.md`

## certificados recibidos

### Para Que Sirve

Gestionar los certificados recibidos de una persona: adjuntar, modificar y eliminar.

### Donde Entrar

- Certificado Recibido Adjuntar (frontend/certificados/controller/certificado_recibido_adjuntar.php)
- Certificado Recibido Modificar (frontend/certificados/controller/certificado_recibido_modificar.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Gestionar certificados recibidos

1. Adjuntar: pulsar **adjuntar nuevo certificado**, rellenar y subir el PDF.
2. Modificar: abrir el certificado, cambiar los datos y guardar.
3. Eliminar: pulsarlo y confirmar «¿seguro que desea borrar el certificado?».

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Listado en dossier persona (`Select_certificados_de_una_persona`); permiso dossier en frontend.
- Sin control de permisos propio; formularios en dossier de persona o modificar recibido.

### Referencias Internas

- Flujo: `certificados.certificado_recibido.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/certificado_recibido.md`

## Registrar un certificado recibido

### Para Que Sirve

Registrar un certificado recibido de otra delegación, con su PDF.

### Donde Entrar

- Certificado Recibido Adjuntar (frontend/certificados/controller/certificado_recibido_adjuntar.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Registrar un certificado recibido

1. Abrir la ficha de la persona y su dossier de certificados.
2. Pulsar **adjuntar nuevo certificado** y elegir el tipo recibido.
3. Rellenar los datos y elegir el PDF.
4. Guardar.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio; dossier persona o flujo adjuntar.

### Referencias Internas

- Flujo: `certificados.certificado_recibido_adjuntar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/certificado_recibido_adjuntar.md`

## Modificar un certificado recibido

### Para Que Sirve

Corregir los datos de un certificado recibido.

### Donde Entrar

- Certificado Recibido Modificar (frontend/certificados/controller/certificado_recibido_modificar.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Modificar un certificado recibido

1. En el dossier de certificados, pulsar el certificado recibido.
2. Cambiar los datos y guardar.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio; acceso desde listado dossier o modificar.

### Referencias Internas

- Flujo: `certificados.certificado_recibido_modificar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/certificado_recibido_modificar.md`

## Descargar el PDF de un certificado recibido

### Para Que Sirve

Descargar el PDF de un certificado recibido.

### Donde Entrar

- Certificado Recibido Pdf Download (frontend/certificados/controller/certificado_recibido_pdf_download.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Descargar el PDF de un certificado recibido

1. En el dossier de certificados, pulsar descargar en el certificado recibido.
2. El PDF se abre en una pestaña nueva.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Autorización vía token firmado; no sesión explícita.

### Referencias Internas

- Flujo: `certificados.certificado_recibido_pdf_download.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/certificado_recibido_pdf_download.md`

## Subir el PDF de un certificado recibido

### Para Que Sirve

Subir el PDF de un certificado recibido.

### Donde Entrar

- Certificado Recibido Pdf Upload (frontend/certificados/controller/certificado_recibido_pdf_upload.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Subir el PDF de un certificado recibido

1. En el formulario de adjuntar, elegir el PDF y guardar.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- HashF en formularios frontend.

### Referencias Internas

- Flujo: `certificados.certificado_recibido_pdf_upload.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/certificado_recibido_pdf_upload.md`

## Elegir el idioma del certificado

### Para Que Sirve

Cargar la lista de idiomas disponibles en los formularios de certificados.

### Donde Entrar

- Certificado Emitido Adjuntar (frontend/certificados/controller/certificado_emitido_adjuntar.php)
- Certificado Recibido Adjuntar (frontend/certificados/controller/certificado_recibido_adjuntar.php)
- sin entrada de menú en el índice

### Tareas Habituales

#### Elegir el idioma del certificado

1. En el formulario de certificado, abrir el desplegable de idioma.
2. Elegir el idioma en que se emitirá.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio.

### Referencias Internas

- Flujo: `certificados.certificados_locales.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/certificados_locales.md`

## Textos legales del certificado

### Para Que Sirve

Textos legales que se incluyen automáticamente al generar el PDF (no requiere acción del usuario).

### Donde Entrar

- Acceso vía fragmento, dossier o pantalla relacionada (sin pantalla principal propia).
- sin entrada de menú en el índice

### Tareas Habituales

#### Textos legales del certificado

1. Al emitir un certificado, el sistema incluye los textos legales en el idioma elegido.
2. No hace falta hacer nada.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- No aplica (include server-side).

### Referencias Internas

- Flujo: `certificados.textos_certificados.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/textos_certificados.md`

## Notas

- Rutas de menú propagadas desde el catálogo; revisar en UI si alguna etiqueta de menú cambió.
- Permisos y errores se toman de las fichas API relacionadas.
- Fuente: `docs/catalogo/certificados/flujos/`.
