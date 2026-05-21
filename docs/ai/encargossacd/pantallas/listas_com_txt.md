---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "encargossacd"
titulo: "Listas Com Txt"
pantalla: "encargossacd.pantalla.listas_com_txt"
preguntas: ["Que se puede hacer en Listas Com Txt?", "Que campos tiene Listas Com Txt?", "Que acciones hay en Listas Com Txt?"]
capacidades: ["encargossacd.listas_com_txt.gestionar"]
endpoints: ["/src/encargossacd/listas_com_txt_data", "/src/encargossacd/listas_com_txt_get", "/src/encargossacd/listas_com_txt_update"]
source: "docs/catalogo/encargossacd/pantallas/listas_com_txt.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Listas Com Txt

## Resumen

Pantalla para editar los textos de comunicacion de los encargos a los SACD.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.clave`
- `form.comunicacion`
- `form.idioma`
- `html.comunicacion`

## Acciones Detectadas

- `fnjs_get_texto`
- `fnjs_guardar`

## Capacidades Relacionadas

- `encargossacd.listas_com_txt.gestionar`

## Endpoints Relacionados

- `/src/encargossacd/listas_com_txt_data`
- `/src/encargossacd/listas_com_txt_get`
- `/src/encargossacd/listas_com_txt_update`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
