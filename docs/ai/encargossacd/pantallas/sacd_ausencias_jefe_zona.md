---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "encargossacd"
titulo: "Sacd Ausencias Jefe Zona"
pantalla: "encargossacd.pantalla.sacd_ausencias_jefe_zona"
preguntas: ["Que se puede hacer en Sacd Ausencias Jefe Zona?", "Que campos tiene Sacd Ausencias Jefe Zona?", "Que acciones hay en Sacd Ausencias Jefe Zona?"]
capacidades: ["encargossacd.sacd_ausencias_jefe_zona.gestionar"]
endpoints: ["/src/encargossacd/sacd_ausencias_jefe_zona_data"]
source: "docs/catalogo/encargossacd/pantallas/sacd_ausencias_jefe_zona.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Sacd Ausencias Jefe Zona

## Resumen

Muestra la ficha de ausencias para un jefe de zona / oficial.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.filtro_sacd`
- `form.historial`
- `form.id_nom`

## Acciones Detectadas

- `fnjs_horario`
- `fnjs_lista_sacd`
- `fnjs_ver_ficha`

## Capacidades Relacionadas

- `encargossacd.sacd_ausencias_jefe_zona.gestionar`

## Endpoints Relacionados

- `/src/encargossacd/sacd_ausencias_jefe_zona_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
