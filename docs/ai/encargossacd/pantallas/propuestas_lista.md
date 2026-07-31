---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "encargossacd"
titulo: "Propuestas Lista"
pantalla: "encargossacd.pantalla.propuestas_lista"
preguntas: ["Que se puede hacer en Propuestas Lista?", "Que campos tiene Propuestas Lista?", "Que acciones hay en Propuestas Lista?"]
capacidades: []
endpoints: ["/src/encargossacd/opciones_seccion_data", "/src/encargossacd/propuestas_ajax"]
source: "docs/catalogo/encargossacd/pantallas/propuestas_lista.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Propuestas Lista

## Resumen

Pantalla editable de propuestas: filtro por grupo de centros y tabla HTML de encargos con titular/suplente/colaboradores, popups de selección SACD, info y dedicación.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.filtro_ctr`
- `form.dedic_m`
- `form.dedic_t`
- `form.dedic_v`
- `html.lista`

## Acciones Detectadas

- `fnjs_lista_propuestas`
- `fnjs_ver_sacd_posibles`
- `fnjs_cmb_sacd`
- `fnjs_info`
- `fnjs_dedicacion`
- `fnjs_guardar_horario`
- `fnjs_cerrar_propuesta_popup`

## Capacidades Relacionadas

- No hay capacidades relacionadas.

## Endpoints Relacionados

- `/src/encargossacd/opciones_seccion_data`
- `/src/encargossacd/propuestas_ajax`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
