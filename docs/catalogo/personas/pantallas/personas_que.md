---
id: "personas.pantalla.personas_que"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "personas"
nombre: "Buscar personas"
controller: "frontend/personas/controller/personas_que.php"
vistas: ["frontend/personas/view/personas_que.phtml"]
fragmentos_frontend: []
endpoints: []
capacidades: []
campos: ["form.apellido1", "form.apellido2", "form.centro", "form.cmb", "form.exacto", "form.nombre"]
acciones: ["fnjs_enviar_formulario"]
estado_revision: "revisado"
---

# Buscar personas

Formulario de criterios de búsqueda. Al enviar, navega a `personas_select.php` con los filtros
y parámetros de contexto (`tabla`, `na`, `tipo`, `es_sacd`) heredados del menú.

## Tipo

- Subtipo: `pantalla_principal`
- Controller: `frontend/personas/controller/personas_que.php`

## Vistas Relacionadas

- `frontend/personas/view/personas_que.phtml`

## Campos

- Nombre, apellido1, apellido2, centro
- `exacto` (radio): coincidencia exacta vs prefijo sin acentos
- `cmb`: incluir situación distinta de activa (requiere permiso `dtor` para ver bajas)

Hidden: `tabla`, `na`, `tipo`, `que`, `es_sacd` (definidos por la entrada de menú).

## Acciones

- Enviar búsqueda → `personas_select.php`

## Manual De Usuario

Pantalla revisada contra `frontend/personas/`. La rama `que=telf` del legacy fue eliminada
(enlace muerto).

## Ruta de menú

Variantes según parámetros `tabla`/`na`/`es_sacd` en `_referencia_menus.md`. Ejemplos vsm/PERSONAS:

- **Legacy:** `vsm > buscar n > n de paso` · `vsm > buscar n > n r/dl`
- **Pills2:** `PERSONAS > Numerarios > Buscar n de paso` · `PERSONAS > Numerarios > Buscar n de la r/dl`

Otras entradas: agd/s/sssc/nax por colectivo (`vest`, `vsg`, `dagd`, `stgr`, `dre`, `vnax`, etc.).
