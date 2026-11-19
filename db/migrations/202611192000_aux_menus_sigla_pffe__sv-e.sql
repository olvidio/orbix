-- La sigla de la oficina «dre» / «des» (en las regiones, «der») pasa a ser «pffe».
-- Grupo de menús y nombres de menú («casas comunes (des)», «todas casas (des)»…).
-- Cada nombre corregido es un msgid de gettext: tras aplicar las migraciones, pasar
-- php tools/i18n/actualizar.php para regenerar los textos de menús y traducir los nuevos.
-- aux_grupmenu y aux_menus de todos los esquemas *v (sv-e, datos). Equivalentes: el otro de __sv-e / __sf
-- y ref_grupmenu / ref_menus en __comun.
-- No se toca el «des» catalán («des de», «des del»). Idempotente.
UPDATE *.aux_grupmenu SET grup_menu = 'pffe' WHERE grup_menu IN ('dre', 'des', 'der');
UPDATE *.aux_menus SET menu = regexp_replace(menu, '\m(dre|des)\M', 'pffe', 'g')
WHERE menu ~ '\m(dre|des)\M' AND menu !~ '\mdes (de|del|dels|d)\M';
