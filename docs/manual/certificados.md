---
tipo: manual_usuario
modulo: certificados
flujos: 18
estado_revision: revisado_parcial
---

# Manual De Usuario - certificados

Emision y recepcion de **certificados** (PDF, firma, envio).

## Acceso Por Menu (rol 12 STGR)

| Texto en menu | Controller |
|---------------|------------|
| **Certificados** | `certificado_emitido_lista.php` |

Otras pantallas: emitido ver/modificar, recibido, textos, locales — desde listado o enlaces internos.

## Listado Certificados Emitidos

1. Abrir **Certificados**.
2. Filtrar/buscar segun pantalla.
3. Acciones por fila: **ver**, **imprimir**, **PDF**, **enviar**, **adjuntar firmado**, **upload**.

## Emitir O Modificar Certificado

1. Crear o abrir certificado emitido.
2. Completar datos y textos (`textos_certificados` si aplica).
3. **Guardar PDF** / **imprimir** (mpdf) / **enviar** por correo.

## Certificados Recibidos

- Registrar certificado recibido, adjuntar PDF, modificar metadatos.

## Modulos Relacionados

- **personas** — titular certificado
- **notas** — contexto STGR

Legacy: `documentacion/Documentacion_Obix/certificados/mapa_*.md`
