---
id: "certificados.pantalla.certificado_emitido_imprimir"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "certificados"
nombre: "Certificado Emitido Imprimir"
controller: "frontend/certificados/controller/certificado_emitido_imprimir.php"
vistas: []
fragmentos_frontend: ["frontend/certificados/controller/certificado_emitido_2_mpdf.php"]
endpoints: ["/src/certificados/certificado_emitido_delete", "/src/certificados/certificado_emitido_imprimir_datos", "/src/shared/locales_posibles"]
capacidades: ["certificados.certificado_emitido.gestionar", "certificados.certificado_emitido_imprimir.gestionar"]
campos: ["form.certificado", "form.destino", "form.f_certificado", "form.firmado", "form.guardar", "form.id_item", "form.idioma", "post.id_nom", "post.id_tabla", "post.sel", "post.stack"]
acciones: []
estado_revision: "revisado"
---

# Certificado Emitido Imprimir

Formulario previo a generar un certificado nuevo: datos de persona, idioma y configuración STGR.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/certificados/controller/certificado_emitido_imprimir.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

- `frontend/certificados/controller/certificado_emitido_2_mpdf.php`

## Endpoints Usados

- `/src/certificados/certificado_emitido_delete`
- `/src/certificados/certificado_emitido_imprimir_datos`
- `/src/shared/locales_posibles`

## Capacidades Relacionadas

- `certificados.certificado_emitido.gestionar`
- `certificados.certificado_emitido_imprimir.gestionar`

## Campos Detectados

- `form.certificado`
- `form.destino`
- `form.f_certificado`
- `form.firmado`
- `form.guardar`
- `form.id_item`
- `form.idioma`
- `post.id_nom`
- `post.id_tabla`
- `post.sel`
- `post.stack`

## Acciones Detectadas

No se han detectado acciones.

## Ruta de menú

- sin entrada de menú en el índice (desde dossier persona o matrículas)
