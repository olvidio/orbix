---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividades"
titulo: "Seleccionar centro y periodo (listados por ctr)"
pantalla: "actividades.pantalla.actividades_centro_que"
preguntas: ["Que se puede hacer en Seleccionar centro y periodo (listados por ctr)?", "Que campos tiene Seleccionar centro y periodo (listados por ctr)?", "Que acciones hay en Seleccionar centro y periodo (listados por ctr)?"]
capacidades: []
endpoints: []
source: "docs/catalogo/actividades/pantallas/actividades_centro_que.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Seleccionar centro y periodo (listados por ctr)

## Resumen

Formulario para **elegir centro(s) y periodo** y lanzar distintos listados según `tipo_lista`: actividades del centro (`crt`/`cv` → `lista_centros_activ`), datos económicos (`datosEc`), centros encargados (`ctrsEncargados` → `calendario_listas`), etc. Usa `CentrosQue` + desplegables múltiples de centros y `PeriodoQue`.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.empiezamax`
- `form.empiezamin`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.id_ctr`
- `form.id_ctr_mas`
- `form.id_ctr_num`
- `form.periodo`
- `form.year`
- `post.empiezamax`
- `post.empiezamin`
- `post.periodo`
- `post.tipo_ctr`
- `post.tipo_lista`
- `post.ver_ctr`
- `post.year`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar`
- `fnjs_mas_centros`
- `fnjs_modificar`
- `fnjs_update_div`
- `fnjs_ver`

## Capacidades Relacionadas

- No hay capacidades relacionadas.

## Endpoints Relacionados

- No hay endpoints detectados.

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
