-- La sigla de la oficina «dre» / «des» (en las regiones, «der») pasa a ser «pffe».
-- Plantillas de menús: ref_grupmenu y ref_menus (comun, datos). Equivalentes en los esquemas:
-- 202611192000_aux_menus_sigla_pffe (__sv-e y __sf).
-- No se toca el «des» catalán («des de», «des del»). Idempotente.
UPDATE public.ref_grupmenu SET grup_menu = 'pffe' WHERE grup_menu IN ('dre', 'des', 'der');
UPDATE public.ref_menus SET menu = regexp_replace(menu, '\m(dre|des)\M', 'pffe', 'g')
WHERE menu ~ '\m(dre|des)\M' AND menu !~ '\mdes (de|del|dels|d)\M';
