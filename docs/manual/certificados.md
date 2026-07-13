---
tipo: "manual_usuario"
modulo: "certificados"
flujos: 18
estado_revision: "generado"
---

# Manual De Usuario - certificados

Este manual es un borrador generado desde `docs/catalogo`. Debe revisarse para ajustar nombres de menu, permisos, validaciones y lenguaje final de usuario.

## Como Usar Este Manual

Cada apartado describe una tarea de usuario. Las rutas de menu y nombres visibles pueden necesitar revision manual.

## Certificado Emitido

### Para Que Sirve

Imprimir, guardar o eliminar un certificado emitido desde el formulario de impresión.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

#### Guardar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Botón expuesto solo si `soy_region_stgr()` en el builder de lista; sin check adicional aquí.
- Sin control de permisos propio en el controller; acceso desde formularios de región STGR.

### Referencias Internas

- Flujo: `certificados.certificado_emitido.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/certificado_emitido.md`

## Certificado Emitido Adjuntar

### Para Que Sirve

Adjuntar PDF de certificado emitido a una persona.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio; formulario abierto desde dossier o flujo de persona.

### Referencias Internas

- Flujo: `certificados.certificado_emitido_adjuntar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/certificado_emitido_adjuntar.md`

## Certificado Emitido Enviar

### Para Que Sirve

Enviar certificado emitido a la delegación destino del alumno (copia + anuncio).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Botón expuesto solo si `soy_region_stgr()` en el listado; sin check adicional aquí.

### Referencias Internas

- Flujo: `certificados.certificado_emitido_enviar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/certificado_emitido_enviar.md`

## Certificado Emitido Guardar Pdf

### Para Que Sirve

Persistir el PDF generado y el número de certificado en BD.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio; invocado tras generar PDF en región STGR.

### Referencias Internas

- Flujo: `certificados.certificado_emitido_guardar_pdf.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/certificado_emitido_guardar_pdf.md`

## Certificado Emitido Imprimir

### Para Que Sirve

Preparar datos e iniciar impresión de certificado nuevo para un alumno.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Requiere `$_SESSION['oConfig']`; sin permisos de oficina adicionales en el controller.

### Referencias Internas

- Flujo: `certificados.certificado_emitido_imprimir.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/certificado_emitido_imprimir.md`

## Certificado Emitido Imprimir Mpdf

### Para Que Sirve

Generar el PDF del certificado con notas y textos traducidos.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Requiere `$_SESSION['oConfig']` y esquema región STGR en sesión para notas de otras regiones.

### Referencias Internas

- Flujo: `certificados.certificado_emitido_imprimir_mpdf.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/certificado_emitido_imprimir_mpdf.md`

## Certificado Emitido Lista

### Para Que Sirve

Consultar y gestionar certificados emitidos pendientes de envío en la región STGR.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Botones de mutación condicionados a `DelegacionRepository::soy_region_stgr()` en el dominio.
- La pantalla frontend restringe acceso a ámbito `rstgr` o `r` (`OrbixRuntime::miAmbito()`).

### Referencias Internas

- Flujo: `certificados.certificado_emitido_lista.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/certificado_emitido_lista.md`

## Certificado Emitido Pdf Download

### Para Que Sirve

Descargar PDF adjunto de certificado emitido.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Autorización vía token firmado generado en frontend (`SignedDownloadToken`); no comprueba sesión explícita.

### Referencias Internas

- Flujo: `certificados.certificado_emitido_pdf_download.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/certificado_emitido_pdf_download.md`

## Certificado Emitido Pdf Upload

### Para Que Sirve

Subir fichero PDF al API de certificados emitidos.

### Donde Entrar

- Certificado Emitido Pdf Upload (frontend/certificados/controller/certificado_emitido_pdf_upload.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Validación HashFront en formularios frontend; sin permisos adicionales en el controller.

### Referencias Internas

- Flujo: `certificados.certificado_emitido_pdf_upload.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/certificado_emitido_pdf_upload.md`

## Certificado Emitido Upload Firmado

### Para Que Sirve

Subir el PDF firmado de un certificado ya emitido.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio; acceso desde listado de emitidos (región STGR).

### Referencias Internas

- Flujo: `certificados.certificado_emitido_upload_firmado.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/certificado_emitido_upload_firmado.md`

## Certificado Emitido Ver

### Para Que Sirve

Consultar detalle de un certificado emitido seleccionado en el listado.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio; autorización en frontend + `$_SESSION['oPerm']` al abrir desde el listado.

### Referencias Internas

- Flujo: `certificados.certificado_emitido_ver.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/certificado_emitido_ver.md`

## Certificado Recibido

### Para Que Sirve

Gestionar certificados recibidos de una persona (adjuntar, modificar, eliminar).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

#### Guardar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Listado en dossier persona (`Select_certificados_de_una_persona`); permiso dossier en frontend.
- Sin control de permisos propio; formularios en dossier de persona o modificar recibido.

### Referencias Internas

- Flujo: `certificados.certificado_recibido.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/certificado_recibido.md`

## Certificado Recibido Adjuntar

### Para Que Sirve

Registrar un certificado recibido nuevo con PDF.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio; dossier persona o flujo adjuntar.

### Referencias Internas

- Flujo: `certificados.certificado_recibido_adjuntar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/certificado_recibido_adjuntar.md`

## Certificado Recibido Modificar

### Para Que Sirve

Modificar metadatos de certificado recibido.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio; acceso desde listado dossier o modificar.

### Referencias Internas

- Flujo: `certificados.certificado_recibido_modificar.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/certificado_recibido_modificar.md`

## Certificado Recibido Pdf Download

### Para Que Sirve

Descargar PDF de certificado recibido.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Autorización vía token firmado; no sesión explícita.

### Referencias Internas

- Flujo: `certificados.certificado_recibido_pdf_download.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/certificado_recibido_pdf_download.md`

## Certificado Recibido Pdf Upload

### Para Que Sirve

Subir PDF de certificado recibido.

### Donde Entrar

- Certificado Recibido Pdf Upload (frontend/certificados/controller/certificado_recibido_pdf_upload.php)
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- HashFront en formularios frontend.

### Referencias Internas

- Flujo: `certificados.certificado_recibido_pdf_upload.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/certificado_recibido_pdf_upload.md`

## Certificados Locales

### Para Que Sirve

Cargar desplegable de idiomas en formularios de certificados.

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Obtener Datos

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- Sin control de permisos propio.

### Referencias Internas

- Flujo: `certificados.certificados_locales.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/certificados_locales.md`

## Textos Certificados

### Para Que Sirve

Plantilla de textos legales incluida al generar PDF (no flujo de usuario directo).

### Donde Entrar

- Pendiente de revisar.
- Ruta de menu: pendiente de documentar.

### Tareas Habituales

#### Ejecutar

1. Revisar manualmente los pasos de esta accion.

### Errores O Avisos Frecuentes

- No hay errores documentados en el catalogo para este flujo.

### Permisos

- No aplica (include server-side).

### Referencias Internas

- Flujo: `certificados.textos_certificados.gestionar.flujo`
- Fichero catalogo: `docs/catalogo/certificados/flujos/textos_certificados.md`

## Revision Pendiente

- Sustituir nombres tecnicos por nombres visibles en la aplicacion.
- Completar rutas de menu.
- Confirmar permisos necesarios.
- Anadir capturas o ejemplos si se quiere publicar para usuarios finales.
