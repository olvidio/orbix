---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "usuarios"
titulo: "Preferencias"
pantalla: "usuarios.pantalla.preferencias"
preguntas: ["Que se puede hacer en Preferencias?", "Que campos tiene Preferencias?", "Que acciones hay en Preferencias?"]
capacidades: ["usuarios.usuario_preferencias.gestionar"]
endpoints: ["/src/shared/locales_posibles", "/src/usuarios/usuario_preferencias"]
source: "docs/catalogo/usuarios/pantallas/preferencias.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Preferencias

## Resumen

Preferencias personales: layout, inicio, idioma, tablas, estilo.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.estilo_color`
- `form.idioma_nou`
- `form.inicio`
- `form.layout`
- `form.oficina`
- `form.ordenApellidos`
- `form.tipo_menu`
- `form.tipo_tabla`
- `form.zona_horaria_nou`

## Acciones Detectadas

- `button:guardar preferencias`
- `fnjs_guardar_preferencias`
- `fnjs_left_side_hide`
- `fnjs_update_div`

## Capacidades Relacionadas

- `usuarios.usuario_preferencias.gestionar`

## Endpoints Relacionados

- `/src/shared/locales_posibles`
- `/src/usuarios/usuario_preferencias`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
