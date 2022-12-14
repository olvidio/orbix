<?php

namespace core;

/**
 * PgTimestamp
 *
 * Date and timestamp converter
 *
 * @package   Foundation
 * @copyright 2014 - 2017 Grégoire HUBERT
 * @author    Grégoire HUBERT <hubert.greg@gmail.com>
 * @license   X11 {@link http://opensource.org/licenses/mit-license.php}
 * @see       ConverterInterface
 */
class Converter
{

    var $Converter;
    var $type;

    public function __construct($type, $data)
    {
        $this->type = $type;
        switch ($type) {
            case 'date':
            case 'datetime':
                $this->Converter = new PgTimestamp($data);
                break;
            case 'timestamp':
                $this->Converter = new PgTimestamp($data);
                break;

            default:
                ;
                break;
        };
    }

    public function fromPg()
    {
        return $this->Converter->fromPg();
    }

    public function toPg()
    {
        return $this->Converter->toPg($this->type);
    }
}
