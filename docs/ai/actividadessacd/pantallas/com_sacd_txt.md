---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividadessacd"
titulo: "Com Sacd Txt"
pantalla: "actividadessacd.pantalla.com_sacd_txt"
preguntas: ["Que se puede hacer en Com Sacd Txt?", "Que campos tiene Com Sacd Txt?", "Que acciones hay en Com Sacd Txt?"]
capacidades: ["actividadessacd.locales_desplegable.gestionar", "actividadessacd.texto_comunicacion.gestionar"]
endpoints: ["/src/actividadessacd/locales_desplegable_data", "/src/actividadessacd/texto_comunicacion_data", "/src/actividadessacd/texto_comunicacion_guardar"]
source: "docs/catalogo/actividadessacd/pantallas/com_sacd_txt.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Com Sacd Txt

## Resumen

Pantalla de edicion de textos de comunicacion a los sacd.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `html.comunicacion`

## Acciones Detectadas

- `fnjs_cancelar`
- `fnjs_get_texto`
- `fnjs_guardar`
- `fnjs_parse_rta_txt`

## Capacidades Relacionadas

- `actividadessacd.locales_desplegable.gestionar`
- `actividadessacd.texto_comunicacion.gestionar`

## Endpoints Relacionados

- `/src/actividadessacd/locales_desplegable_data`
- `/src/actividadessacd/texto_comunicacion_data`
- `/src/actividadessacd/texto_comunicacion_guardar`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
