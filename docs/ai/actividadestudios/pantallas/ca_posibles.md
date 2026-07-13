---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividadestudios"
titulo: "Ca Posibles"
pantalla: "actividadestudios.pantalla.ca_posibles"
preguntas: ["Que se puede hacer en Ca Posibles?", "Que campos tiene Ca Posibles?", "Que acciones hay en Ca Posibles?"]
capacidades: ["actividadestudios.ca_posibles.gestionar"]
endpoints: ["/src/actividadestudios/ca_posibles_data"]
source: "docs/catalogo/actividadestudios/pantallas/ca_posibles.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Ca Posibles

## Resumen

Resultado del informe «posibles CA»: calcula y muestra, para un centro y periodo, los créditos cursables de cada alumno por actividad (cuadro imprimible o listado resumido). Se invoca desde `ca_posibles_que.php` al pulsar **ver cuadro**.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `html.observ`
- `post.ca_estudios`
- `post.ca_repaso`
- `post.ca_todos`
- `post.empiezamax`
- `post.empiezamin`
- `post.grupo_estudios`
- `post.id_ctr_agd`
- `post.id_ctr_n`
- `post.idca`
- `post.na`
- `post.obj_pau`
- `post.periodo`
- `post.ref`
- `post.sel`
- `post.stack`
- `post.texto`
- `post.year`

## Acciones Detectadas

- `fnjs_update_div`

## Capacidades Relacionadas

- `actividadestudios.ca_posibles.gestionar`

## Endpoints Relacionados

- `/src/actividadestudios/ca_posibles_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
