-- Registro de lo que se pregunta en «Preguntar a la documentación» y de si la respuesta sirvió.
-- BD comun: las guías son las mismas para todas las delegaciones (sv y sf).
-- Es anónimo: guarda la delegación y la pantalla desde la que se preguntó, nunca el usuario.
-- Propietario: orbix (sin GRANT extra).

CREATE TABLE IF NOT EXISTS public.ayuda_consultas (
    uuid_item     uuid PRIMARY KEY,
    f_consulta    timestamp without time zone NOT NULL,
    pregunta      text NOT NULL,
    esquema       text,
    pantalla      text,
    n_resultados  integer NOT NULL DEFAULT 0,
    puntuacion    numeric(8,2),
    fuente        text,
    titulo        text,
    util          boolean,
    comentario    text,
    estado        smallint NOT NULL DEFAULT 1,
    CONSTRAINT ayuda_consultas_estado_valido CHECK (estado IN (1, 2, 3))
);

ALTER TABLE public.ayuda_consultas OWNER TO orbix;

CREATE INDEX IF NOT EXISTS ayuda_consultas_f_consulta_idx ON public.ayuda_consultas (f_consulta);
CREATE INDEX IF NOT EXISTS ayuda_consultas_estado_idx ON public.ayuda_consultas (estado);

COMMENT ON TABLE public.ayuda_consultas IS
    'Preguntas hechas a la ayuda («Preguntar a la documentación»), con el primer resultado que se '
    'dio y la valoración de quien preguntó. Sirve para revisar las guías de docs/guias. Sin usuario.';

COMMENT ON COLUMN public.ayuda_consultas.esquema IS 'Delegación de la sesión (p. ej. H-dlbv); no el usuario';
COMMENT ON COLUMN public.ayuda_consultas.pantalla IS 'Controlador frontend desde el que se abrió la ayuda, si se sabe';
COMMENT ON COLUMN public.ayuda_consultas.n_resultados IS 'Resultados devueltos; 0 = la ayuda no supo contestar';
COMMENT ON COLUMN public.ayuda_consultas.puntuacion IS 'Puntuación del primer resultado';
COMMENT ON COLUMN public.ayuda_consultas.fuente IS 'Documento del primer resultado (ruta bajo docs/)';
COMMENT ON COLUMN public.ayuda_consultas.titulo IS 'Título del primer resultado';
COMMENT ON COLUMN public.ayuda_consultas.util IS 'NULL sin valorar; true sirvió; false no sirvió';
COMMENT ON COLUMN public.ayuda_consultas.comentario IS 'Qué buscaba quien marcó que no le sirvió';
COMMENT ON COLUMN public.ayuda_consultas.estado IS '1 pendiente de revisar, 2 resuelta, 3 descartada';
