---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "encargossacd"
titulo: "Propuestas Ajax"
pantalla: "encargossacd.pantalla.propuestas_ajax"
preguntas: ["Que se puede hacer en Propuestas Ajax?", "Que campos tiene Propuestas Ajax?", "Que acciones hay en Propuestas Ajax?"]
capacidades: []
endpoints: ["/src/encargossacd/propuestas_ajax"]
source: "docs/catalogo/encargossacd/pantallas/propuestas_ajax.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Propuestas Ajax

## Resumen

Proxy JSON hacia `/src/encargossacd/propuestas_ajax`. Reenvía `$_POST` y responde al JS legacy con el payload interno en la raíz (`success`, `lista`, `html`, …).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.que`
- `post.filtro_ctr`
- `post.tipo`
- `post.id_item`
- `post.id_enc`
- `post.id_sacd`
- `post.dedic_m`
- `post.dedic_t`
- `post.dedic_v`

## Acciones Detectadas

- No hay acciones detectadas.

## Capacidades Relacionadas

- No hay capacidades relacionadas.

## Endpoints Relacionados

- `/src/encargossacd/propuestas_ajax`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
