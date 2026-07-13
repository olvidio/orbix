---
id: "profesores.ficha_profesor_stgr"
tipo: "endpoint"
modulo: "profesores"
url: "/src/profesores/ficha_profesor_stgr"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/profesores/infrastructure/ui/http/controllers/ficha_profesor_stgr.php"
entrada: ["post.depende:string", "post.id_nom:integer", "post.id_tabla:string", "post.obj_pau:string", "post.permiso:string", "post.print:integer"]
entrada_obligatoria: ["post.id_nom"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "profesores_FichaProfesorStgrData"
respuesta_data: ["aPerm:array", "nom_ap:string", "nombre_ubi:string", "dep:string", "num_txt:string", "agd_txt:string", "sacd_txt:string", "latin_txt:string", "f_juramento:string", "a_curriculum:array", "a_nombramientos:array", "a_director:array", "a_ampliacion:array", "a_publicaciones:array", "a_congresos:array", "a_docencias:array", "go_cosas_link_specs:array", "ficha_self_link_spec:array", "use_print_phtml:boolean"]
requiere_hashb: false
frontend_referencias: ["frontend/profesores/controller/ficha_profesor_stgr.php"]
casos_uso: ["src\\profesores\\application\\FichaProfesorStgr"]
tags: ["profesores", "ficha", "profesor", "stgr"]
estado_revision: "revisado"
---

# Ficha Profesor Stgr

Dossier académico STGR de un profesor: datos personales, nombramientos, curriculum, ampliaciones,
congresos, docencia, director de departamento, juramento y publicaciones. En modo impresión (`print`)
omite director, juramento y publicaciones y filtra nombramientos sin cese.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/profesores/ficha_profesor_stgr`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/profesores/infrastructure/ui/http/controllers/ficha_profesor_stgr.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_nom` | `integer` | controller | Sí | También llega vía `sel` (`id_nom#id_tabla`) o alias `id_pau` en el frontend |
| `id_tabla` | `string` | controller | No | `n` → numérico, `a` → agd; afecta a `num_txt`/`agd_txt` |
| `print` | `integer` | controller | No | `1` activa vista imprimible; en ámbito `rstgr` el controller fuerza impresión |
| `obj_pau` | `string` | controller | No | Clase PAU para enlaces `tablaDB_lista_ver` |
| `permiso` | `string` | controller | No | Nivel de permiso dossier propagado desde personas |
| `depende` | `string` | controller | No | Dependencia dossier propagada desde personas |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse` salvo excepciones).
- Éxito: `success: true`, `data` con el payload de ficha.
- Claves principales: `aPerm` (lectura/escritura por sección: curriculum, nombramientos, ampliacion,
  congresos, docencia, director, juramento, publicaciones, latin), datos de cabecera (`nom_ap`,
  `nombre_ubi`, `dep`, flags `num_txt`/`agd_txt`/`sacd_txt`/`latin_txt`), arrays de bloques
  (`a_curriculum`, `a_nombramientos`, `a_director`, `a_ampliacion`, `a_publicaciones`, `a_congresos`,
  `a_docencias`), `go_cosas_link_specs` y `ficha_self_link_spec` (URLs firmadas en frontend),
  `use_print_phtml`.
- Error en `mensaje` si `id_nom` no existe (el payload incluye `error` que el controller extrae).

## Objetivo funcional

Construye el dossier completo del profesor STGR para visualización o impresión. Calcula permisos
por tipo de dossier (`PermDossier`) y enlaces a submantenimientos vía `tablaDB_lista_ver`. Rama
impresión: solo nombramientos activos y bloques públicos.

## Errores conocidos

- `No encuentro a nadie con id_nom: %s` — persona inexistente.
- `No se ha encontrado la asignatura con id: %s` — `RuntimeException` si falta asignatura en
  ampliación o docencia (error no capturado en envelope).

## Permisos

- Frontend: si `$_SESSION['oPerm']->have_perm_oficina('est')`, fuerza `permiso=3`.
- Lectura/escritura por sección: `PermDossier` según tipo dossier (1012–1025).
- Entrada habitual desde `personas_select` (botón **ficha profesor stgr**); sin menú directo.

## Casos De Uso

- `src\profesores\application\FichaProfesorStgr`

## Frontend Relacionado

- `frontend/profesores/controller/ficha_profesor_stgr.php` — invocado desde
  `frontend/personas/controller/personas_select.php` (`fnjs_ficha_profe`).
