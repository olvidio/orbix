---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Ver las propuestas por encargos"
flujo: "encargossacd.propuestas_lista_enc.gestionar.flujo"
preguntas: ["Como ver las propuestas por encargos en Ver las propuestas por encargos?"]
pantallas_principales: ["encargossacd.pantalla.propuestas_lista_enc"]
fragmentos: ["encargossacd.pantalla.propuestas_lista_enc"]
endpoints: ["/src/encargossacd/propuestas_lista_enc_data"]
source: "docs/catalogo/encargossacd/flujos/propuestas_lista_enc.md"
estado_revision: "generado"
---

# Ayuda IA - Ver las propuestas por encargos

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver las propuestas por encargos`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver las propuestas por encargos en Ver las propuestas por encargos?

## Donde Entrar

- Propuestas Lista Enc (`encargossacd.pantalla.propuestas_lista_enc`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver las propuestas por encargos

1. Abrir **listado propuestas por encargos**.
2. Si se quiere, filtrar por centro.
3. Se muestra el listado. Si todavía no se ha creado la tabla de propuestas, avisa «Debe crear la tabla de propuestas».

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/propuestas_lista_enc_data`

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.propuestas_lista_enc`
- `encargossacd.pantalla.propuestas_lista_enc`

## Objetivo

Ver cómo van las propuestas, por encargos, después (o durante) la edición. (Redactado desde el código; no contrastado con la pantalla.)

## Errores Documentados

- `Debe crear la tabla de propuestas`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
