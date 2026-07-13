---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "misas"
titulo: "Ver Iniciales Zona"
pantalla: "misas.pantalla.ver_iniciales_zona"
preguntas: ["Que se puede hacer en Ver Iniciales Zona?", "Que campos tiene Ver Iniciales Zona?", "Que acciones hay en Ver Iniciales Zona?"]
capacidades: ["misas.update_iniciales.gestionar", "misas.ver_iniciales_zona.gestionar"]
endpoints: ["/src/misas/update_iniciales", "/src/misas/ver_iniciales_zona_data"]
source: "docs/catalogo/misas/pantallas/ver_iniciales_zona.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Ver Iniciales Zona

## Resumen

Fragmento SlickGrid con sacds de la zona; edición inline que postea a `update_iniciales`.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.color`
- `form.id_sacd`
- `form.iniciales`
- `post.id_zona`

## Acciones Detectadas

- `fnjs_generarNomEnc`

## Capacidades Relacionadas

- `misas.update_iniciales.gestionar`
- `misas.ver_iniciales_zona.gestionar`

## Endpoints Relacionados

- `/src/misas/update_iniciales`
- `/src/misas/ver_iniciales_zona_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
