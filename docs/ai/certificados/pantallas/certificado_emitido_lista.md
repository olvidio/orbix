---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "certificados"
titulo: "Certificado Emitido Lista"
pantalla: "certificados.pantalla.certificado_emitido_lista"
preguntas: ["Que se puede hacer en Certificado Emitido Lista?", "Que campos tiene Certificado Emitido Lista?", "Que acciones hay en Certificado Emitido Lista?"]
capacidades: ["certificados.certificado_emitido_lista.gestionar"]
endpoints: ["/src/certificados/certificado_emitido_lista_datos"]
source: "docs/catalogo/certificados/pantallas/certificado_emitido_lista.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Certificado Emitido Lista

## Resumen

Listado principal de certificados emitidos no enviados (región STGR). Filtro por número o rango de curso académico. Acciones: nuevo, modificar, enviar, subir PDF firmado, descargar y eliminar.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

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

## Acciones Detectadas

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

## Capacidades Relacionadas

- `certificados.certificado_emitido_lista.gestionar`

## Endpoints Relacionados

- `/src/certificados/certificado_emitido_lista_datos`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
