<?php
/**
 * Description of Pimvc\File\Csv\Parser
 *
 * @author pierrefromager
 */
namespace Pimvc\File\Csv;

class Parser
{
    # use first line/entry as field names

    public $heading = true;

    # override field names
    public $fields = [];

    # sort entries by this field
    public $sortBy = null;
    public $sortReverse = false;

    # sort behavior passed to ksort/krsort functions
    # regular = SORT_REGULAR
    # numeric = SORT_NUMERIC
    # string  = SORT_STRING
    public $sortType = null;

    # delimiter (comma) and enclosure (double quote)
    public $delimiter = ',';
    public $enclosure = '"';

    # basic SQL-like conditions for row matching
    public $conditions = null;

    # number of rows to ignore from beginning of data
    public $offset = null;

    # limits the number of returned rows to specified amount
    public $limit = null;

    # number of rows to analyze when attempting to auto-detect delimiter
    public $autoDepth = 15;

    # characters to ignore when attempting to auto-detect delimiter
    public $autoNonChars = "a-zA-Z0-9\n\r";

    # preferred delimiter characters, only used when all filtering method
    # returns multiple possible delimiters (happens very rarely)
    public $autoPreferred = ",;\t.:|";

    # character encoding options
    public $convertEncoding = false;
    public $inputEncoding = 'ISO-8859-1';
    public $outputEncoding = 'ISO-8859-1';

    # used by unparse(), save(), and output() functions
    public $linefeed = "\r\n";

    # only used by output() function
    public $outputDelimiter = ',';
    public $outputFilename = 'data.csv';

    # keep raw file data in memory after successful parsing (useful for debugging)
    public $keepFileData = false;

    /**
     * Internal variables
     */
    # current file
    public $file;

    # loaded file contents
    public $fileData;

    # error while parsing input data
    #  0 = No errors found. Everything should be fine :)
    #  1 = Hopefully correctable syntax error was found.
    #  2 = Enclosure character (double quote by default)
    #      was found in non-enclosed field. This means
    #      the file is either corrupt, or does not
    #      standard CSV formatting. Please validate
    #      the parsed data yourself.
    public $error = 0;

    # detailed error info
    public $error_info = [];

    # array of field values in data parsed
    public $titles = [];

    # two dimentional array of CSV data
    public $data = [];

    /**
     * __construct
     *
     * @param string $input
     * @param int $offset
     * @param int $limit
     * @param array $conditions
     */
    public function __construct($input = null, $offset = null, $limit = null, $conditions = [])
    {
        if ($offset !== null) {
            $this->offset = $offset;
        }
        if ($limit !== null) {
            $this->limit = $limit;
        }
        if (count($conditions) > 0) {
            $this->conditions = $conditions;
        }
        if (!empty($input)) {
            $this->parse($input);
        }
    }

    /**
     * parse
     * Parse CSV file or string
     *
     * @param string $input CSV file or string
     * @param int $offset
     * @param int $limit
     * @param array $conditions
     * @return boolean
     */
    public function parse($input = null, $offset = null, $limit = null, $conditions = [])
    {
        if ($input === null) {
            $input = $this->file;
        }
        if (!empty($input)) {
            if ($offset !== null) {
                $this->offset = $offset;
            }
            if ($limit !== null) {
                $this->limit = $limit;
            }
            if (count($conditions) > 0) {
                $this->conditions = $conditions;
            }
            if (is_readable($input)) {
                $this->data = $this->parseFile($input);
            } else {
                $this->fileData = &$input;
                $this->data = $this->parseString();
            }
            if ($this->data === false) {
                return false;
            }
        }
        return true;
    }

    /**
     * save
     * Save changes, or new file and/or data
     *
     * @param string $file file to save to
     * @param array $data 2D array with data
     * @param bool $append append current data to end of target CSV if exists
     * @param array $fields field names
     * @return bool
     */
    public function save($file = null, $data = [], $append = false, $fields = [])
    {
        if (empty($file)) {
            $file = &$this->file;
        }
        $mode = ($append) ? 'at' : 'wt';
        $is_php = (preg_match('/\.php$/i', $file)) ? true : false;
        return $this->_wfile($file, $this->unparse($data, $fields, $append, $is_php), $mode);
    }

    /**
     * prepareHeaders
     *
     * @param string $charset
     * @param string $filename
     */
    private static function prepareHeaders($charset, $filename)
    {
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Content-Encoding: ' . $charset);
        header('Content-type: text/csv; charset=' . $charset);
        header('Content-Disposition: attachment; filename=' . $filename);
        //echo "\xEF\xBB\xBF"; // UTF-8 BOM
    }

    /**
     * Generate CSV based string for output
     * @param   filename    if specified, headers and data will be output directly to browser as a downloable file
     * @param   data        2D array with data
     * @param   fields      field names
     * @param   delimiter   delimiter used to separate data
     * @return  CSV data using delimiter of choice, or default
     */
    public function output($filename = null, $data = [], $fields = [], $delimiter = null)
    {
        $filename = (empty($filename)) ? $this->outputFilename : $filename;
        $delimiter = ($delimiter === null) ? $this->outputDelimiter : $delimiter;
        $data = $this->unparse($data, $fields, null, null, $delimiter);
        if ($filename !== null) {
            $charset = mb_detect_encoding($data);
            self::prepareHeaders($charset, $filename);
            $data = mb_convert_encoding($data, $charset, "auto");
            echo $data;
            die;
            echo $data;
        }
        return $data;
    }

    /**
     * Convert character encoding
     * @param   input    input character encoding, uses default if left blank
     * @param   output   output character encoding, uses default if left blank
     * @return  nothing
     */
    public function encoding($input = null, $output = null)
    {
        $this->convertEncoding = true;
        if ($input !== null) {
            $this->inputEncoding = $input;
        }
        if ($output !== null) {
            $this->outputEncoding = $output;
        }
    }

    /**
     * Auto-Detect Delimiter: Find delimiter by analyzing a specific number of
     * rows to determine most probable delimiter character
     * @param   file           local CSV file
     * @param   parse          true/false parse file directly
     * @param   search_depth   number of rows to analyze
     * @param   preferred      preferred delimiter characters
     * @param   enclosure      enclosure character, default is double quote (").
     * @return  delimiter character
     */
    public function auto($file = null, $parse = true, $search_depth = null, $preferred = null, $enclosure = null)
    {
        if ($file === null) {
            $file = $this->file;
        }
        if (empty($search_depth)) {
            $search_depth = $this->autoDepth;
        }
        if ($enclosure === null) {
            $enclosure = $this->enclosure;
        }

        if ($preferred === null) {
            $preferred = $this->autoPreferred;
        }

        if (empty($this->fileData)) {
            if ($this->checkData($file)) {
                $data = &$this->fileData;
            } else {
                return false;
            }
        } else {
            $data = &$this->fileData;
        }

        $chars = [];
        $strlen = strlen($data);
        $enclosed = false;
        $n = 1;
        $to_end = true;

        // walk specific depth finding posssible delimiter characters
        for ($i = 0; $i < $strlen; $i++) {
            $ch = $data{$i};
            $nch = (isset($data{$i + 1})) ? $data{$i + 1} : false;
            $pch = (isset($data{$i - 1})) ? $data{$i - 1} : false;

            // open and closing quotes
            if ($ch == $enclosure) {
                if (!$enclosed || $nch != $enclosure) {
                    $enclosed = ($enclosed) ? false : true;
                } elseif ($enclosed) {
                    $i++;
                }

                // end of row
            } elseif (($ch == "\n" && $pch != "\r" || $ch == "\r") && !$enclosed) {
                if ($n >= $search_depth) {
                    $strlen = 0;
                    $to_end = false;
                } else {
                    $n++;
                }

                // count character
            } elseif (!$enclosed) {
                if (!preg_match('/[' . preg_quote($this->autoNonChars, '/') . ']/i', $ch)) {
                    if (!isset($chars[$ch][$n])) {
                        $chars[$ch][$n] = 1;
                    } else {
                        $chars[$ch][$n] ++;
                    }
                }
            }
        }

        // filtering
        $depth = ($to_end) ? $n - 1 : $n;
        $filtered = [];
        foreach ($chars as $char => $value) {
            if ($match = $this->checkCount($char, $value, $depth, $preferred)) {
                $filtered[$match] = $char;
            }
        }

        // capture most probable delimiter
        ksort($filtered);
        $this->delimiter = reset($filtered);

        // parse data
        if ($parse) {
            $this->data = $this->parseString();
        }

        return $this->delimiter;
    }

    /**
     * Read file to string and call parseString()
     * @param   file   local CSV file
     * @return  2D array with CSV data, or false on failure
     */
    public function parseFile($file = null)
    {
        if ($file === null) {
            $file = $this->file;
        }
        if (empty($this->fileData)) {
            $this->loadData($file);
        }
        return (!empty($this->fileData)) ? $this->parseString() : false;
    }

    /**
     * Parse CSV strings to arrays
     * @param   data   CSV string
     * @return  2D array with CSV data, or false on failure
     */
    public function parseString($data = null)
    {
        if (empty($data)) {
            if ($this->checkData()) {
                $data = &$this->fileData;
            } else {
                return false;
            }
        }

        $white_spaces = str_replace($this->delimiter, '', " \t\x0B\0");

        $rows = [];
        $row = [];
        $row_count = 0;
        $current = '';
        $head = (!empty($this->fields)) ? $this->fields : [];
        $col = 0;
        $enclosed = false;
        $was_enclosed = false;
        $strlen = strlen($data);

        // walk through each character
        for ($i = 0; $i < $strlen; $i++) {
            $ch = $data{$i};
            $nch = (isset($data{$i + 1})) ? $data{$i + 1} : false;
            $pch = (isset($data{$i - 1})) ? $data{$i - 1} : false;

            // open/close quotes, and inline quotes
            if ($ch == $this->enclosure) {
                if (!$enclosed) {
                    if (ltrim($current, $white_spaces) == '') {
                        $enclosed = true;
                        $was_enclosed = true;
                    } else {
                        $this->error = 2;
                        $error_row = count($rows) + 1;
                        $error_col = $col + 1;
                        if (!isset($this->error_info[$error_row . '-' . $error_col])) {
                            $this->error_info[$error_row . '-' . $error_col] = array(
                                'type' => 2,
                                'info' => 'Syntax error found on row ' . $error_row . '. Non-enclosed fields can not contain double-quotes.',
                                'row' => $error_row,
                                'field' => $error_col,
                                'field_name' => (!empty($head[$col])) ? $head[$col] : null,
                            );
                        }
                        $current .= $ch;
                    }
                } elseif ($nch == $this->enclosure) {
                    $current .= $ch;
                    $i++;
                } elseif ($nch != $this->delimiter && $nch != "\r" && $nch != "\n") {
                    for ($x = ($i + 1); isset($data{$x}) && ltrim($data{$x}, $white_spaces) == ''; $x++) {
                    }
                    if ($data{$x} == $this->delimiter) {
                        $enclosed = false;
                        $i = $x;
                    } else {
                        if ($this->error < 1) {
                            $this->error = 1;
                        }
                        $error_row = count($rows) + 1;
                        $error_col = $col + 1;
                        if (!isset($this->error_info[$error_row . '-' . $error_col])) {
                            $this->error_info[$error_row . '-' . $error_col] = array(
                                'type' => 1,
                                'info' =>
                                'Syntax error found on row ' . (count($rows) + 1) . '. ' .
                                'A single double-quote was found within an enclosed string. ' .
                                'Enclosed double-quotes must be escaped with a second double-quote.',
                                'row' => count($rows) + 1,
                                'field' => $col + 1,
                                'field_name' => (!empty($head[$col])) ? $head[$col] : null,
                            );
                        }
                        $current .= $ch;
                        $enclosed = false;
                    }
                } else {
                    $enclosed = false;
                }

                // end of field/row
            } elseif (($ch == $this->delimiter || $ch == "\n" || $ch == "\r") && !$enclosed) {
                $key = (!empty($head[$col])) ? $head[$col] : $col;
                $row[$key] = ($was_enclosed) ? $current : trim($current);
                $current = '';
                $was_enclosed = false;
                $col++;

                // end of row
                if ($ch == "\n" || $ch == "\r") {
                    if ($this->validateOffset($row_count) && $this->validateRowConditions($row, $this->conditions)) {
                        if ($this->heading && empty($head)) {
                            $head = $row;
                        } elseif (empty($this->fields) || (!empty($this->fields) && (($this->heading && $row_count > 0) || !$this->heading))) {
                            if (!empty($this->sortBy) && !empty($row[$this->sortBy])) {
                                if (isset($rows[$row[$this->sortBy]])) {
                                    $rows[$row[$this->sortBy] . '_0'] = &$rows[$row[$this->sortBy]];
                                    unset($rows[$row[$this->sortBy]]);
                                    for ($sn = 1; isset($rows[$row[$this->sortBy] . '_' . $sn]); $sn++) {
                                    }
                                    $rows[$row[$this->sortBy] . '_' . $sn] = $row;
                                } else {
                                    $rows[$row[$this->sortBy]] = $row;
                                }
                            } else {
                                $rows[] = $row;
                            }
                        }
                    }
                    $row = [];
                    $col = 0;
                    $row_count++;
                    if ($this->sortBy === null && $this->limit !== null && count($rows) == $this->limit) {
                        $i = $strlen;
                    }
                    if ($ch == "\r" && $nch == "\n") {
                        $i++;
                    }
                }

                // append character to current field
            } else {
                $current .= $ch;
            }
        }
        $this->titles = $head;
        if (!empty($this->sortBy)) {
            $sortType = SORT_REGULAR;
            if ($this->sortType == 'numeric') {
                $sortType = SORT_NUMERIC;
            } elseif ($this->sortType == 'string') {
                $sortType = SORT_STRING;
            }
            ($this->sortReverse) ? krsort($rows, $sortType) : ksort($rows, $sortType);
            if ($this->offset !== null || $this->limit !== null) {
                $rows = array_slice($rows, ($this->offset === null ? 0 : $this->offset), $this->limit, true);
            }
        }
        if (!$this->keepFileData) {
            $this->fileData = null;
        }
        return $rows;
    }

    /**
     * Create CSV data from array
     * @param   data        2D array with data
     * @param   fields      field names
     * @param   append      if true, field names will not be output
     * @param   is_php      if a php die() call should be put on the first
     *                      line of the file, this is later ignored when read.
     * @param   delimiter   field delimiter to use
     * @return  CSV data (text string)
     */
    public function unparse($data = [], $fields = [], $append = false, $is_php = false, $delimiter = null)
    {
        if (!is_array($data) || empty($data)) {
            $data = &$this->data;
        }
        if (!is_array($fields) || empty($fields)) {
            $fields = &$this->titles;
        }
        if ($delimiter === null) {
            $delimiter = $this->delimiter;
        }

        $string = ($is_php) ? "<?php header('Status: 403'); die(' '); ?>" . $this->linefeed : '';
        $entry = [];

        // create heading
        if ($this->heading && !$append && !empty($fields)) {
            foreach ($fields as $key => $value) {
                $entry[] = $this->encloseValue($value);
            }
            $string .= implode($delimiter, $entry) . $this->linefeed;
            $entry = [];
        }

        // create data
        foreach ($data as $key => $row) {
            foreach ($row as $field => $value) {
                $entry[] = $this->encloseValue($value);
            }
            $string .= implode($delimiter, $entry) . $this->linefeed;
            $entry = [];
        }

        return $string;
    }

    /**
     * Load local file or string
     * @param   input   local CSV file
     * @return  true or false
     */
    public function loadData($input = null)
    {
        $data = null;
        $file = null;
        if ($input === null) {
            $file = $this->file;
        } elseif (file_exists($input)) {
            $file = $input;
        } else {
            $data = $input;
        }
        if (!empty($data) || $data = $this->_rfile($file)) {
            if ($this->file != $file) {
                $this->file = $file;
            }
            if (preg_match('/\.php$/i', $file) && preg_match('/<\?.*?\?>(.*)/ims', $data, $strip)) {
                $data = ltrim($strip[1]);
            }
            if ($this->convertEncoding) {
                $data = iconv($this->inputEncoding, $this->outputEncoding, $data);
            }
            if (substr($data, -1) != "\n") {
                $data .= "\n";
            }
            $this->fileData = &$data;
            return true;
        }
        return false;
    }

    /**
     * Validate a row against specified conditions
     * @param   row          array with values from a row
     * @param   conditions   specified conditions that the row must match
     * @return  true of false
     */
    private function validateRowConditions($row = [], $conditions = null)
    {
        if (!empty($row)) {
            if (!empty($conditions)) {
                $conditions = (strpos($conditions, ' OR ') !== false) ? explode(' OR ', $conditions) : array($conditions);
                $or = '';
                foreach ($conditions as $key => $value) {
                    if (strpos($value, ' AND ') !== false) {
                        $value = explode(' AND ', $value);
                        $and = '';
                        foreach ($value as $k => $v) {
                            $and .= $this->validateRowCondition($row, $v);
                        }
                        $or .= (strpos($and, '0') !== false) ? '0' : '1';
                    } else {
                        $or .= $this->validateRowCondition($row, $value);
                    }
                }
                return (strpos($or, '1') !== false) ? true : false;
            }
            return true;
        }
        return false;
    }

    /**
     * Validate a row against a single condition
     * @param   row          array with values from a row
     * @param   condition   specified condition that the row must match
     * @return  true of false
     */
    private function validateRowCondition($row, $condition)
    {
        $operators = array(
            '=', 'equals', 'is',
            '!=', 'is not',
            '<', 'is less than',
            '>', 'is greater than',
            '<=', 'is less than or equals',
            '>=', 'is greater than or equals',
            'contains',
            'does not contain',
        );
        $operators_regex = [];
        foreach ($operators as $value) {
            $operators_regex[] = preg_quote($value, '/');
        }
        $operators_regex = implode('|', $operators_regex);
        if (preg_match('/^(.+) (' . $operators_regex . ') (.+)$/i', trim($condition), $capture)) {
            $field = $capture[1];
            $op = $capture[2];
            $value = $capture[3];
            if (preg_match('/^([\'\"]{1})(.*)([\'\"]{1})$/i', $value, $capture)) {
                if ($capture[1] == $capture[3]) {
                    $value = $capture[2];
                    $value = str_replace("\\n", "\n", $value);
                    $value = str_replace("\\r", "\r", $value);
                    $value = str_replace("\\t", "\t", $value);
                    $value = stripslashes($value);
                }
            }
            if (array_key_exists($field, $row)) {
                if (($op == '=' || $op == 'equals' || $op == 'is') && $row[$field] == $value) {
                    return '1';
                } elseif (($op == '!=' || $op == 'is not') && $row[$field] != $value) {
                    return '1';
                } elseif (($op == '<' || $op == 'is less than') && $row[$field] < $value) {
                    return '1';
                } elseif (($op == '>' || $op == 'is greater than') && $row[$field] > $value) {
                    return '1';
                } elseif (($op == '<=' || $op == 'is less than or equals') && $row[$field] <= $value) {
                    return '1';
                } elseif (($op == '>=' || $op == 'is greater than or equals') && $row[$field] >= $value) {
                    return '1';
                } elseif ($op == 'contains' && preg_match('/' . preg_quote($value, '/') . '/i', $row[$field])) {
                    return '1';
                } elseif ($op == 'does not contain' && !preg_match('/' . preg_quote($value, '/') . '/i', $row[$field])) {
                    return '1';
                } else {
                    return '0';
                }
            }
        }
        return '1';
    }

    /**
     * Validates if the row is within the offset or not if sorting is disabled
     * @param   current_row   the current row number being processed
     * @return  true of false
     */
    private function validateOffset($current_row)
    {
        if ($this->sortBy === null && $this->offset !== null && $current_row < $this->offset) {
            return false;
        }
        return true;
    }

    /**
     * Enclose values if needed
     *  - only used by unparse()
     * @param   value   string to process
     * @return  Processed value
     */
    private function encloseValue($value = null)
    {
        if ($value !== null && $value != '') {
            $delimiter = preg_quote($this->delimiter, '/');
            $enclosure = preg_quote($this->enclosure, '/');
            if (preg_match("/" . $delimiter . "|" . $enclosure . "|\n|\r/i", $value) || ($value{0} == ' ' || substr($value, -1) == ' ')) {
                $value = str_replace($this->enclosure, $this->enclosure . $this->enclosure, $value);
                $value = $this->enclosure . $value . $this->enclosure;
            }
        }
        return $value;
    }

    /**
     * Check file data
     * @param   file   local filename
     * @return  true or false
     */
    private function checkData($file = null)
    {
        if (empty($this->fileData)) {
            if ($file === null) {
                $file = $this->file;
            }
            return $this->loadData($file);
        }
        return true;
    }

    /**
     * Check if passed info might be delimiter
     *  - only used by find_delimiter()
     * @return  special string used for delimiter selection, or false
     */
    private function checkCount($char, $array, $depth, $preferred)
    {
        if ($depth == count($array)) {
            $first = null;
            $equal = null;
            $almost = false;
            foreach ($array as $key => $value) {
                if ($first == null) {
                    $first = $value;
                } elseif ($value == $first && $equal !== false) {
                    $equal = true;
                } elseif ($value == $first + 1 && $equal !== false) {
                    $equal = true;
                    $almost = true;
                } else {
                    $equal = false;
                }
            }
            if ($equal) {
                $match = ($almost) ? 2 : 1;
                $pref = strpos($preferred, $char);
                $pref = ($pref !== false) ? str_pad($pref, 3, '0', STR_PAD_LEFT) : '999';
                return $pref . $match . '.' . (99999 - str_pad($first, 5, '0', STR_PAD_LEFT));
            } else {
                return false;
            }
        }
    }

    /**
     * Read local file
     * @param   file   local filename
     * @return  Data from file, or false on failure
     */
    private function _rfile($file = null)
    {
        if (is_readable($file)) {
            if (!($fh = fopen($file, 'r'))) {
                return false;
            }
            $data = fread($fh, filesize($file));
            fclose($fh);
            return $data;
        }
        return false;
    }

    /**
     * Write to local file
     * @param   file     local filename
     * @param   string   data to write to file
     * @param   mode     fopen() mode
     * @param   lock     flock() mode
     * @return  true or false
     */
    private function _wfile($file, $string = '', $mode = 'wb', $lock = 2)
    {
        if ($fp = fopen($file, $mode)) {
            flock($fp, $lock);
            $re = fwrite($fp, $string);
            $re2 = fclose($fp);
            if ($re != false && $re2 != false) {
                return true;
            }
        }
        return false;
    }
}
