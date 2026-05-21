---
tipo: "relacion_pantallas_api"
modulo: "certificados"
pantallas: 14
endpoints_api: 20
capacidades: 18
estado_revision: "generado"
---

# Relacion Pantallas API - certificados

Indice generado automaticamente para cruzar pantallas frontend, capacidades y endpoints API.

## Por Pantalla

### `certificados.pantalla.certificado_emitido_2_mpdf`

- Controller: `frontend/certificados/controller/certificado_emitido_2_mpdf.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/certificados/certificado_emitido_guardar_pdf`

Capacidades:
- `certificados.certificado_emitido_guardar_pdf.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `certificados.pantalla.certificado_emitido_adjuntar`

- Controller: `frontend/certificados/controller/certificado_emitido_adjuntar.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/certificados/certificado_emitido_adjuntar_data`
- `/src/certificados/certificados_locales_data`

Capacidades:
- `certificados.certificado_emitido_adjuntar.gestionar`
- `certificados.certificados_locales.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `certificados.pantalla.certificado_emitido_aviso_html`

- Controller: `frontend/certificados/controller/certificado_emitido_aviso_html.php`
- Subtipo: `pantalla`

Endpoints directos:
- Ninguno detectado.

Capacidades:
- Ninguna detectada.

Endpoints aportados por capacidades:
- Ninguno adicional.

### `certificados.pantalla.certificado_emitido_imprimir`

- Controller: `frontend/certificados/controller/certificado_emitido_imprimir.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/certificados/certificado_emitido_delete`
- `/src/certificados/certificado_emitido_imprimir_datos`
- `/src/shared/locales_posibles`

Capacidades:
- `certificados.certificado_emitido.gestionar`
- `certificados.certificado_emitido_imprimir.gestionar`

Endpoints aportados por capacidades:
- `/src/certificados/certificado_emitido_guardar`

### `certificados.pantalla.certificado_emitido_imprimir_mpdf`

- Controller: `frontend/certificados/controller/certificado_emitido_imprimir_mpdf.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/certificados/certificado_emitido_imprimir_mpdf_datos`

Capacidades:
- `certificados.certificado_emitido_imprimir_mpdf.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `certificados.pantalla.certificado_emitido_lista`

- Controller: `frontend/certificados/controller/certificado_emitido_lista.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/certificados/certificado_emitido_lista_datos`

Capacidades:
- `certificados.certificado_emitido_lista.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `certificados.pantalla.certificado_emitido_pdf_download`

- Controller: `frontend/certificados/controller/certificado_emitido_pdf_download.php`
- Subtipo: `pantalla`

Endpoints directos:
- `/src/certificados/infrastructure/ui/http/controllers/certificado_emitido_pdf_download`

Capacidades:
- Ninguna detectada.

Endpoints aportados por capacidades:
- Ninguno adicional.

### `certificados.pantalla.certificado_emitido_pdf_upload`

- Controller: `frontend/certificados/controller/certificado_emitido_pdf_upload.php`
- Subtipo: `pantalla`

Endpoints directos:
- `/src/certificados/certificado_emitido_pdf_upload`
- `/src/certificados/infrastructure/ui/http/controllers/certificado_emitido_pdf_upload`

Capacidades:
- `certificados.certificado_emitido_pdf_upload.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `certificados.pantalla.certificado_emitido_upload_firmado`

- Controller: `frontend/certificados/controller/certificado_emitido_upload_firmado.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/certificados/certificado_emitido_upload_firmado_data`

Capacidades:
- `certificados.certificado_emitido_upload_firmado.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `certificados.pantalla.certificado_emitido_ver`

- Controller: `frontend/certificados/controller/certificado_emitido_ver.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/certificados/certificado_emitido_ver_datos`
- `/src/shared/locales_posibles`

Capacidades:
- `certificados.certificado_emitido_ver.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `certificados.pantalla.certificado_recibido_adjuntar`

- Controller: `frontend/certificados/controller/certificado_recibido_adjuntar.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/certificados/certificado_recibido_adjuntar_data`
- `/src/certificados/certificado_recibido_guardar`
- `/src/certificados/certificado_recibido_pdf_upload`
- `/src/certificados/certificados_locales_data`

Capacidades:
- `certificados.certificado_recibido.gestionar`
- `certificados.certificado_recibido_adjuntar.gestionar`
- `certificados.certificado_recibido_pdf_upload.gestionar`
- `certificados.certificados_locales.gestionar`

Endpoints aportados por capacidades:
- `/src/certificados/certificado_recibido_delete`

### `certificados.pantalla.certificado_recibido_modificar`

- Controller: `frontend/certificados/controller/certificado_recibido_modificar.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/certificados/certificado_recibido_guardar`
- `/src/certificados/certificado_recibido_modificar_data`
- `/src/certificados/certificado_recibido_pdf_upload`

Capacidades:
- `certificados.certificado_recibido.gestionar`
- `certificados.certificado_recibido_modificar.gestionar`
- `certificados.certificado_recibido_pdf_upload.gestionar`

Endpoints aportados por capacidades:
- `/src/certificados/certificado_recibido_delete`

### `certificados.pantalla.certificado_recibido_pdf_download`

- Controller: `frontend/certificados/controller/certificado_recibido_pdf_download.php`
- Subtipo: `pantalla`

Endpoints directos:
- `/src/certificados/infrastructure/ui/http/controllers/certificado_recibido_pdf_download`

Capacidades:
- Ninguna detectada.

Endpoints aportados por capacidades:
- Ninguno adicional.

### `certificados.pantalla.certificado_recibido_pdf_upload`

- Controller: `frontend/certificados/controller/certificado_recibido_pdf_upload.php`
- Subtipo: `pantalla`

Endpoints directos:
- `/src/certificados/certificado_recibido_pdf_upload`
- `/src/certificados/infrastructure/ui/http/controllers/certificado_recibido_pdf_upload`

Capacidades:
- `certificados.certificado_recibido_pdf_upload.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

## Por Endpoint API

### `/src/certificados/certificado_emitido_adjuntar_data`

Pantallas directas:
- `certificados.pantalla.certificado_emitido_adjuntar`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/certificados/certificado_emitido_delete`

Pantallas directas:
- `certificados.pantalla.certificado_emitido_imprimir`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/certificados/certificado_emitido_enviar`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/certificados/certificado_emitido_guardar`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- `certificados.pantalla.certificado_emitido_imprimir`

### `/src/certificados/certificado_emitido_guardar_pdf`

Pantallas directas:
- `certificados.pantalla.certificado_emitido_2_mpdf`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/certificados/certificado_emitido_imprimir_datos`

Pantallas directas:
- `certificados.pantalla.certificado_emitido_imprimir`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/certificados/certificado_emitido_imprimir_mpdf_datos`

Pantallas directas:
- `certificados.pantalla.certificado_emitido_imprimir_mpdf`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/certificados/certificado_emitido_lista_datos`

Pantallas directas:
- `certificados.pantalla.certificado_emitido_lista`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/certificados/certificado_emitido_pdf_download`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/certificados/certificado_emitido_pdf_upload`

Pantallas directas:
- `certificados.pantalla.certificado_emitido_pdf_upload`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/certificados/certificado_emitido_upload_firmado_data`

Pantallas directas:
- `certificados.pantalla.certificado_emitido_upload_firmado`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/certificados/certificado_emitido_ver_datos`

Pantallas directas:
- `certificados.pantalla.certificado_emitido_ver`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/certificados/certificado_recibido_adjuntar_data`

Pantallas directas:
- `certificados.pantalla.certificado_recibido_adjuntar`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/certificados/certificado_recibido_delete`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- `certificados.pantalla.certificado_recibido_adjuntar`
- `certificados.pantalla.certificado_recibido_modificar`

### `/src/certificados/certificado_recibido_guardar`

Pantallas directas:
- `certificados.pantalla.certificado_recibido_adjuntar`
- `certificados.pantalla.certificado_recibido_modificar`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/certificados/certificado_recibido_modificar_data`

Pantallas directas:
- `certificados.pantalla.certificado_recibido_modificar`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/certificados/certificado_recibido_pdf_download`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/certificados/certificado_recibido_pdf_upload`

Pantallas directas:
- `certificados.pantalla.certificado_recibido_adjuntar`
- `certificados.pantalla.certificado_recibido_modificar`
- `certificados.pantalla.certificado_recibido_pdf_upload`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/certificados/certificados_locales_data`

Pantallas directas:
- `certificados.pantalla.certificado_emitido_adjuntar`
- `certificados.pantalla.certificado_recibido_adjuntar`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/certificados/textos_certificados`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

## Alertas De Revision

Endpoints sin pantalla directa detectada:
- `/src/certificados/certificado_emitido_enviar`
- `/src/certificados/certificado_emitido_guardar`
- `/src/certificados/certificado_emitido_pdf_download`
- `/src/certificados/certificado_recibido_delete`
- `/src/certificados/certificado_recibido_pdf_download`
- `/src/certificados/textos_certificados`

Endpoints sin pantalla directa ni capacidad relacionada:
- Ninguno — los endpoints listados arriba se consumen via AJAX, forms `.phtml`/`.twig` o login; ver `docs/REPASSO_FINAL.md` § B.

## Revision Manual

- Repaso 2026-05-21: huerfanos aceptados como patron normal Orbix (PostRequest / fetch desde vista).

## Revision Manual

- Confirmar si los endpoints sin pantalla directa se usan desde fragmentos AJAX no enlazados.
- Revisar pantallas que dependen de varias capacidades.
- Completar relaciones si hay navegacion generada dinamicamente o desde menus.
