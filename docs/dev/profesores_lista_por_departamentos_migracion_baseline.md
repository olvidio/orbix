# Baseline migracion `lista_por_departamentos`

## Pantalla

- URL legacy actual: `apps/profesores/controller/lista_por_departamentos.php`.
- Objetivo canonico: `frontend/profesores/controller/lista_por_departamentos.php`.

## Parametros de entrada

- `POST dl[]`: delegaciones seleccionadas (filtro en ambito `rstgr`).
- `POST filtro`: cuando vale `1`, aplica filtro y muestra listado.

## Reglas funcionales

- En ambito `rstgr` y sin `filtro=1`, se muestra primero selector de delegaciones (`dl_rstgr_que.html.twig`).
- Si no aplica el paso anterior, muestra listado de profesores por departamento.
- Solo se listan personas con `situacion = 'A'`.
- Agrupacion por departamento y por tipo (`director`, `ayudante`, `encargado`, etc.).

## Salida

- Vista filtro (`twig`) o vista final HTML (`lista_por_departamentos.phtml`).
- En `rstgr` se separa visualmente por DL en la lista final.
