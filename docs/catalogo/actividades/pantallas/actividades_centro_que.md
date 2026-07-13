---
id: "actividades.pantalla.actividades_centro_que"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "actividades"
nombre: "Seleccionar centro y periodo (listados por ctr)"
controller: "frontend/actividades/controller/actividades_centro_que.php"
vistas: ["frontend/actividades/view/actividades_centro_que.phtml"]
fragmentos_frontend: ["frontend/actividades/controller/calendario_listas.php", "frontend/actividades/controller/lista_centros_activ.php"]
endpoints: []
capacidades: []
campos: ["form.empiezamax", "form.empiezamin", "form.iactividad_val", "form.iasistentes_val", "form.id_ctr", "form.id_ctr_mas", "form.id_ctr_num", "form.periodo", "form.year", "post.empiezamax", "post.empiezamin", "post.periodo", "post.tipo_ctr", "post.tipo_lista", "post.ver_ctr", "post.year"]
acciones: ["fnjs_cerrar", "fnjs_guardar", "fnjs_mas_centros", "fnjs_modificar", "fnjs_update_div", "fnjs_ver"]
estado_revision: "revisado"
---

# Seleccionar centro y periodo (listados por ctr)

Formulario para **elegir centro(s) y periodo** y lanzar distintos listados según
`tipo_lista`: actividades del centro (`crt`/`cv` → `lista_centros_activ`), datos
económicos (`datosEc`), centros encargados (`ctrsEncargados` → `calendario_listas`),
etc. Usa `CentrosQue` + desplegables múltiples de centros y `PeriodoQue`.

Roles `ctr` ven solo sus centros (`id_ubi_in` de la sesión). Con `tipo_ctr=sg` filtra
centros SV/SF según la sección del usuario.

## Tipo

- Subtipo: `pantalla_principal` (`ViewNewPhtml`, `#main`, nav atrás)
- Controller: `frontend/actividades/controller/actividades_centro_que.php`
- Vista: `frontend/actividades/view/actividades_centro_que.phtml`

## Acciones (revisadas)

| Acción | Función JS | Destino |
|--------|-----------|---------|
| Buscar | `fnjs_ver()` | URL según `tipo_lista` (AJAX HTML en `#exportar`) |
| Añadir centro | `fnjs_mas_centros` | Añade otro desplegable de centros |
| Modificar actividad | `fnjs_modificar(id_activ)` | Popup `centro_ajax.php?que=form_ingreso` |
| Nueva actividad | `fnjs_modificar()` sin id | Popup `centro_ajax.php?que=nuevo&id_ubi=…` |
| Cerrar popup | `fnjs_cerrar` | Oculta `#div_modificar` |

## Manual De Usuario

Ver [`manual/actividades.md`](../../../manual/actividades.md). El usuario selecciona
centro(s), periodo y pulsa buscar; el listado aparece debajo sin recargar la página
completa.

## Ruta de menú

Entradas según `tipo_ctr` + `tipo_lista` (crt/cv):

- **Legacy:** vsg > crt > de cada ctr; vsg > cv > de cada ctr.
- **Pills2:** sin entrada dedicada en el índice (mismas rutas vsg).
