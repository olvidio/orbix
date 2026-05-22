-- Renombres en global/public y columna idioma en actividades publicadas.
ALTER TABLE global.encargos RENAME COLUMN sf_sv TO grupo_encargo;
---ALTER TABLE public.tablon_anuncios RENAME COLUMN tanotado TO t_anotado;
---ALTER TABLE public.tablon_anuncios RENAME COLUMN teliminado TO t_eliminado;

ALTER TABLE public.u_centros RENAME COLUMN status TO active;
ALTER TABLE public.u_centros RENAME COLUMN f_status TO f_active;

ALTER TABLE public.x_locales RENAME COLUMN activo TO active;
ALTER TABLE public.a_actividades_all ADD COLUMN idioma varchar(12) NULL;

CREATE OR REPLACE VIEW public.av_actividades_pub AS
  SELECT * FROM public.a_actividades_all a_actividades
  WHERE a_actividades.publicado = true;
