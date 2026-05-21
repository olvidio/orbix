---
tipo: "relacion_pantallas_api"
modulo: "notas"
pantallas: 26
endpoints_api: 33
capacidades: 31
estado_revision: "generado"
---

# Relacion Pantallas API - notas

Indice generado automaticamente para cruzar pantallas frontend, capacidades y endpoints API.

## Por Pantalla

### `notas.pantalla.acta_2_mpdf`

- Controller: `frontend/notas/controller/acta_2_mpdf.php`
- Subtipo: `pantalla`

Endpoints directos:
- Ninguno detectado.

Capacidades:
- Ninguna detectada.

Endpoints aportados por capacidades:
- Ninguno adicional.

### `notas.pantalla.acta_imprimir`

- Controller: `frontend/notas/controller/acta_imprimir.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/notas/acta_imprimir_presentacion_data`

Capacidades:
- `notas.acta_imprimir_presentacion.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `notas.pantalla.acta_imprimir_mpdf`

- Controller: `frontend/notas/controller/acta_imprimir_mpdf.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/notas/acta_imprimir_presentacion_data`

Capacidades:
- `notas.acta_imprimir_presentacion.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `notas.pantalla.acta_listado_anual`

- Controller: `frontend/notas/controller/acta_listado_anual.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/notas/acta_listado_anual_data`

Capacidades:
- `notas.acta_listado_anual.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `notas.pantalla.acta_pdf_delete`

- Controller: `frontend/notas/controller/acta_pdf_delete.php`
- Subtipo: `pantalla`

Endpoints directos:
- `/src/notas/infrastructure/ui/http/controllers/acta_pdf_eliminar`

Capacidades:
- Ninguna detectada.

Endpoints aportados por capacidades:
- Ninguno adicional.

### `notas.pantalla.acta_pdf_download`

- Controller: `frontend/notas/controller/acta_pdf_download.php`
- Subtipo: `pantalla`

Endpoints directos:
- `/src/notas/acta_pdf_download`
- `/src/notas/infrastructure/ui/http/controllers/acta_pdf_download`

Capacidades:
- `notas.acta_pdf_download.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `notas.pantalla.acta_pdf_upload`

- Controller: `frontend/notas/controller/acta_pdf_upload.php`
- Subtipo: `pantalla`

Endpoints directos:
- `/src/notas/infrastructure/ui/http/controllers/acta_pdf_subir`

Capacidades:
- Ninguna detectada.

Endpoints aportados por capacidades:
- Ninguno adicional.

### `notas.pantalla.acta_select`

- Controller: `frontend/notas/controller/acta_select.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/notas/acta_eliminar`
- `/src/notas/acta_select_data`

Capacidades:
- `notas.acta.gestionar`
- `notas.acta_select.gestionar`

Endpoints aportados por capacidades:
- `/src/notas/acta_nueva`

### `notas.pantalla.acta_ver`

- Controller: `frontend/notas/controller/acta_ver.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/notas/acta_modificar`
- `/src/notas/acta_nueva`
- `/src/notas/acta_ver_form_data`
- `/src/notas/asignaturas_search`
- `/src/notas/examinadores_search`

Capacidades:
- `notas.acta.gestionar`
- `notas.acta_modificar.gestionar`
- `notas.acta_ver.gestionar`
- `notas.asignaturas_search.gestionar`
- `notas.examinadores_search.gestionar`

Endpoints aportados por capacidades:
- `/src/notas/acta_eliminar`

### `notas.pantalla.actividad_buscar_form`

- Controller: `frontend/notas/controller/actividad_buscar_form.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/notas/actividades_buscar_data`

Capacidades:
- `notas.actividades_buscar.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `notas.pantalla.asig_faltan_personas_select`

- Controller: `frontend/notas/controller/asig_faltan_personas_select.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/notas/asig_faltan_personas_select_data`

Capacidades:
- `notas.asig_faltan_personas_select.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `notas.pantalla.asig_faltan_que`

- Controller: `frontend/notas/controller/asig_faltan_que.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/asignaturas/asignaturas_con_separador_data`

Capacidades:
- Ninguna detectada.

Endpoints aportados por capacidades:
- Ninguno adicional.

### `notas.pantalla.asig_faltan_select`

- Controller: `frontend/notas/controller/asig_faltan_select.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/notas/asig_faltan_select_data`

Capacidades:
- `notas.asig_faltan_select.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `notas.pantalla.asignaturas_pendientes`

- Controller: `frontend/notas/controller/asignaturas_pendientes.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/notas/asignaturas_pendientes_data`

Capacidades:
- `notas.asignaturas_pendientes.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `notas.pantalla.asignaturas_pendientes_resumen`

- Controller: `frontend/notas/controller/asignaturas_pendientes_resumen.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/notas/asignaturas_pendientes_resumen_data`

Capacidades:
- `notas.asignaturas_pendientes_resumen.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `notas.pantalla.comprobar_notas`

- Controller: `frontend/notas/controller/comprobar_notas.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/notas/comprobar_notas_page_data`

Capacidades:
- `notas.comprobar_notas_page.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `notas.pantalla.form_notas_de_una_persona`

- Controller: `frontend/notas/controller/form_notas_de_una_persona.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/notas/buscar_acta`
- `/src/notas/nota_persona_form_data`
- `/src/notas/persona_nota_editar`
- `/src/notas/persona_nota_nueva`
- `/src/notas/posibles_opcionales_data`
- `/src/notas/posibles_preceptores_data`

Capacidades:
- `notas.buscar_acta.gestionar`
- `notas.nota_persona.gestionar`
- `notas.persona_nota.gestionar`
- `notas.persona_nota_editar.gestionar`
- `notas.posibles_opcionales.gestionar`
- `notas.posibles_preceptores.gestionar`

Endpoints aportados por capacidades:
- `/src/notas/persona_nota_eliminar`

### `notas.pantalla.informe_stgr_agd`

- Controller: `frontend/notas/controller/informe_stgr_agd.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/notas/informe_stgr_agd_data`

Capacidades:
- `notas.informe_stgr_agd.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `notas.pantalla.informe_stgr_n`

- Controller: `frontend/notas/controller/informe_stgr_n.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/notas/informe_stgr_n_data`

Capacidades:
- `notas.informe_stgr_n.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `notas.pantalla.informe_stgr_profesores`

- Controller: `frontend/notas/controller/informe_stgr_profesores.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/notas/informe_stgr_profesores_data`

Capacidades:
- `notas.informe_stgr_profesores.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `notas.pantalla.resumen_anual`

- Controller: `frontend/notas/controller/resumen_anual.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/ubis/delegaciones_region_stgr_data`

Capacidades:
- Ninguna detectada.

Endpoints aportados por capacidades:
- Ninguno adicional.

### `notas.pantalla.tessera_2_mpdf`

- Controller: `frontend/notas/controller/tessera_2_mpdf.php`
- Subtipo: `pantalla`

Endpoints directos:
- Ninguno detectado.

Capacidades:
- Ninguna detectada.

Endpoints aportados por capacidades:
- Ninguno adicional.

### `notas.pantalla.tessera_copiar_select`

- Controller: `frontend/notas/controller/tessera_copiar_select.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/notas/tessera_copiar`
- `/src/notas/tessera_copiar_select_data`

Capacidades:
- `notas.tessera.gestionar`
- `notas.tessera_copiar_select.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `notas.pantalla.tessera_imprimir`

- Controller: `frontend/notas/controller/tessera_imprimir.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/notas/tessera_imprimir_data`

Capacidades:
- `notas.tessera_imprimir.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `notas.pantalla.tessera_imprimir_mpdf`

- Controller: `frontend/notas/controller/tessera_imprimir_mpdf.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/notas/tessera_imprimir_data`

Capacidades:
- `notas.tessera_imprimir.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `notas.pantalla.tessera_ver`

- Controller: `frontend/notas/controller/tessera_ver.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/notas/tessera_ver_data`

Capacidades:
- `notas.tessera_ver.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

## Por Endpoint API

### `/src/notas/acta_eliminar`

Pantallas directas:
- `notas.pantalla.acta_select`

Pantallas via capacidad:
- `notas.pantalla.acta_ver`

### `/src/notas/acta_imprimir_presentacion_data`

Pantallas directas:
- `notas.pantalla.acta_imprimir`
- `notas.pantalla.acta_imprimir_mpdf`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/notas/acta_listado_anual_data`

Pantallas directas:
- `notas.pantalla.acta_listado_anual`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/notas/acta_modificar`

Pantallas directas:
- `notas.pantalla.acta_ver`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/notas/acta_nueva`

Pantallas directas:
- `notas.pantalla.acta_ver`

Pantallas via capacidad:
- `notas.pantalla.acta_select`

### `/src/notas/acta_pdf_download`

Pantallas directas:
- `notas.pantalla.acta_pdf_download`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/notas/acta_pdf_eliminar`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/notas/acta_pdf_subir`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/notas/acta_select_data`

Pantallas directas:
- `notas.pantalla.acta_select`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/notas/acta_ver_form_data`

Pantallas directas:
- `notas.pantalla.acta_ver`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/notas/actividades_buscar_data`

Pantallas directas:
- `notas.pantalla.actividad_buscar_form`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/notas/asig_faltan_personas_select_data`

Pantallas directas:
- `notas.pantalla.asig_faltan_personas_select`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/notas/asig_faltan_select_data`

Pantallas directas:
- `notas.pantalla.asig_faltan_select`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/notas/asignaturas_pendientes_data`

Pantallas directas:
- `notas.pantalla.asignaturas_pendientes`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/notas/asignaturas_pendientes_resumen_data`

Pantallas directas:
- `notas.pantalla.asignaturas_pendientes_resumen`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/notas/asignaturas_search`

Pantallas directas:
- `notas.pantalla.acta_ver`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/notas/buscar_acta`

Pantallas directas:
- `notas.pantalla.form_notas_de_una_persona`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/notas/comprobar_notas_constants_data`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- Ninguna adicional.

### `/src/notas/comprobar_notas_page_data`

Pantallas directas:
- `notas.pantalla.comprobar_notas`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/notas/examinadores_search`

Pantallas directas:
- `notas.pantalla.acta_ver`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/notas/informe_stgr_agd_data`

Pantallas directas:
- `notas.pantalla.informe_stgr_agd`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/notas/informe_stgr_n_data`

Pantallas directas:
- `notas.pantalla.informe_stgr_n`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/notas/informe_stgr_profesores_data`

Pantallas directas:
- `notas.pantalla.informe_stgr_profesores`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/notas/nota_persona_form_data`

Pantallas directas:
- `notas.pantalla.form_notas_de_una_persona`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/notas/persona_nota_editar`

Pantallas directas:
- `notas.pantalla.form_notas_de_una_persona`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/notas/persona_nota_eliminar`

Pantallas directas:
- Ninguna detectada.

Pantallas via capacidad:
- `notas.pantalla.form_notas_de_una_persona`

### `/src/notas/persona_nota_nueva`

Pantallas directas:
- `notas.pantalla.form_notas_de_una_persona`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/notas/posibles_opcionales_data`

Pantallas directas:
- `notas.pantalla.form_notas_de_una_persona`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/notas/posibles_preceptores_data`

Pantallas directas:
- `notas.pantalla.form_notas_de_una_persona`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/notas/tessera_copiar`

Pantallas directas:
- `notas.pantalla.tessera_copiar_select`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/notas/tessera_copiar_select_data`

Pantallas directas:
- `notas.pantalla.tessera_copiar_select`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/notas/tessera_imprimir_data`

Pantallas directas:
- `notas.pantalla.tessera_imprimir`
- `notas.pantalla.tessera_imprimir_mpdf`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/notas/tessera_ver_data`

Pantallas directas:
- `notas.pantalla.tessera_ver`

Pantallas via capacidad:
- Ninguna adicional.

## Alertas De Revision

Endpoints sin pantalla directa detectada:
- `/src/notas/acta_pdf_eliminar`
- `/src/notas/acta_pdf_subir`
- `/src/notas/comprobar_notas_constants_data`
- `/src/notas/persona_nota_eliminar`

Endpoints sin pantalla directa ni capacidad relacionada:
- Ninguno — los endpoints listados arriba se consumen via AJAX, forms `.phtml`/`.twig` o login; ver `docs/REPASSO_FINAL.md` § B.

## Revision Manual

- Repaso 2026-05-21: huerfanos aceptados como patron normal Orbix (PostRequest / fetch desde vista).

## Revision Manual

- Confirmar si los endpoints sin pantalla directa se usan desde fragmentos AJAX no enlazados.
- Revisar pantallas que dependen de varias capacidades.
- Completar relaciones si hay navegacion generada dinamicamente o desde menus.
