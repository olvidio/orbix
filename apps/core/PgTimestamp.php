<?php

namespace core;

use Exception;
use web\DateTimeLocal;
use web\TimeLocal;

/**
 * @author dani
 *
 */
class PgTimestamp
{

    const TS_FORMAT = 'Y-m-d H:i:s.uP';
    const DATE_FORMAT = 'Y-m-d';
    const TIME_FORMAT = 'H:i:s';

    /**
     * @var $data
     */
    var $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * fromPg
     *
     * Parse the output string from PostgreSQL and returns the converted value
     * into an according PHP representation.
     *
     * @return mixed   PHP representation of the data.
     */
    public function fromPg(string $type)
    {
        if ($this->data !== null) {
            switch ($type) {
                case 'timestamp':
                case 'date':
                    $oFecha = new DateTimeLocal($this->data);
                    break;
                case 'time':
                    $oFecha = TimeLocal::fromString($this->data);
                    break;
                default:
                    throw new \Exception('Unexpected value');
            }
        } else {
            $oFecha = null;
        }

        return $oFecha;
    }

    /**
     * toPg
     *
     * Convert a PHP representation into the according Pg formatted string.
     *
     * @param string $type
     * @return string  Pg converted string for input.
     */
    public function toPg(string $type)
    {
        $rta = null;
        if ($this->data !== null) {
            switch ($type) {
                case 'timestamp':
                    //$rta = sprintf("%s '%s'", $type, $this->checkData($this->data)->format(static::TS_FORMAT));
                    $rta = sprintf("%s", $this->checkData($this->data)->format(static::TS_FORMAT));
                    break;
                case 'date':
                    $rta = sprintf("%s", $this->checkData($this->data)->format(static::DATE_FORMAT));
                    break;
                case 'time':
                    $rta = sprintf("%s", $this->checkData($this->data)->format(static::TIME_FORMAT));
                    break;
            }
        } else {
            //$rta = sprintf("NULL::%s", $type);
            $rta = null;
        }
        return $rta;
    }

    /**
     * toPgStandardFormat
     *
     * Convert a PHP representation into short PostgreSQL format like used in
     * COPY values list.
     *
     * @return string   PostgreSQL standard representation.
     */
    public function toPgStandardFormat()
    {
        return
            $this->data !== null
                ? $this->checkData($this->data)->format(static::TS_FORMAT)
                : null;
    }

    /**
     * checkData
     *
     * Ensure a DateTime instance.
     *
     * @param mixed $data
     * @return DateTimeLocal
     * @throws \Exception
     */
    protected function checkData(mixed $data)
    {
        // Modificamos la comprobación para aceptar también web\TimeLocal
        if (!$data instanceof \DateTimeInterface && !$data instanceof \web\TimeLocal) {
            try {
                // Si llega un string "HH:MM", añadimos los segundos antes de convertir
                if (is_string($data) && preg_match('/^\d{1,2}:\d{2}$/', $data)) {
                    $data .= ':00';
                }

                $data = DateTimeLocal::createFromLocal($data);
            } catch (\Exception $e) {
                throw new Exception(
                    sprintf(
                        "Cannot convert data from invalid datetime representation '%s'.",
                        $data
                    ),
                    null,
                    $e
                );
            }
        }
        return $data;
    }
}