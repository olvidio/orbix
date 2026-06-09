---
tipo: "ayuda_ia"
subtipo: "api_resumen"
modulo: "certificados"
endpoints: 20
estado_revision: "generado"
---

# Resumen API Para Ayuda IA - certificados

Este documento solo sirve como soporte tecnico para la IA local. Para responder a usuarios, priorizar los documentos de `flujos/` y `pantallas/`.

## `/src/certificados/certificado_emitido_adjuntar_data`

- Id: `certificados.certificado_emitido_adjuntar_data`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_emitido_adjuntar_data.php`
- Entrada: `post.id_nom:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/certificados/certificado_emitido_delete`

- Id: `certificados.certificado_emitido_delete`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_emitido_delete.php`
- Entrada: `post.id_item:integer`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/certificados/certificado_emitido_enviar`

- Id: `certificados.certificado_emitido_enviar`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_emitido_enviar.php`
- Entrada: `post.id_item:integer`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/certificados/certificado_emitido_guardar`

- Id: `certificados.certificado_emitido_guardar`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_emitido_guardar.php`
- Entrada: `post.certificado:string`, `post.certificado_old:string`, `post.destino:string`, `post.f_certificado:string`, `post.f_enviado:string`, `post.firmado:string`, `post.id_item:integer`, `post.id_nom:integer`, `post.idioma:string`, `post.nom:string`, `post.nuevo:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/certificados/certificado_emitido_guardar_pdf`

- Id: `certificados.certificado_emitido_guardar_pdf`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_emitido_guardar_pdf.php`
- Entrada: `post.certificado:string`, `post.id_item:integer`, `post.id_nom:integer`, `post.pdf:string`
- Respuesta: `standard_envelope_string_data`

## `/src/certificados/certificado_emitido_imprimir_datos`

- Id: `certificados.certificado_emitido_imprimir_datos`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_emitido_imprimir_datos.php`
- Entrada: `post.id_nom:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/certificados/certificado_emitido_imprimir_mpdf_datos`

- Id: `certificados.certificado_emitido_imprimir_mpdf_datos`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_emitido_imprimir_mpdf_datos.php`
- Entrada: `post.id_item:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/certificados/certificado_emitido_lista_datos`

- Id: `certificados.certificado_emitido_lista_datos`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_emitido_lista_datos.php`
- Entrada: `post.certificado:string`, `post.fincurs_ca_iso:string`, `post.inicurs_ca_iso:string`
- Respuesta: `standard_envelope_string_data`

## `/src/certificados/certificado_emitido_pdf_download`

- Id: `certificados.certificado_emitido_pdf_download`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_emitido_pdf_download.php`
- Entrada: `get.tk:mixed`
- Respuesta: `raw_response`

## `/src/certificados/certificado_emitido_pdf_upload`

- Id: `certificados.certificado_emitido_pdf_upload`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_emitido_pdf_upload.php`
- Entrada: `post.certificado:string`, `post.destino:string`, `post.f_certificado:string`, `post.f_enviado:string`, `post.firmado:string`, `post.id_item:integer`, `post.id_nom:integer`, `post.idioma:string`, `post.solo_pdf:integer`
- Respuesta: `raw_response`

## `/src/certificados/certificado_emitido_upload_firmado_data`

- Id: `certificados.certificado_emitido_upload_firmado_data`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_emitido_upload_firmado_data.php`
- Entrada: `post.id_item:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/certificados/certificado_emitido_ver_datos`

- Id: `certificados.certificado_emitido_ver_datos`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_emitido_ver_datos.php`
- Entrada: `post.id_item:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/certificados/certificado_recibido_adjuntar_data`

- Id: `certificados.certificado_recibido_adjuntar_data`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_recibido_adjuntar_data.php`
- Entrada: `post.id_nom:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/certificados/certificado_recibido_delete`

- Id: `certificados.certificado_recibido_delete`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_recibido_delete.php`
- Entrada: `post.id_item:integer`, `post.sel:array`
- Respuesta: `standard_envelope_string_data`

## `/src/certificados/certificado_recibido_guardar`

- Id: `certificados.certificado_recibido_guardar`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_recibido_guardar.php`
- Entrada: `post.certificado:string`, `post.certificado_old:string`, `post.destino:string`, `post.f_certificado:string`, `post.f_recibido:string`, `post.firmado:string`, `post.id_item:integer`, `post.id_nom:integer`, `post.idioma:string`, `post.nom:string`, `post.nuevo:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/certificados/certificado_recibido_modificar_data`

- Id: `certificados.certificado_recibido_modificar_data`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_recibido_modificar_data.php`
- Entrada: `post.id_item:integer`
- Respuesta: `standard_envelope_string_data`

## `/src/certificados/certificado_recibido_pdf_download`

- Id: `certificados.certificado_recibido_pdf_download`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_recibido_pdf_download.php`
- Entrada: `get.tk:mixed`
- Respuesta: `raw_response`

## `/src/certificados/certificado_recibido_pdf_upload`

- Id: `certificados.certificado_recibido_pdf_upload`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_recibido_pdf_upload.php`
- Entrada: `post.certificado:string`, `post.destino:string`, `post.f_certificado:string`, `post.f_recibido:string`, `post.firmado:string`, `post.id_item:integer`, `post.id_nom:integer`, `post.idioma:string`
- Respuesta: `raw_response`

## `/src/certificados/certificados_locales_data`

- Id: `certificados.certificados_locales_data`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificados_locales_data.php`
- Entrada: ninguna detectada.
- Respuesta: `standard_envelope_string_data`

## `/src/certificados/textos_certificados`

- Id: `certificados.textos_certificados`
- Controller: `src/certificados/infrastructure/ui/http/controllers/textos_certificados.php`
- Entrada: ninguna detectada.
- Respuesta: `pendiente_revision`
