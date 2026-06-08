<?php
namespace src\shared\config;

/**
 * @desc ConfigMagik - Ini-File Reader and Writer (ConfigKeeper)
 * @author BennyZaminga <bzaminga@web.de>
 * @date Sat Jul 03 19:52:46 CEST 2004
 * @version 0.01 - Sat Jul 03 19:52:46 2004
 *     - 1st release
 * @version 0.02 - Sun Jul 18 16:04:51 2004
 *     - Added listKeys()
 *     - Added listSections()
 *     - Added toString() [TEXT and HTML -View]
 *     - Added Editor to package
 *     - Changed PROCESS_SECTIONS to enabled by default
 *     - Lacks of SECTIONS_AUTORECOGNITION (comming soon)
 * @version 0.03 - Wed Aug 11 01:31:52 2004
 *     - Fixed a bug in get()
 *     - Added additional code handling PROCESS_SECTIONS more gracefully
 */
class ConfigMagik
{
    public ?string $PATH = null;
    /** @var array<string, mixed> */
    public array $VARS = [];
    public bool $SYNCHRONIZE = false;
    public bool $PROCESS_SECTIONS = true;
    public bool $PROTECTED_MODE = true;
    /** @var list<string> */
    public array $ERRORS = [];

    private function scalarToString(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }
        if (is_int($value) || is_float($value) || is_bool($value)) {
            return (string) $value;
        }

        return '';
    }

    /**
     * @desc   Constructor of this class.
     * @param string|null $path Path to ini-file to load at startup.
     * NOTE:   If the ini-file can not be found, it will try to generate a
     *         new empty one at the location indicated by path passed to
     *         constructor-method of this class.
     * @param bool $synchronize TRUE for constant synchronisation of memory and file (disabled by default).
     * @param bool $process_sections TRUE or FALSE to enable or disable sections in your ini-file (enabled by default).
     */
    function __construct(?string $path = null, bool $synchronize = false, bool $process_sections = true)
    {
        $this->PROCESS_SECTIONS = $process_sections;
        $this->SYNCHRONIZE = $synchronize;
        // if a path was passed and file exists, try to load it
        if ($path != null) {
            // set passed path as class-var
            $this->PATH = $path;
            if (!is_file($path)) {
                // conf-file seems not to exist, try to create an empty new one
                $fp_new = @fopen($path, 'w', false);
                if (!$fp_new) {
                    $err = "ConfigMagik::ConfigMagik() - Could not create new config-file('$path'), error.";
                    array_push($this->ERRORS, $err);
                    die($err);
                } else {
                    fclose($fp_new);
                }
            } else {
                // try to load and parse ini-file at specified path
                $loaded = $this->load($path);
                if (!$loaded) die();
            }
        }
    }

    /**
     * @return list<string>
     */
    function getErrors(): array
    {
        return $this->ERRORS;
    }

    /**
     * @desc                      Retrieves the value for a given key.
     * @param string|null $key Key or name of directive to set in current config.
     * @param string|null $section Name of section to set key/value-pair therein.
     * NOTE:                   Section must only be specified when sections are used in your ini-file.
     * @return mixed           Returns the value or NULL on failure.
     * NOTE:                   An empty directive will always return an empty string.
     *                         Only when directive can not be found, NULL is returned.
     */
    function get(?string $key = null, ?string $section = null): mixed
    {
        // if section was passed, change the PROCESS_SECTION-switch (FIX: 11/08/2004 BennyZaminga)
        if ($section) $this->PROCESS_SECTIONS = true;
        else           $this->PROCESS_SECTIONS = false;
        // get requested value
        if (empty($this->VARS)) {
            return null;
        }
        if ($this->PROCESS_SECTIONS) {
            if ($key === null) {
                return null;
            }
            $sectionVars = $this->VARS[$section] ?? null;
            if (!is_array($sectionVars)) {
                return null;
            }
            $value = $sectionVars[$key] ?? null;
        } else {
            if ($key === null) {
                return null;
            }
            $value = $this->VARS[$key] ?? null;
        }
        // if value was not found (false), return NULL (FIX: 11/08/2004 BennyZaminga)
        if ($value === false) {
            return null;
        }
        // return found value
        return $value;
    }

    /**
     * @desc   Sets the value for a given key (in given section, if any specified).
     * @param string $key Key or name of directive to set in current config.
     * @param mixed $value Value of directive to set in current config.
     * @param string|null $section Name of section to set key/value-pair therein.
     * NOTE:   Section must only be specified when sections are enabled in your ini-file.
     * @return bool            Returns TRUE on success, FALSE on failure.
     */
    function set(string $key, mixed $value, ?string $section = null): bool
    {
        // when sections are enabled and user tries to genarate non-sectioned vars,
        // throw an error, this is definitely not allowed.
        if ($this->PROCESS_SECTIONS and !$section) {
            $err = "ConfigMagik::set() - Passed no section when in section-mode, nothing was set.";
            array_push($this->ERRORS, $err);
            return false;
        }
        // set key with given value in given section (if enabled)
        if ($this->PROCESS_SECTIONS) {
            $sectionVars = $this->VARS[$section] ?? [];
            if (!is_array($sectionVars)) {
                $sectionVars = [];
            }
            $sectionVars[$key] = $value;
            $this->VARS[$section] = $sectionVars;
        } else {
            $this->VARS[$key] = $value;
        }
        // synchronize memory with file when enabled
        if ($this->SYNCHRONIZE) {
            $this->save();
        }
        return true;
    }

    /**
     * @desc   Remove a directive (key and it's value) from current config.
     * @param string $key Name of key to remove form current config.
     * @param string|null $section Optional name of section (if used).
     * @return bool            Returns TRUE on success, FALSE on failure.
     */
    function removeKey(string $key, ?string $section = null): bool
    {
        // check if section was passed and it's valid
        if ($section != null) {
            if (in_array($section, array_keys($this->VARS)) == false) {
                $err = "ConfigMagik::removeKey() - Could not find section('$section'), nothing was removed.";
                array_push($this->ERRORS, $err);
                return false;
            }
            $sectionVars = $this->VARS[$section] ?? null;
            if (!is_array($sectionVars)) {
                return false;
            }
            // look if given key exists in given section
            if (in_array($key, array_keys($sectionVars)) === false) {
                $err = "ConfigMagik::removeKey() - Could not find key('$key'), nothing was removed.";
                array_push($this->ERRORS, $err);
                return false;
            }
            // remove key from section
            $pos = array_search($key, array_keys($sectionVars), true);
            if ($pos === false) {
                return false;
            }
            array_splice($sectionVars, $pos, 1);
            $this->VARS[$section] = $sectionVars;
            return true;
        } else {
            // look if given key exists
            if (in_array($key, array_keys($this->VARS)) === false) {
                $err = "ConfigMagik::removeKey() - Could not find key('$key'), nothing was removed.";
                array_push($this->ERRORS, $err);
                return false;
            }
            // remove key (sections disabled)
            $pos = array_search($key, array_keys($this->VARS), true);
            if ($pos === false) {
                return false;
            }
            array_splice($this->VARS, $pos, 1);
            // synchronisation-stuff
            if ($this->SYNCHRONIZE) $this->save();
            // return
            return true;
        }
    }

    /**
     * @desc   Remove entire section from current config.
     * @param string $section Name of section to remove.
     * @return bool            Returns TRUE on success, FALSE on failure.
     */
    function removeSection(string $section): bool
    {
        // check if section exists
        if (in_array($section, array_keys($this->VARS), true) === false) {
            $err = "ConfigMagik::removeSection() - Section('$section') could not be found, nothing removed.";
            array_push($this->ERRORS, $err);
            return false;
        }
        // find position of $section in current config
        $pos = array_search($section, array_keys($this->VARS), true);
        if ($pos === false) {
            return false;
        }
        // remove section from current config
        array_splice($this->VARS, $pos, 1);
        // synchronisation-stuff
        if ($this->SYNCHRONIZE) $this->save();
        // return
        return true;
    }

    /**
     * @desc   Loads and parses ini-file from filesystem.
     * @param string|null $path Optional path to ini-file to load.
     * NOTE:   When not provided, path passed to constructor will be used.
     * @return bool Returns TRUE on success, FALSE on failure.
     */
    function load(?string $path = null): bool
    {
        // if path was specified, check if valid else abort
        if ($path != null and !is_file($path)) {
            $err = "ConfigMagik::load() - Path('$path') is invalid, nothing loaded.";
            array_push($this->ERRORS, $err);
            //echo $err;
            return false;
        } elseif ($path == null) {
            // no path was specified, fall back to class-var
            $path = $this->PATH;
        }
        if (!is_string($path) || $path === '') {
            return false;
        }
        /*
         * PHP's own method is used for parsing the ini-file instead of own code.
         * It's robust enough ;-)
         */
        $parsed = parse_ini_file($path, $this->PROCESS_SECTIONS);
        if ($parsed === false) {
            $err = "ConfigMagik::load() - Could not parse ini-file('$path'), error.";
            array_push($this->ERRORS, $err);
            return false;
        }
        $this->VARS = $parsed;
        return true;
    }

    /**
     * @desc   Writes ini-file to filesystem as file.
     * @param string|null $path Optional path to write ini-file to.
     * NOTE:   When not provided, path passed to constructor will be used.
     * @return bool Returns TRUE on success, FALSE on failure.
     */
    function save(?string $path = null): bool
    {
        // if no path was specified, fall back to class-var
        if ($path == null) $path = $this->PATH;
        if (!is_string($path) || $path === '') {
            return false;
        }

        $content = "";

        // PROTECTED_MODE-prefix
        if ($this->PROTECTED_MODE) {
            $content .= "<?PHP\n; /*\n; -- BEGIN PROTECTED_MODE\n";
        }

        // config-header
        $content .= "; This files was automagically generated by ConfigMagik\n";
        $content .= "; Do not edit this file by hand, use ConfigMagik instead.\n";
        $content .= "; Last modified: " . date('d M Y H:i s') . "\n";

        // check if there are sections to process
        if ($this->PROCESS_SECTIONS) {
            foreach ($this->VARS as $key => $elem) {
                $content .= "[" . $key . "]\n";
                if (!is_array($elem)) {
                    continue;
                }
                foreach ($elem as $key2 => $elem2) {
                    $content .= $key2 . " = \"" . $this->scalarToString($elem2) . "\"\n";
                }
            }
        } else {
            foreach ($this->VARS as $key => $elem) {
                $content .= $key . " = \"" . $this->scalarToString($elem) . "\"\n";
            }
        }

        // add PROTECTED_MODE-ending
        if ($this->PROTECTED_MODE) {
            $content .= "\n; -- END PROTECTED_MODE\n; */\n ?>\n    mm";
        }

        // write to file
        if (!$handle = @fopen($path, 'w')) {
            $err = "ConfigMagik::save() - Could not open file('$path') for writing, error.";
            array_push($this->ERRORS, $err);
            return false;
        }
        if (!fwrite($handle, $content)) {
            $err = "ConfigMagik::save() - Could not write to open file('$path'), error.";
            array_push($this->ERRORS, $err);
            return false;
        } else {
            // push a message onto error-stack
            $err = "ConfigMagik::save() - Sucessfully saved to file('$path').";
            //array_push($this->ERRORS, $err);
        }
        fclose($handle);
        return true;
    }

    /**
     * @desc   Renders this Object as formatted string (TEXT or HTML).
     * @param string $output_type Type of desired output. Can be 'TEXT' or 'HTML'.
     * @return string Returns a formatted string according to chosen output-type.
     */
    function toString(string $output_type = 'TEXT'): string
    {
        $output_type = strtoupper($output_type);
        // check requested output-type
        if ($output_type !== 'TEXT' && $output_type !== 'HTML') {
            $err = "ConfigMagik::toString() - Unknown OutputType('$output_type') was requested, falling back to TEXT.";
            array_push($this->ERRORS, $err);
            $output_type = 'TEXT';
        }
        if ($output_type === 'TEXT') {
            // render object as TEXT
            $out = "";
            ob_start();
            print_r($this->VARS);
            $out .= ob_get_clean();
            return $out;
        }
        // render object as HTML
        $out = "<table style='background:#FFEECC;border:1px solid black;' width=60%>\n";
        if ($this->PROCESS_SECTIONS) {
            // render with sections
            $out .= "\t<tr><th style='border:1px solid white;'>Section</th><th style='border:1px solid white;'>Key</th><th style='border:1px solid white;'>Value</th></tr>\n";
            $sections = $this->listSections();
            $num_sections = 0;
            $num_keys = 0;
            foreach ($sections as $section) {
                $out .= "\t<tr><td style='border:1px solid white;' colspan=3>$section</td></tr>\n";
                $keys = $this->listKeys($section);
                if ($keys === false) {
                    continue;
                }
                foreach ($keys as $key) {
                    $val = $this->get($key, $section);
                    $out .= "\t<tr><td>&nbsp;</td><td style='border:1px solid maroon;'>$key</td><td style='border:1px solid brown;'>" . $this->scalarToString($val) . "</td></tr>\n";
                    $num_keys++;
                }
                $num_sections++;
            }
            // summary of table (with sections)
            $out .= "\t<tr><td style='border:1px solid white;' colspan=3 align=right><code>There are <b>$num_keys keys</b> in <b>$num_sections sections</b>.</code></td></tr>\n";
        } else {
            // render without sections
            $keys = $this->listKeys();
            if ($keys !== false) {
                $num_keys = 0;
                $out .= "\t<tr><th style='border:1px solid white;'>Key</th><th style='border:1px solid white;'>Value</th></tr>\n";
                foreach ($keys as $key) {
                    $val = $this->get($key);
                    $out .= "\t<tr><td style='border:1px solid maroon;'>$key</td><td style='border:1px solid brown;'>" . $this->scalarToString($val) . "</td></tr>\n";
                    $num_keys++;
                }
                // summary of table (without sections)
                $out .= "\t<tr><td style='border:1px solid white;' colspan=2 align=right><code>There are <b>$num_keys keys</b>.</code></td></tr>\n";
            }
        }

        // close table
        $out .= "</table>";
        return $out;
    }

    /**
     * @desc                   Lists all keys.
     * @param string|null $section Optional section (needed only when using sections).
     * @return list<string>|false
     */
    function listKeys(?string $section = null): array|false
    {
        // check if section was passed
        if ($section !== null) {
            // check if passed section exists
            $sections = $this->listSections();
            if (in_array($section, $sections) === false) {
                $err = "ConfigMagik::listKeys() - Section('$section') could not be found.";
                array_push($this->ERRORS, $err);
                return false;
            }
            // list all keys in given section
            $list = [];
            $sectionVars = $this->VARS[$section];
            if (!is_array($sectionVars)) {
                return [];
            }
            $all = array_keys($sectionVars);
            foreach ($all as $possible_key) {
                if (!isset($this->VARS[$possible_key]) || !is_array($this->VARS[$possible_key])) {
                    array_push($list, $possible_key);
                }
            }
            return $list;
        } else {
            // list all keys (section-less)
            return array_keys($this->VARS);
        }
    }

    /**
     * @desc   List all sections (if any).
     * @return list<string> Returns a numeric array with all section-names as stings therein.
     */
    function listSections(): array
    {
        $list = [];
        // separate sections from normal keys
        $all = array_keys($this->VARS);
        foreach ($all as $possible_section) {
            if (is_array($this->VARS[$possible_section])) {
                array_push($list, $possible_section);
            }
        }
        return $list;
    }
}

?>