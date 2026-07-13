---
id: "cambios.pantalla.avisos_generar"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "cambios"
nombre: "Lista de cambios"
controller: "frontend/cambios/controller/avisos_generar.php"
vistas: ["frontend/cambios/view/avisos_generar.phtml", "frontend/cambios/view/avisos_generar_lista.phtml"]
fragmentos_frontend: []
endpoints: ["/src/cambios/avisos_generar_lista_data", "/src/cambios/cambio_usuario_eliminar", "/src/cambios/cambio_usuario_eliminar_hasta_fecha"]
capacidades: ["cambios.avisos_generar.gestionar"]
campos: ["form.aviso_tipo", "form.id_usuario", "html.f_fin", "html.refresh", "post.Gstack", "post.aviso_tipo", "post.id_usuario", "post.refresh"]
acciones: ["fnjs_actualizar", "fnjs_borrar", "fnjs_borrar_hasta_fecha", "fnjs_enviar_formulario", "fnjs_selectAll"]
estado_revision: "revisado"
---

# Lista de cambios

Pantalla de consulta y mantenimiento de cambios anotados pendientes de avisar (`CambioUsuario` con
`avisado=false`). Los administradores pueden filtrar por usuario y tipo de aviso; el resto ve solo sus
propios cambios.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/cambios/controller/avisos_generar.php`

## Vistas Relacionadas

- `frontend/cambios/view/avisos_generar.phtml` (shell con filtros)
- `frontend/cambios/view/avisos_generar_lista.phtml` (tabla, también servible con `solo_lista=1`)

## Endpoints Usados

- `/src/cambios/avisos_generar_lista_data` (carga y refresco)
- `/src/cambios/cambio_usuario_eliminar` (`fnjs_borrar`)
- `/src/cambios/cambio_usuario_eliminar_hasta_fecha` (`fnjs_borrar_hasta_fecha`)

## Capacidades Relacionadas

- `cambios.avisos_generar.gestionar`

## Campos Detectados

- `form.aviso_tipo`, `form.id_usuario` (solo admin)
- `html.f_fin` (fecha límite de purga)
- `post.refresh`, `post.solo_lista`, `sel[]`

## Acciones Detectadas

- `fnjs_actualizar` — refresca listado tras cambiar filtros
- `fnjs_borrar` — elimina filas seleccionadas
- `fnjs_borrar_hasta_fecha` — purga hasta `f_fin`
- `fnjs_selectAll` — selección masiva

## Manual De Usuario

1. Abrir la pantalla desde el menú (varias rutas según perfil).
2. Si es admin, elegir usuario y tipo de aviso y pulsar actualizar.
3. Revisar la tabla (fecha, autor del cambio, descripción).
4. Opcional: marcar filas y borrar, o indicar fecha límite y purgar en bloque.

## Ruta de menú

- **Legacy:** Calendario > actividades > ver lista cambios; sistema > usuarios web > ver lista cambios;
  Utilidades > Utilidades > lista de cambios
- **Pills2:** mismas rutas; en usuarios web también ADMIN LOCAL > usuarios web > ver lista cambios
