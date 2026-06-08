<?php

namespace src\shared\infrastructure\persistence\postgresql;

use Exception;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\TimeLocal;

/**
 * @author dani
 *
 */
class PgTimestamp
{

    const TS_FORMAT = 'Y-m-d H:i:s.uP';
    const DATE_FORMAT = 'Y-m-d';
    const TIME_FORMAT = 'H:i:s';

    private mixed $data;

    public function __construct(mixed $data)
    {
        $this->data = $data;
    }

    /**
     * Parse the output string from PostgreSQL and returns the converted value
     * into an according PHP representation.
     */
    public function fromPg(string $type): DateTimeLocal|TimeLocal|null
    {
        if ($this->data !== null && is_string($this->data)) {
            switch ($type) {
                case 'datetime':
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
    public function toPg(string $type): ?string
    {
        $rta = null;
        if (!$this->isEmptyValue()) {
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
    public function toPgStandardFormat(): ?string
    {
        return
            !$this->isEmptyValue()
                ? $this->checkData($this->data)->format(static::TS_FORMAT)
                : null;
    }

    private function isEmptyValue(): bool
    {
        return $this->data === null;
    }

    /**
     * @return DateTimeLocal
     * @throws \Exception
     */
    protected function checkData(mixed $data): DateTimeLocal
    {
        if ($data instanceof DateTimeLocal) {
            return $data;
        }

        if (!$data instanceof \DateTimeInterface) {
            try {
                // Si llega un string "HH:MM", añadimos los segundos antes de convertir
                if (is_string($data) && preg_match('/^\d{1,2}:\d{2}$/', $data)) {
                    $data .= ':00';
                }

                $data = DateTimeLocal::createFromLocal($data);
            } catch (\Exception $e) {
                $repr = is_scalar($data) || $data === null ? (string) $data : get_debug_type($data);
                throw new Exception(
                    sprintf(
                        "Cannot convert data from invalid datetime representation '%s'.",
                        $repr
                    ),
                    0,
                    $e
                );
            }
        } else {
            $className = DateTimeLocal::class;
            $converted = new $className();
            $converted->setTimestamp($data->getTimestamp());
            $data = $converted;
        }

        if (!$data instanceof DateTimeLocal) {
            throw new Exception(
                sprintf(
                    "Cannot convert data from invalid datetime representation '%s'.",
                    get_debug_type($data)
                )
            );
        }

        return $data;
    }
}