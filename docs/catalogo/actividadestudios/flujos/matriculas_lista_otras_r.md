---
id: "actividadestudios.matriculas_lista_otras_r.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Gestionar Matriculas Lista Otras R"
capacidad: "actividadestudios.matriculas_lista_otras_r.gestionar"
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.matriculas_lista_otras_r"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadestudios/matriculas_lista_otras_r_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Matriculas Lista Otras R

Listado de alumnos de otras regiones STGR para certificados.

## Objetivo De Usuario

El usuario busca alumnos de otras regiones por apellido para consultar sus asignaturas
matriculadas y emitir certificados E43. Solo visible en ámbito región STGR (`rstgr` o `r`).

## Punto De Entrada

Pantalla `matriculas_lista_otras_r`
(`frontend/actividadestudios/controller/matriculas_lista_otras_r.php`): al cargar o buscar
por apellidos llama a `matriculas_lista_otras_r_data`.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.matriculas_lista_otras_r`

## Escenarios Inferidos

### Obtener Datos

Pasos:
1. Abrir **Envío información a otras r** (solo regiones STGR).
2. Opcionalmente filtrar por apellido y pulsar **buscar**.
3. El sistema consulta `matriculas_lista_otras_r_data` y muestra alumnos con alertas y
   asignaturas.
4. Seleccionar un alumno para **imprimir certificado** (`fnjs_imp_certificado`).

Endpoints asociados:
- `/src/actividadestudios/matriculas_lista_otras_r_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.apellido1`
- `html.apellido1`
- `html.btn`
- `html.mod`
- `html.pau`
- `html.refresh`
- `post.apellido1`
- `post.mod`
- `post.stack`

Acciones JavaScript:
- `fnjs_buscar`
- `fnjs_buscar_por_apellidos`
- `fnjs_enviar_formulario`
- `fnjs_imp_certificado`
- `fnjs_left_side_hide`
- `fnjs_solo_uno`

## Endpoints Del Flujo

- `/src/actividadestudios/matriculas_lista_otras_r_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** — (sin entrada en el índice Legacy).
- **Pills2:** ESTUDIOS > Actas y certificados > Envío información a otras r.
