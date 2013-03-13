<?php
class CsvListValue extends DataObjectSet {
	
	protected $fieldnames;
	protected $delimiter = ";";
	protected $name;
	
	function __construct($name, $fieldnames = null) {
		$this->name = $name;
		if(is_array($fieldnames)) {
			$this->fieldnames = $fieldnames;
		} else {
			$this->fieldnames = array_slice(func_get_args(), 1);
		}
	}
	
	function setValue($value) {
		if(is_array($value)) {
			$this->items = $value;
		} else {
			$this->items = $this->parseCsv($value, $this->fieldnames);
		}
	}
	
	
	function parseCsv($file, $field_names, $delimiter=";", $to_object=false) {
	    $delimiter = $delimiter;
	    $to_object = empty($options['to_object']) ? false : true;
	    $expr="/$delimiter(?=(?:[^\"]*\"[^\"]*\")*(?![^\"]*\"))/"; // added
	    $str = $file;
	    $lines = explode("\n", $str);
		$res = array();
	    foreach ($lines as $line) {
	        // Skip the empty line
	        if (empty($line)) continue;
	        $fields = preg_split($expr,trim($line)); // added
	        $fields = preg_replace("/^\"(.*)\"$/s","$1",$fields); //added
	        //$fields = explode($delimiter, $line);
	        $_res = $to_object ? new stdClass : array();
	        foreach ($field_names as $key => $f) {
	            if ($to_object) {
	                $_res->{$f} = $fields[$key];
	            } else {
	                $_res[$f] = $fields[$key];
	            }
	        }
	        $res[] = new ArrayData($_res);
	    }
	    return $res;
	}
	
	function serialize() {
		$result = "";
		foreach($this->items as $row) {
			$rowdata = array();
			foreach($this->fieldnames as $name) {
				if(is_array($row))
					$rowdata[] = $row[$name];
				else
					$rowdata[] = $row->$name;
			}
			$result .= "\"" . implode("\"{$this->delimiter}\"",array_values($rowdata)) . "\"\n";			
		}
		return $result;
	}
	
	function saveInto($dataObject) {
		$value = $this->serialize();
		$fieldName = $this->name;
		if($fieldName) {
			$dataObject->$fieldName = $value;
		} else {
			user_error("DBField::saveInto() Called on a nameless '" . get_class($this) . "' object", E_USER_ERROR);
		}

	}

	/**
	 * Returns the value to be set in the database to blank this field.
	 * Usually it's a choice between null, 0, and ''
	 */
	function nullValue() {
		return "null";
	}

	/**
	 * Return an encoding of the given value suitable
	 * for inclusion in a SQL statement. If necessary,
	 * this should include quotes.
	 * 
	 * @param $value mixed The value to check
	 * @return string The encoded value
	 */
	function prepValueForDB() {
		$value = $this->serialize();
		if($value === null || $value === "" || $value === false) {
			return "null";
		} else {
			return "'" . addslashes($value) . "'";
		}
	}	

	/**
	 * Prepare the current field for usage in a 
	 * database-manipulation (works on a manipulation reference).
	 * 
	 * Make value safe for insertion into
	 * a SQL SET statement by applying addslashes() - 
	 * can also be used to apply special SQL-commands
	 * to the raw value (e.g. for GIS functionality).
	 * {@see prepValueForDB}
	 * 
	 * @param array $manipulation
	 */
	function writeToManipulation(&$manipulation) {
		$manipulation['fields'][$this->name] = $this->hasValue() ? $this->prepValueForDB() : $this->nullValue();
	}

	/**
	 * Determines if the field has a value which
	 * is not considered to be 'null' in
	 * a database context.
	 * 
	 * @return boolean
	 */
	function hasValue() {
		return $this->items && count($this->items) > 0;
	}

}
?>