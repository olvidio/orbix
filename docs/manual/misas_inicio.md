# Guía de inicio: plan de misas

El programa de misas se usa de **dos maneras distintas**, con el mismo usuario de sacerdote:

| | Quién | Para qué |
|---|--------|----------|
| **Consulta** | Cualquier sacerdote con usuario `p-sacd` | Ver **sus** misas y, si quiere, el cuadro de un centro. |
| **Organización** | Quien monta el calendario de la zona (jefe de zona, jefe de calendario u oficina) | Definir encargos, plantilla, asignar sacerdotes y **comunicar** el plan. |

Si solo hay que mirar el horario propio, basta el apartado 4. Si hay que elaborar el plan de la zona, el 5.

Antes hacen falta dos cosas que **no son del módulo de misas**:

1. Un **usuario SACD** (apartado 1) — si no, nadie entra.
2. Las **zonas** (apartado 3) — si no, no hay mapa de centros ni de sacerdotes sobre el que montar el plan.

---

## 1. Antes de entrar: hace falta un usuario SACD

Sin un **usuario web de sacerdote** (rol `p-sacd`) nadie entra al plan de misas. Ese alta se hace en **usuarios**, no en misas.

Lo hace un administrador local (**ADMIN LOCAL > usuarios web**). El sacerdote no lo crea él mismo.

### 1.1. El sacerdote ya debe existir como persona

El usuario se **enlaza** a una ficha de persona. Esa persona debe:

- estar dada de alta en la delegación (numerario o agregado),
- estar **activa**,
- estar marcada como **sacerdote**.

Si no aparece en el desplegable al vincular el usuario, falta ese paso en **personas**.

### 1.2. Crear el usuario web

Menú: **ADMIN LOCAL > usuarios web > lista usuarios**.

1. Pulsar **nuevo usuario**.
2. Rellenar:
   - **login**: nombre con el que entrará.
   - **nombre a mostrar**.
   - **role**: `p-sacd`.
   - **password**: conviene marcar **cambio password** para que la cambie al entrar.
   - **email**.
3. Guardar.

En el alta, el desplegable del sacerdote **aún no sale**. Hay que volver a abrir la ficha:

4. Buscar el usuario y abrirlo.
5. En **sacd**, elegir a la persona correcta.
6. Guardar otra vez.

Ese enlace (usuario ↔ sacerdote) es lo que permite ver **su** plan. Si se deja vacío, al abrir el plan de sacerdote aparecerá que no hay SACD disponibles.

Entregar: dirección de Orbix, login y contraseña inicial.

Para **organizar** el plan hace falta, además, que ese sacerdote sea **jefe de la zona** (apartado 3). El usuario solo no basta.

---

## 2. Primera vez que entra

1. Identificarse con login y contraseña.
2. Si pide **cambiar la contraseña**, hacerlo.
3. Si pide **doble factor (2FA)**, seguir los pasos.

Debe verse el menú **ATENCIÓN SACD**. Si no aparece, el usuario no es `p-sacd` o el rol no tiene esos menús: se revisa en usuarios.

---

## 3. Las zonas (tampoco es el módulo de misas)

El plan de misas se elabora **por zona**. La zona es el mapa: qué centros y qué sacerdotes pertenecen a un territorio, y quién es el **jefe** que puede organizar ese calendario.

Eso se gestiona en **ATENCIÓN SACD > Gestión de zonas** (menú antiguo: **dre > zonas**), no en **Gestión de misas**.

Conviene tenerlo listo **antes** de encargos, plantilla o un plan nuevo. Cambiar asignaciones (mover centros o sacerdotes) suele exigir permiso de oficina (`des` o `vcsd`); sin ese permiso se puede **consultar** el listado, pero no reasignar. El jefe de zona puede organizar las misas de su zona aunque no sea él quien mueve a la gente de zona.

### 3.1. Qué hay que dejar claro

| Pieza | Dónde | Para qué sirve en misas |
|-------|--------|-------------------------|
| La zona en sí (nombre y **jefe**) | **zonas** / **zonas geogr.** | El jefe es quien ve esa zona en las pantallas de elaborar el plan. |
| Qué **centros** están en la zona | **Zonas-ctr** | Encargos, plantilla y **Plan ctr** agrupan por esos centros. |
| Qué **sacerdotes** están en la zona | **Zonas-sacd** | Salen en el desplegable de la cuadrícula (filtro «zona») y en **Iniciales sacd**. |
| Días de la semana de cada sacerdote | **Zonas-sacd** → **modificar** | El plan marca si ese día «está en la zona» o no (colores de aviso). |

Un sacerdote tiene una zona **propia** (donde pertenece) y puede tener **otras** asignaciones (iglesia / cgi) sin dejar la propia.

### 3.2. Crear la zona y nombrar al jefe

Menú: **ATENCIÓN SACD > Gestión de zonas > zonas** (a veces **zonas geogr.**).

Es un listado de las zonas de misas. Al crear o editar:

- **nombre zona**,
- **orden** (cómo salen en los desplegables),
- **grupo** (si se usa),
- **jefe zona**: el sacerdote que va a organizar el plan de esa zona.

Sin jefe, un usuario `p-sacd` normal **no verá esa zona** al preparar o modificar el plan. El jefe de calendario y el oficial de la delegación sí ven todas.

El jefe debe ser el mismo sacerdote al que está enlazado su usuario web (apartado 1).

### 3.3. Asignar centros a la zona

**Zonas-ctr**.

1. En **lista de centros de la zona**, elegir una zona (o **sin asignar zona**, o **sin asignar zona sf** si hay permiso).
2. Sale el listado de centros de esa selección.
3. Marcar los centros, elegir la zona de destino y pulsar **asignar**.
4. Destino **sin asignar zona** los saca de cualquier zona.

Hasta que un centro no está en una zona, no encaja bien en encargos ni en **Plan ctr**.

### 3.4. Asignar sacerdotes a la zona

**Zonas-sacd**.

1. En **lista de sacd de la zona**, elegir una zona. **sin asignar zona** muestra a los sacerdotes que aún no tienen ninguna.
2. Sale la tabla: sacerdote, zona, si es **propia**, y los días L–D.
3. Marcar uno o varios, elegir la zona de destino y:
   - **cambiar asignación zona**: mueve la zona **propia** (o da de alta al que estaba sin zona).
   - **añadir asignación iglesia/cgi**: le añade esa zona **sin quitarle la propia** (colabora allí).
4. Destino **sin asignar zona** quita esa asignación.

Para los **días** en que atiende esa zona:

1. Marcar **un** sacerdote.
2. **modificar**.
3. En el cuadro: **propia** y los días (lunes a domingo).
4. **Grabar**.

Esos días son los que el plan usa para saber si ese sacerdote «está en la zona» el día de la misa. El filtro **libre a 1ª hora** de la cuadrícula también se apoya en esto.

### 3.5. Ver el mapa completo

**Lista sacd-zona**: todos los sacerdotes y sus zonas (propia o no), para comprobar que no queda nadie fuera.

---

## 4. Caso A — el sacerdote que consulta

No elabora el calendario. Solo mira lo que ya le han **comunicado**.

Menú habitual: **ATENCIÓN SACD > Gestión de misas**.

No necesita ser jefe de zona. Sí necesita el usuario `p-sacd` (apartado 1).

### 4.1. Ver el plan propio

**Plan sacerdote** (menú antiguo: **dre > Misas > Plan sacerdote**).

1. En **Sacerdote** suele aparecer ya el propio nombre. Quien no es jefe de zona **solo se ve a sí mismo**. Un jefe de zona puede elegir a los sacerdotes de su zona.
2. Elegir el **periodo**:
   - esta semana (por defecto),
   - este mes,
   - próxima semana (lunes a domingo),
   - próximo mes,
   - **otro**, y entonces las dos fechas (desde / hasta).
3. La lista se actualiza al cambiar periodo o fechas. No hace falta un botón de búsqueda.

| Columna | Contenido |
|---------|-----------|
| **Día** | Día, fecha y, si hay, hora (`L 8.9 07:30-08:00`). |
| **Encargo** | Nombre de la misa o encargo. |
| **Observaciones** | Notas de ese día, si las hay. |

Los días sin nada salen igual, con encargo vacío.

### 4.2. Ver el plan de un centro

**Plan ctr** (menú antiguo: **dre > Misas > Plan centro**).

Sirve para ver **quién cubre cada encargo de un centro** esos días.

1. En **zona**, lo habitual es **centros encargos**: los centros donde el sacerdote tiene un encargo vigente. El jefe de zona puede elegir también su zona.
2. Elegir el **centro**.
3. Elegir el **periodo**.

La tabla tiene encargos en filas, días en columnas, y en cada celda las **iniciales** del sacerdote y la hora. Debajo hay una **leyenda** (iniciales → nombre). Un asterisco (`*`) indica que hay observación.

### 4.3. Qué no se ve

El que solo consulta **no ve el borrador**. Mientras el plan de la zona está en **propuesta**, esas celdas no salen aquí.

Cuando quien organiza pasa el estado a **comunicado sacerdotes** o **comunicado centros**, sí aparecen.

(El jefe de zona, al consultar, sí ve también las propuestas de su zona.)

Las pantallas de elaborar el plan (plantilla, nuevo plan, modificar plan, cambiar estado, encargos, iniciales) **no las necesita** quien solo consulta.

### 4.4. Si no sale lo esperado (consulta)

| Qué ocurre | Qué comprobar |
|------------|----------------|
| No aparece **ATENCIÓN SACD** | Usuario sin rol `p-sacd`. Lo resuelve el administrador en **usuarios**. |
| Aviso de que **no hay SACD disponibles** | El usuario no está vinculado a una persona en el campo **sacd**. |
| El plan sale **vacío** | Probar otro periodo. Si sigue vacío, ese tramo **aún no está comunicado**. |
| Falta un día que sí está en el calendario de zona | Sigue en **propuesta**, o no está asignado a ese sacerdote. |

---

## 5. Caso B — el sacerdote que organiza

Monta el calendario de la zona: encargos, plantilla, asignaciones y comunicación. Trabaja en **ATENCIÓN SACD > Gestión de misas** (menú antiguo: **dre > Misas**).

Orden recomendado: **zonas** ya listas (apartado 3) → **datos fijos** de misas (5.2) → **ciclo de cada periodo** (5.3).

### 5.1. Quién puede organizar

En las pantallas de elaboración solo salen las zonas de las que el usuario es **jefe** (campo **jefe zona** del apartado 3.2). Quien no es jefe de ninguna zona verá los desplegables vacíos o el aviso de que no tiene permiso.

Ven todas las zonas el **jefe de calendario** y el **oficial de la delegación**.

Hace falta el mismo usuario `p-sacd` del apartado 1, enlazado a esa persona.

Los sacerdotes y centros que se van a usar deben estar ya en la zona (apartado 3). Si no, no salen en el desplegable de la celda ni en **Plan ctr**.

### 5.2. Datos fijos del plan (una vez, o cuando cambie el mapa)

Estos pasos no se repiten cada semana. Las **zonas** ya deben estar hechas; aquí se define el contenido del plan.

#### Encargos de la zona

**Modificar encargos**.

1. Elegir la **zona** (y, si se quiere, el criterio de **orden**: orden, prioridad o alfabético).
2. **nuevo** para dar de alta un encargo (una misa, un centro, un horario típico…).
3. Clic en una fila para editarla.
4. Rellenar al menos: **tipo de encargo**, **lugar** (centro) o **descripción del lugar**, **nombre del encargo** (se puede pulsar **generar**), **orden** y **prioridad**.
5. **Grabar**. **Eliminar encargo** lo borra de la zona.

Sin encargos, la cuadrícula del plan no tiene filas que rellenar.

#### Encargos visibles en un centro

**Encargos ctr**.

Decide qué encargos de la zona **ve el centro** cuando alguien abre **Plan ctr**. Un encargo puede existir en la zona y no mostrarse en un centro concreto.

1. Elegir la **zona**.
2. **nuevo**, o clic en una fila.
3. Elegir **encargo** y **centro**.
4. **Grabar** (o **Eliminar encargo** para quitar esa visibilidad).

#### Iniciales de los sacerdotes

**Iniciales sacd**.

En la cuadrícula y en el plan del centro se ven **iniciales**, no el nombre completo. Hay que definirlas (y un color, si se quiere) para cada sacerdote de la zona.

1. Elegir la **zona**.
2. Clic en la fila del sacerdote.
3. Poner **iniciales** y, si aplica, **color**.
4. Guardar.

Si un sacerdote no está en **Zonas-sacd**, no saldrá aquí.

#### Plantilla

**Modificar plantilla**.

La plantilla es el **modelo repetible** (una semana tipo, con o sin tratamiento especial de domingos, o un mes). El plan de fechas reales se **copia** de aquí al preparar un periodo.

1. Elegir **zona**, **tipo de plantilla** y **orden**.
2. Tipos habituales:
   - **semanal una opción** / **semanal tres opciones**,
   - **semanal y domingos** (una o tres opciones),
   - **mensual** (una o tres opciones).  
   «Tres opciones» es una rotación de tres bloques; «una opción» es un único modelo.
3. La cuadrícula se edita igual que el plan (apartado 5.3): clic en la celda, sacerdote, horas, observaciones.
4. Si ya hay otra plantilla de la zona, se puede **importar** a la que se está viendo. **Borra todos los datos de la plantilla que se está viendo.**

### 5.3. Cada periodo: preparar → ajustar → comunicar

#### Preparar un plan nuevo

**Nuevo plan**.

1. Elegir **zona**, **tipo de plantilla** (la que se usará como origen) y **periodo** (próxima semana, próximo mes, u **otro** con fechas).
2. Pulsar **preparar**.

Copia la plantilla a esas fechas reales. **Borra todo lo que ya hubiera en ese periodo para esa zona.** No usarlo para un retoque puntual: para eso está **Modificar plan**.

Tras preparar, se ve la cuadrícula del plan generado. Suele quedar en estado **propuesta** (los sacerdotes que solo consultan **aún no lo ven**).

#### Ajustar asignaciones

**Modificar plan**.

1. Elegir **zona**, **orden** y **periodo**.
2. Clic en una **celda de misa** (fila de encargo × día). Se abre un cuadro:
   - filtro de sacerdotes: **libre a 1ª hora**, **zona**, **dl** o **de paso**;
   - **sacerdote**;
   - **inicio** y **fin**;
   - **observaciones**.
3. **Save** para guardar. Dejar el sacerdote vacío y guardar **quita** la asignación.

El filtro **zona** lista a los asignados en **Zonas-sacd**. **Libre a 1ª hora** tiene en cuenta los días marcados en esa ficha.

Las filas de sacerdotes (no las de misa) colorean avisos: por ejemplo **dos misas** (amarillo), **más de dos** o **dos a primera hora** (rojo), **en la zona sin misa** (verde), **no está en la zona y tiene misa a primera hora** (rojo). Sirven para revisar, no sustituyen el criterio de quien organiza.

#### Revisar sin editar

**Ver plan zona**: la misma cuadrícula, solo lectura.

**Plan sacerdote** y **Plan ctr** (apartado 4) sirven para comprobar lo que verá cada uno **después** de comunicar.

#### Comunicar el plan

**Cambiar estado**.

1. Elegir **zona**, **nuevo estado** y **periodo**.
2. Pulsar **cambiar**. Afecta a **todo** el periodo de esa zona.

Estados:

| Estado | Efecto |
|--------|--------|
| **propuesta** | Borrador. El sacerdote que solo consulta **no lo ve**. |
| **comunicado sacerdotes** | Ya sale en **Plan sacerdote**. |
| **comunicado centros** | También sale en **Plan ctr** para los centros. |

Secuencia habitual: trabajar en propuesta → comunicar a sacerdotes → cuando esté cerrado, comunicar a centros.

### 5.4. Si no sale lo esperado (organización)

| Qué ocurre | Qué comprobar |
|------------|----------------|
| **No tiene permiso para ver esta página** / sin zonas | No es **jefe zona** de ninguna (apartado 3.2), ni jefe de calendario / oficial. |
| No aparecen botones para mover centros o sacerdotes | Falta permiso de oficina (`des` / `vcsd`). Se consulta, pero no se reasigna. |
| Cuadrícula vacía al preparar | Faltan **encargos** de la zona o la **plantilla** no tiene filas. |
| No aparecen sacerdotes en el desplegable de la celda | No están en **Zonas-sacd**, o el filtro (zona / dl / de paso) no los incluye. |
| Avisos de «no está en la zona» | Faltan los **días** en **Zonas-sacd** → **modificar**. |
| En **Plan sacerdote** no se ve el plan recién preparado | Sigue en **propuesta**. Hay que **cambiar estado**. |
| En **Plan ctr** no sale un encargo | El centro no está en **Zonas-ctr**, o el encargo no está en **Encargos ctr**. |
| Iniciales raras (`--`) | Falta rellenar **Iniciales sacd**. |

---

## 6. Resumen

**Acceso:** usuario `p-sacd` enlazado a la persona (**usuarios**, no misas).

**Mapa:** zonas, jefe, centros y sacerdotes (**Gestión de zonas**, no misas). Zona propia frente a iglesia/cgi; días de la semana.

**Quien consulta:** **Plan sacerdote** (y si quiere **Plan ctr**). Solo ve lo **comunicado**.

**Quien organiza:** debe ser **jefe de la zona**. Encargos e iniciales → plantilla → **nuevo plan** → **modificar plan** → **cambiar estado** a comunicado sacerdotes (y luego centros).
