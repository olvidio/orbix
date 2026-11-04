---
id: "casas.casa_ec.consultar.flujo"
tipo: "flujo_frontend"
modulo: "casas"
nombre: "Flujo - Consultar estadística económica por años"
capacidad: "casas.casa_ec.consultar"
pantallas_principales: ["casas.pantalla.casa_ec"]
fragmentos: ["casas.pantalla.casas_resumen_lista"]
acciones: ["listar"]
endpoints: []
estado_revision: "revisado"
---

# Flujo - Consultar estadística económica por años

Resumen económico de una o varias casas, año a año.

## Objetivo De Usuario

Ver, para las casas elegidas, cómo han evolucionado la ocupación, los ingresos, los gastos y el superávit en los últimos años, y desplegar el detalle por actividad.

## Punto De Entrada

Menú **estadística por años** (Gestión casas / Estadística económica). Abre la pantalla **RESUMEN ECONÓMICO** con el selector de casas. La tabla de resultados se carga debajo, sin salir de la pantalla, reutilizando el listado de `casas_resumen_lista` en modo anual.

Casas que ofrece el selector según quien entra:

- usuario de una casa: solo su(s) casa(s);
- oficinas **des** o **vcsd**: todas las casas activas;
- oficina **adl** (equivale al perfil de des): todas las casas activas;
- resto: las casas activas de su sección (sv o sf).

## Fragmentos O Pantallas Auxiliares

- `casas.pantalla.casas_resumen_lista`: tabla de resultados (modo anual, una fila por casa y año).

## Escenarios Inferidos

### Consultar la estadística de una casa

Pasos propuestos:
1. Abrir **estadística por años** desde el menú.
2. Elegir la casa en el desplegable del recuadro **RESUMEN ECONÓMICO**.
3. Pulsar **buscar**.
4. Leer la tabla que aparece debajo: una fila por año, con días ocupados, nº días x nº asistentes (previsto y real), ingresos previstos y reales acumulados, gastos reales, aportación real dl y superávit. Las columnas **%** indican el porcentaje respecto al total.
5. Solo se cuentan las actividades que no están borradas: los estados son proyecto, actual, terminada y borrable (1 a 4), y se excluye «borrable».
6. Si una casa pertenece a un grupo de casas, el superávit se calcula para el conjunto del grupo, no para cada casa por separado.
7. Pulsar **mostrar detalles** en la fila de un año para ver las actividades de esa casa (ocupación sv/sf e ingresos sv/sf). **ocultar detalles** lo pliega.

Endpoints asociados:
- Ninguno directo: la tabla se carga con `frontend/casas/controller/casas_resumen_lista.php` (datos de `/src/casas/casas_resumen_data`).

### Consultar varias casas

Pasos propuestos:
1. Abrir **estadística por años** y elegir una casa.
2. Al elegir una casa aparece debajo otro desplegable para elegir otra; repite hasta tener todas las casas que interesen.
3. Pulsar **buscar**: la tabla muestra cada casa con sus años y los totales por año.

Endpoints asociados:
- Ninguno directo.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `id_cdc`, `id_cdc_mas`, `id_cdc_num` (casas elegidas)
- `cdc_sel`, `que` (los rellena la propia pantalla)

Acciones JavaScript:
- `fnjs_ver`, `fnjs_mas_casas`, `fnjs_detalles`

## Errores Conocidos

La pantalla no muestra errores propios. Si no hay casas visibles para el usuario, el selector aparece vacío.

## Ruta de menú

- **Legacy:** adl > Gestión casas > estadística por años
- **Legacy (variante):** adl > Nuevo Calendario > Estadística por años
- **Pills2:** CASAS Y CTR > Gestión casas > estadística por años
- **Pills2 (variante):** ACTIVIDADES > Estadísticas económicas > Estadística por años
