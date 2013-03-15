<?php
class CsvListField extends TextField {

	protected $template = "CsvListField";
	protected $fieldset;
	protected $fieldnames;
	protected $fieldnames_combined;
	protected $delimiter = ";";
	protected $enclosure = '"';
	protected $escape = "\\";

	function __construct($name, $title = null, $fields = null, $value = "", $custom_template = null ){
		parent::__construct($name, $title, $value);

		if($fields == null) {
			user_error("CsvListField: No field definition given.", E_USER_ERROR);
		} else {
			//Must be fieldset or array
			$this->fieldset = new FieldList();
			$this->fieldnames = array();
			foreach($fields as $field) {
				$this->fieldnames[] = $field->getName();
				$combinedname = $this->getName()."[%Pos][".$field->getName()."]";
				$this->fieldnames_combined[] = $combinedname;
				//$field->setName($combinedname);
				$this->fieldset->push($field);
			}
		}

		//Set value again to convert
		$this->setValue($value);

		if($custom_template != null)
			$this->template = $custom_template;
	}
	public function setForm($form) {
		$this->fieldset->setForm($form);

	}

	function extraClass() {
		return parent::extraClass() . " csvlistfield";
	}

	function dataValue() {
		//to csv
		$result = "";
		foreach($this->value as $row) {
			$csvColumns = array_values($row);
			$result .= "\"" . implode("\"{$this->delimiter}\"",array_values($csvColumns)) . "\"\n";
		}
		return $result;
	}

	function setValue($value) {
		if(is_array($value)) {
			//from form
			$this->value = $this->fromForm($value);
		} else {
			//from field
			$this->value = $this->parseCsv($value, $this->fieldnames, $this->delimiter);
		}
	}

        function fromForm($value) {
            $result = array();
            foreach($value as $row) {
                $item = array();
                $count = 0;
                foreach($this->fieldset as $field) {
			if(array_key_exists($field->id(), $row)) { //Strange case when editing.
				$field->setValue($row[$field->id()]);
			} else {
				$field->setValue($row[$field->getName()]);
			}

                    $item[$field->getName()] = $field->Value();
                    $count++;
                }
                $result[] = $item;
            }
            return $result;
        }

	function NewFields() {
		$fields = new ArrayList();
		foreach($this->fieldset as $field) {
			$newfield = clone $field;
			$newfield->setForm($this->form);
			$fields->push($newfield);
		}
		return $fields;
	}

	function FieldsForForm() {
		$result = new ArrayList();
		if(!$this->value) {
			$pos = 1;
			$fields = new FieldList();
			foreach($this->fieldset as $field) {
				$newfield = clone $field;
				$combinedname = $this->getName()."[$pos][".$field->getName()."]";
				$newfield->setName($combinedname);
				$newfield->setForm($this->form);
				$fields->push($newfield);
			}
			$result->push(new ArrayData(array(
				"Fields" => $fields
			)));
			return $result;
		}
		$pos = 0;
		foreach($this->value as $value) {
			$fields = new FieldList();
			foreach($this->fieldset as $field) {
				$newfield = clone $field;
				$newfield->setValue($value[$field->name]);
				$combinedname = $this->Name."[$pos][".$field->Name."]";
				$newfield->setName($combinedname);
				$newfield->setForm($this->form);
				$fields->push($newfield);
			}
			$result->push(new ArrayData(array(
				"Fields" => $fields
			)));
			$pos++;
		}
		return $result;
	}

	function LineFieldSet() {
		return $this->fieldset;
	}

	public function Field($properties = array()) {
		//print_r($this->dataValue());
		return $this->renderWith($this->template);
	}

	public function FieldHolder($properties = array()) {
		$holder = parent::FieldHolder();
		return $holder;
	}

	function parseCsv($file, $field_names, $delimiter, $to_object=false) {
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
	        $res[] = $_res;
	    }
	    return $res;
	}
}
?>
