<?
/**
 * Forms constructor and manager.
 * Creates form from model or from given array of fields.
 * Collects ans shows validation errors.
 * Can create and use CSRF tokens.
 */
class Form
{
	//Class of model if form is created from model
	private $model_class;
	
	//Record id of model to load values from it
	private $record_id;
	
	//Errors of form after validation
	private $errors = [];
	
	//Mode when errors of form are displayed near inputs
	private $display_with_errors;
	
	//Use CSRF token in form validation
	private $csrf_token;

	//Use ajax CSRF token in form validation
	private static $ajax_csrf_token;
	
	//Use special javascript token in form validation
	private static $jquery_token;

	//List of security tokens to check in current form object
	private $used_tokens = [];
	
	//Fields of form (objects 0f data types) 
	public $fields;
	
	//Localization
	public $i18n;
	
	//Settings
	public $registry;
		
	public function __construct($form_source)
	{
		$this -> i18n = I18n :: instance();
		$this -> registry = Registry :: instance();
		$this -> used_tokens = ["regular" => false, "ajax" => false, "jquery" => false];

		if(!is_array($form_source) && class_exists($form_source))
		{			
			$model = new $form_source();
			
			if(get_parent_class($model) == "Model_Simple")
				Debug :: displayError("It's forbidden to create frontend forms from simple models.");
			
			$model -> loadRelatedData();
			$arguments = func_get_args();
			
			if(isset($arguments[1]) && is_numeric($arguments[1]))
				$this -> record_id = intval($arguments[1]); //To load record values into form
			
			$this -> fields = $model -> getElements(); //Pass model's fields objects
						
			foreach($this -> fields as $name => $field)
				if($field -> getType() == "many_to_one" || $field -> getType() == "group")
					unset($this -> fields[$name]);
			
			$this -> model_class = $form_source;
		}
		else if(is_array($form_source) && count($form_source))
		{
			foreach($form_source as $field_data)
				$this -> addField($field_data);
		}
		else
		{
			$message = "The form fields source was not passed. You need to pass the array of fields";
			$message .= " or class name of existed model from folder '~models/'.";
			Debug :: displayError($message);
		}
	}
	
	public function __call($method, $arguments)
	{
	    if($method == "displayFormErrors")
	        return $this -> displayErrors();
	    else if($method == "displayTokenCSFR")
	        return $this -> displayTokenCSRF();
	    else if($method == "checkTokenCSFR")
	        return $this -> checkTokenCSRF();
	    else
	        Debug :: displayError("Call to undefiend method '".$method."' of form object.");
	}
	
	public function setDisplayWithErrors()
	{
		$arguments = func_get_args();
		
		if(!isset($arguments[0]))
			$value = true;
		else 	
			$value = (bool) $arguments[0];
		
		$this -> display_with_errors = $value;
		
		return $this;
	}
	
	public function dropValues()
	{
		foreach($this -> fields as $object)
		{
			$object -> setValue(""); //Drops values and errors of all fields
			$object -> setError("");
		}
		
		$this -> errors = [];
		
		return $this;
	}
	
	public function getAllValues()
	{
		$arguments = func_get_args();
		$fields = (isset($arguments[0]) && is_array($arguments[0])) ? $arguments[0] : false;
		
		$values = [];
		
		foreach($this -> fields as $name => $object)
			if(!$fields || in_array($name, $fields))
			{
				if($object -> getType() == "file" && $object -> getProperty("multiple"))
					$values[$name] = $object -> getMultipleFilesPaths(); //Multiple files data
				else
					$values[$name] = $object -> getValue(); //Collects all form values	
			}
			
		return $values;
	}
	
	public function getDataFromArray($source)
	{
		$this -> dropValues(); //Removes all old values from form fields

		foreach($source as $name => $value)
			if(isset($this -> fields[$name]))
			{
				$type = $this -> fields[$name] -> getType(); //Type of current field
				
				if($type == "file" || $type == "image")
				{
					if($this -> record_id)
						$value = Service :: addFileRoot($value);
						
					$this -> fields[$name] -> setRealValue($value, basename($value));

					Filemanager :: deleteOldFiles($this -> registry -> getSetting("FilesPath")."tmp/");
				}	
				else
					$this -> fields[$name] -> setValue($value);
			}
				
		return $this;
	}
	
	public function getDataFromPost()
	{
		$this -> getDataFromArray($_POST); //Simple field data collection
		
		foreach($this -> fields as $name => $object) //Complex fields data processing
		{
			$type = $object -> getType();
			
			if($type == 'image' || $type == 'file')
			{
				if($type == "file" && $object -> getProperty("multiple")) //Multiple files processing
				{
					$this -> processMultipleFilesData($object);
					continue;
				}

				if(isset($_FILES[$name]) && !isset($_POST['value-'.$name]))
					$object -> setValue($_FILES[$name]);
				else if(isset($_POST['value-'.$name]) && $_POST['value-'.$name])
				{
					$data = explode("-*//*-", $_POST['value-'.$name]);
					
					if(is_file($data[1]))
						$object -> setRealValue($data[1], $data[0]);
				}
			}
			else if(($type == 'date' || $type == 'date_time') && $object -> getDisplaySelects())
			{
				foreach(array('dd', 'mm', 'yyyy', 'hh', 'mi') as $period)
					if(isset($_POST['select-'.$period.'-'.$name]) && is_numeric($_POST['select-'.$period.'-'.$name]))
						$object -> setSelectValue($period, $_POST['select-'.$period.'-'.$name]);
			}
			else if($type == 'many_to_many' && $object -> getProperty('display_table'))
				$object -> setValuesFromCheckboxes();
			else if($type == 'enum' && $object -> getProperty('multiple_choice'))
				$object -> setValuesFromCheckboxes();
		}
		
		return $this;
	}
		
	public function getValue($field)
	{
		if(isset($this -> fields[$field]))
			return $this -> fields[$field] -> getValue();
	}

	public function setValue($field, $value)
	{
		if(isset($this -> fields[$field]))
		{
			if($this -> fields[$field] -> getType() == "file" || $this -> fields[$field] -> getType() == "image")
				$this -> fields[$field] -> setRealValue($value, basename($value));
			else
				$this -> fields[$field] -> setValue($value);
		}
		
		return $this;
	}
	
	public function __get($key)
	{
		return $this -> getValue($key);
	}
	
	public function __set($key, $value)
	{
		return $this -> setValue($key, $value);	
	}
	
	public function getField($field)
	{
		if(isset($this -> fields[$field]))
			return $this -> fields[$field];
	}
	
	public function addField($field_data)
	{
		$field_data = Model :: checkElement($field_data);
		$forbidden_types = array("parent", "multi_images", "many_to_one", "group", "many_to_many");
		$extra_params = isset($field_data[3]) && is_array($field_data[3]) ? $field_data[3] : [];			
		$class_name = ucfirst($field_data[1])."_Model_Element";

		if(($field_data[1] == "file" || $field_data[1] == "image") && !$this -> model_class)
		{
			if(!isset($extra_params['files_folder']))
			{
				$message = "You must specify the folder for uploaded files for field '".$field_data[2]."' of form. ";
				$message .= "Put the name of folder in extra parameter like 'files_folder' => 'uploads'. ";
				$message .= "This folder will be created in folder 'userfiles'.";
				Debug :: displayError($message);
			}
		}
		else if(!$this -> model_class && in_array($field_data[1], $forbidden_types))
		{
			$message = "You can't use the field type '".$field_data[1]."' in form which is created ";
			$message .= "without model.";
			Debug :: displayError($message);
		}

		$this -> fields[$field_data[2]] = new $class_name($field_data[0], $field_data[1], $field_data[2], $extra_params);
		
		if($field_data[1] == "enum")
			$this -> fields[$field_data[2]] -> defineValuesList();
		
		return $this;
	}
	
	public function removeField($field)
	{
		if(isset($this -> fields[$field]))
			unset($this -> fields[$field]);
			
		return $this;
	}
	
	//Validation
	
	public function addRule($field, $rule, $value)
	{
		$fields = is_array($field) ? $field : ($field == "*" ? array_keys($this -> fields) : array($field));
		$arguments = func_get_args();
		$set_new_rule_value = true;
		
		if(!is_array($value) && strval($value) == "->")
			$set_new_rule_value = false;
			
		foreach($fields as $field_)
			if(isset($this -> fields[$field_]) && $this -> fields[$field_] -> hasProperty($rule))
			{
				if($rule == "format" && ($this -> fields[$field_] -> getType() == "date" || 
					$this -> fields[$field_] -> getType() == "date_time"))
					$value = true; //We can not change date format from here
					
				if($set_new_rule_value)
					$this -> fields[$field_] -> setProperty($rule, $value);
				
				if(isset($arguments[3]) && $arguments[3])
				{
					$custom_errors = $this -> fields[$field_] -> getProperty("custom_errors");
					$custom_errors[$rule] = $arguments[3];
					$this -> fields[$field_] -> setProperty("custom_errors", $custom_errors);
				}			
			}
		
		return $this;
	}
	
	public function removeRule($field, $rule)
	{
		$fields = is_array($field) ? $field : ($field == "*" ? array_keys($this -> fields) : array($field));
		
		foreach($fields as $field_)
			if(isset($this -> fields[$field_]) && $this -> fields[$field_] -> hasProperty($rule))
			{
				$this -> fields[$field_] -> setProperty($rule, false);
				
				$custom_errors = $this -> fields[$field_] -> getProperty("custom_errors");
				unset($custom_errors[$rule]);
				$this -> fields[$field_] -> setProperty("custom_errors", $custom_errors);
			}
		
		return $this;
	}
		
	public function validate()
	{
		$arguments = func_get_args();
		$fields = (isset($arguments[0]) && is_array($arguments[0])) ? $arguments[0] : false; //Passed fields to validate
		
		foreach($this -> fields as $name => $object)
			if(!$fields || in_array($name, $fields)) //If we should check this field
				if($object -> getType() == "multi_images" && isset($_FILES[$name]['name']) && $_FILES[$name]['name'])
				{
					$data = $object -> uploadImage($_FILES[$name], $this -> getValue($name));
					
					if($object -> getError())
						$this -> errors[] = array($object -> getCaption(), "{".$object -> getError()."}", $name);
					else
						$this -> fields[$name] -> setValue($data[1]);
				}
				else
				{
					if($object -> validate($this -> model_class, $this -> record_id) -> getError())
		            	$this -> errors[] = array($object -> getCaption(), $object -> getError(), $name);
		            
		            if($object -> getProperty("must_match") && !$object -> getError())
		            {
		            	$match_field = $this -> fields[$object -> getProperty("must_match")];
		            	
		            	if($object -> getValue() != $match_field -> getValue())
		            	{
		            		$error = $object -> chooseError("must_match", "{error-must-match}");
		            		$this -> errors[] = array($object -> getCaption(), $error, $name, $match_field -> getCaption());		            		
		            		
		            		if(!$object -> getError() && !$match_field -> getError())
		            			$object -> setError($error);
		            	}
		            }
				}
			

		if($this -> used_tokens["regular"] && !$this -> checkTokenCSRF())
			$this -> errors[] = ["", "{error-wrong-token}", "csrf_individual_token"];
    	else if($this -> used_tokens["ajax"] && !$this -> checkAjaxTokenCSRF())
		  	$this -> errors[] = ["", "{error-wrong-ajax-token}", "csrf_individual_token"];
    	else if($this -> used_tokens["jquery"] && !$this -> checkJqueryToken())
			$this -> errors[] = ["", "{error-wrong-ajax-token}", "csrf_individual_token"];

      	return (bool) count($this -> errors);
	}
	
	public function displayOneError($error)
	{
		$object = isset($error[2], $this -> fields[$error[2]]) ? $this -> fields[$error[2]] : false;
		  
		return Model :: processErrorText($error, $object);
	}
	
	public function displayErrors()
	{
		if($this -> model_class)
		{
			$model_object = new $this -> model_class();
			
			foreach($this -> fields as $name => $object)
				if(!$model_object -> getElement($name))
					$model_object -> passElement($object);
			
			foreach($this -> errors as $error)
				$model_object -> addError($error);
				
			return $model_object -> displayFormErrors();
		}
		else
		{
			if(!count($this -> errors)) return;
				
			$html = "<div class=\"form-errors\">\n";
			
			foreach($this -> errors as $error)
				$html .= "<p>".$this -> displayOneError($error)."</p>\n";
			
			return $html."</div>\n";
		}					
	}
	
	public function addError($error)
	{
		$arguments = func_get_args();
		
		if(isset($arguments[1], $this -> fields[$arguments[1]]))
		{
			$this -> fields[$arguments[1]] -> setError($error);
			$this -> errors[] = array($this -> fields[$arguments[1]] -> getCaption(), $error, $arguments[1]);
		}
		else
			$this -> errors[] = $error;
		
		return $this;
	}

	public function hasErrors()
	{
		return count($this -> errors);
	}
	
	public function isValid()
	{
		return count($this -> errors) ? false : true;
	}
	
	public function getErrors()
	{
		return $this -> errors;
	}
	
	public function display()
	{
		$arguments = func_get_args();
		$allowed_fields = (isset($arguments[0]) && is_array($arguments[0])) ? $arguments[0] : false;
		$vertical = (isset($arguments[1]) && $arguments[1] == "vertical");
		$fields = $this -> fields;
		
		if($allowed_fields)
		{
			$fields_ = $fields;
			$fields = [];
			
			foreach($allowed_fields as $name)
				if(array_key_exists($name, $fields_))
					$fields[] = $fields_[$name];
					
			unset($fields_);
		}
		
		$html = "";
		
		foreach($fields as $object)
		{
			$caption = $object -> getCaption();
			$type = $object -> getType();
			
			if($type == "many_to_one" || ($type == "many_to_many" && !$object -> getProperty("display_table")))
				continue;
			
			if($object -> getProperty('required'))
				$caption .= "&nbsp;<span class=\"required\">*</span>";
				
			$error_class = $object -> getError() ? " error-field" : "";
				
			if($vertical)
			{
				$html .= "<div class=\"field-name\">\n";
								
				if($type == "bool")
				{
					$bool_html = $object -> displayHtml();
					$bool_id = "form-bool-".$object -> getName();
					$bool_html = str_replace("/>", "id=\"".$bool_id."\" />", $bool_html);
					
					$html .= $bool_html." <label for=\"".$bool_id."\">".$caption."</label></div>\n";
					continue;
				} 
				
				$html .= $caption."</div>\n";
				$html .= "<div class=\"field-input".$error_class."\">\n";
			}
			else
			{
				$html .= "<tr>\n<td class=\"field-name\">\n".$caption."</td>\n";
				$html .= "<td class=\"field-input".$error_class."\">\n";			
			}
			
			if($type == "char" && $object -> getProperty('captcha'))
			{
				$src = $this -> registry -> getSetting("MainPath").$object -> getProperty('captcha');
				$html .= "<img src=\"".$src."\" alt=\"\" />\n";
			}

			if($type == "enum" && $object -> getProperty('multiple_choice'))
				$html .= $object -> displayAsCheckboxes();
			else if($type == "file" && $object -> getProperty('multiple'))
				$html .= $object -> displayMultipleHtml();
			else
				$html .= $object -> displayHtml("frontend");
				
			if($this -> display_with_errors && $object -> getError())
			{				
				$error = array($object -> getCaption(), $object -> getError(), $object -> getName());
				
				if($object -> getError() == "{error-must-match}")
					$error[3] = $this -> fields[$object -> getProperty("must_match")] -> getCaption();
				
				$error = $this -> displayOneError($error);
				$error = preg_replace("/\s+\./", ".", $error);				
				$html .= "<p class=\"field-error\">".$error."</p>\n";
			}			
			
			$html .= $vertical ? "</div>\n" : "</td>\n</tr>\n";
		}
		
		return $html;
	}
	
	public function displayVertical()
	{
		$argumnets = func_get_args();		
		$fields = (isset($argumnets[0]) && is_array($argumnets[0])) ? $argumnets[0] : false;
		$html = $this -> display($fields, "vertical");	
		
		return $html;
	}
	
	public function displayFieldHtml($name)
	{				
		if(isset($this -> fields[$name]))
		{
			$html = $this -> fields[$name] -> displayHtml("frontend");
			$type = $this -> fields[$name] -> getType();
			
			if($type == "char" && $this -> fields[$name] -> getProperty('captcha'))
			{
				$src = $this -> registry -> getSetting("MainPath").$this -> fields[$name] -> getProperty('captcha');
				$html = "<img src=\"".$src."\" alt=\"\" />\n".$html;
			}
			else if($type == "file" && $this -> fields[$name] -> getProperty('multiple'))
				$html = $this -> fields[$name] -> displayMultipleHtml();
			
			return $html;
		}
	}
	
	public function setRequiredFields($fields)
	{
		if(!$fields) return;
		
		if($fields == '*')
			$fields = array_keys($this -> fields);
		
		if(is_array($fields))
			foreach($this -> fields as $name => $object)
				$object -> setRequired(in_array($name, $fields));
				
		return $this;		
	}
	
	public function setCaption($field, $value)
	{
		if(isset($this -> fields[$field]))
			$this -> fields[$field] -> setCaption($value);

		return $this;
	}
	
	public function setRequired($field)
	{
		if(isset($this -> fields[$field]))
			$this -> fields[$field] -> setRequired(true);

		return $this;
	}	
	
	public function setHelpText($field, $value)
	{
		if(isset($this -> fields[$field]))
			$this -> fields[$field] -> setHelpText($value);

		return $this;
	}
	
	public function setHtmlParams($field, $value)
	{
		if(!is_array($field))
			$field = array($field);
			
		foreach($field as $key)
			if(isset($this -> fields[$key]))
				$this -> fields[$key] -> setHtmlParams($value);

		return $this;
	}
	
	public function setEnumEmptyValueTitle($field, $title)
   	{
   		if(isset($this -> fields[$field]) && $this -> fields[$field] -> getType() == "enum")
			$this -> fields[$field] -> setEmptyValueTitle($title);
				
		return $this;
   	}	
	
	public function composeMessage()
	{
		$argumnets = func_get_args();
		
		//Fields of form which will go into message
		$allowed_fields = (isset($argumnets[0]) && is_array($argumnets[0])) ? $argumnets[0] : array_keys($this -> fields);
		
		$message = "<ul>\n";
		
		foreach($allowed_fields as $name)
			if(isset($this -> fields[$name]))
			{
				$type =  $this -> fields[$name] -> getType();
				$caption = $this -> fields[$name] -> getCaption().": ";
				
				if($type == "bool")
				{
					$key = $this -> fields[$name] -> getValue() ? "yes" : "no";
					$message .= "<li>".$caption.I18n :: locale($key)."</li>\n";
				}
				else if($type == "many_to_many" && $this -> fields[$name] -> getValue())
				{
					$values = $this -> fields[$name] -> getDataForMessage($this -> fields[$name] -> getValue());
					$message .= "<li>".$caption.$values."</li>\n";
				}
				else if($type == "enum")
				{
					$values = $this -> fields[$name] -> getValuesList();
					
					if($this -> fields[$name] -> getValue())
						if($this -> fields[$name] -> getProperty("multiple_choice"))
						{
							$selected_titles = [];
							
							foreach(explode(",", $this -> fields[$name] -> getValue()) as $key)
								if(isset($values[$key]))
									$selected_titles[] = $values[$key];
										
							$message .= "<li>".$caption.implode(", ", $selected_titles)."</li>\n";
						}
						else if($this -> fields[$name] -> getProperty("long_list"))
							$message .= "<li>".$caption.$this -> getEnumTitle($name)."</li>\n";
						else if(isset($values[$this -> fields[$name] -> getValue()]))
							$message .= "<li>".$caption.$values[$this -> fields[$name] -> getValue()]."</li>\n";
				}
				else if(($type == "file" || $type == "image") && $this -> fields[$name] -> getValue())
				{
					$file_name = $this -> fields[$name] -> getValue();

					if($type == "file" && $this -> fields[$name] -> getProperty("multiple") && is_numeric($file_name))
					{
						$files = $this -> fields[$name] -> getMultipleFilesPaths();

						foreach($files as $file)
						{
							$file = Service :: addFileRoot($file);
							$this -> fields[$name] -> setRealValue($file, basename($file));

							$link = $this -> fields[$name] -> copyFile("", "no-model");
							$link = Service :: setFullHttpPath($link);

							$message .= "<li>".$caption."<a href=\"".$link."\" target=\"_blank\">".$link."</a></li>\n";
						}

						continue;
					}
					else if(is_file($file_name) && !preg_match("/tmp\/?$/", dirname($file_name)))
						$link = $file_name; //File already uploaded in proper folder
					else
					{
						$link = Service :: removeFileRoot($this -> fields[$name] -> getValue());
						
						if($this -> model_class) //If it's form from model
							$link = $this -> fields[$name] -> copyFile($this -> model_class);
						else
							$link = $this -> fields[$name] -> copyFile("", "no-model");
							
						$this -> fields[$name] -> setRealValue($link, basename($link));
					}

					$link = Service :: setFullHttpPath($link); //Full http link for file
					$message .= "<li>".$caption."<a href=\"".$link."\" target=\"_blank\">".$link."</a></li>\n";
				}				
				else if($this -> fields[$name] -> getValue())
					$message .= "<li>".$caption.$this -> fields[$name] -> getValue()."</li>\n";
			}
				
		return $message."</ul>\n";
	}
	
	public function getEnumTitle($field)
	{
		if(!isset($this -> fields[$field]) || $this -> fields[$field] -> getType() != "enum")
			return;
			
		return $this -> fields[$field] -> getValueName($this -> fields[$field] -> getValue());
	}
	
	public function loadRecord()
	{
		if(!$this -> record_id) return;
		
		$model_object = new $this -> model_class();
		$record = $model_object -> findRecordById($this -> record_id);
		
		if(!$record) return; //If record is not exists
		
		$source = $record -> getValues(); //Values of record from database
		$arguments = func_get_args();
		
		 //If we passed fielda which will be loaded into form
		if(isset($arguments[0]) && count($arguments[0]))
			foreach($source as $field => $value)
				if(!in_array($field, $arguments[0]))
					unset($source[$field]);
					
		foreach($source as $field => $value)
		{
			$object = $model_object -> getElement($field);
			
			if($object)
				if($object -> getType() == "password")
					unset($source[$field]);
				else if($object -> getType() == "date" || $object -> getType() == "date_time")
					$source[$field] = I18n :: formatDate($value);
		}

		$this -> getDataFromArray($source); //Passes data into form fields
		
		foreach($this -> fields as $object)
			$object -> cleanValue();
		
		return $this;
	}
	
	public function filterValuesList($field, $params)
	{
		if(isset($this -> fields[$field]) && count($params))
		{
			$type = $this -> fields[$field] -> getType();
			
			if($type == "enum" || $type == "many_to_many")
				$this -> fields[$field] -> filterValuesList($params);
		}			
		
		return $this;
	}
	
	//Fields settings
	
	public function setDisplaySelects($field)
	{
		if(isset($this -> fields[$field]))
		{
			$type = $this -> fields[$field] -> getType();
		
			if($type == "date" || $type == "date_time")
				$this -> fields[$field] -> setDisplaySelects(true);
		}
			
		return $this;
	}
	
	public function setDisplayRadio($field, $columns)
	{
		if(isset($this -> fields[$field]) && $this -> fields[$field] -> getType() == "enum" && $columns)
			$this -> fields[$field] -> setDisplayRadio(intval($columns));
		
		return $this;
	}
	
	public function setDisplayTable($field, $columns)
	{
		$columns = intval($columns);
		
		if(isset($this -> fields[$field]) && $columns)
			if($this -> fields[$field] -> getType() == "many_to_many")
				$this -> fields[$field] -> setDisplayTable($columns);
			else if($this -> fields[$field] -> getType() == "enum" && !$this -> model_class)					
				$this -> fields[$field] -> setProperty('multiple_choice', $columns);
			
		return $this;
	}
	
	public function getFieldProperty($field, $property)
	{
		if(isset($this -> fields[$field]))
			if($this -> fields[$field] -> hasProperty($property))
				return $this -> fields[$field] -> getProperty($property);
	}

	public function setFieldProperty($field, $property, $value)
	{
		if(isset($this -> fields[$field]))
			if($this -> fields[$field] -> hasProperty($property))
				$this -> fields[$field] -> setProperty($property, $value);
			
		return $this;
	}

	
	//Security tokens

	static public function getCreateCookieKeyToken()
	{
		if(isset($_COOKIE["key_token"]) && $_COOKIE["key_token"])
			return trim($_COOKIE["key_token"]);
		else
		{
			$registry = Registry :: instance();

			$key = Service :: strongRandomString(40);
			$http_only = $registry -> getSetting("HttpOnlyCookie");
			$time = time() + 3600 * 24 * 30;
			
			setcookie("key_token", $key, $time, $registry -> getSetting("MainPath"), "", Router :: isHttps(), $http_only);
			$_COOKIE["key_token"] = $key;

			return $key;
		}
	}


	//Ajax CSRF token
	
	public function useAjaxTokenCSRF()
	{
		self :: createAjaxTokenCSRF();
		$this -> used_tokens["ajax"] = true;
		
		return $this;
	}

	static public function createAjaxTokenCSRF()
	{
		if(!self :: $ajax_csrf_token)
		{
			$key = self :: getCreateCookieKeyToken();
			self :: $ajax_csrf_token =  Service :: createHash(Registry :: instance() -> getSetting("SecretCode").Debug :: browser().$key, "sha224");
		}
				
		return self :: $ajax_csrf_token;
	}

	static public function createAndDisplayAjaxTokenCSRF()
	{
		$token = self :: createAjaxTokenCSRF();
		
		return "<input type=\"hidden\" name=\"csrf_ajax_token\" value=\"".$token."\" />\n";
	}

	public function getAjaxTokenCSRF()
	{
	    return self :: $ajax_csrf_token;
	}

	public function checkAjaxTokenCSRF()
	{
	    return (isset($_POST["csrf_ajax_token"]) && $_POST["csrf_ajax_token"] == self :: $ajax_csrf_token);
	}


	//Regular CSRF token

	public function useTokenCSRF()
	{
		$token = isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : "";
		$token .= $_SERVER["REMOTE_ADDR"].$this -> model_class;
		
		if(Service :: sessionIsStarted())
		{
			if(!isset($_SESSION["csrf-individual-token"]))
				$_SESSION["csrf-individual-token"] = Service :: strongRandomString(50);
			
			$token .= $_SESSION["csrf-individual-token"];
		}

		$this -> csrf_token = Service :: createHash($token.$this -> registry -> getSetting("SecretCode"));
		$this -> used_tokens["regular"] = true;
		
		return $this;
	}
	
	public function displayTokenCSRF()
	{
		$html =  "<input type=\"hidden\" name=\"csrf_individual_token\" value=\"".$this -> csrf_token."\" />\n";
		
		if($this -> display_with_errors)
			foreach($this -> errors as $error)
				if($error[2] == "csrf_individual_token")
				{
					$error[1] = str_replace(array("{", "}"), "", $error[1]);
					$html .= "<p class=\"field-error\">".I18n :: locale($error[1])."</p>\n";
				}
		
		return $html;
	}

	public function getTokenCSRF()
	{
		return $this -> csrf_token;
	}
	
	public function checkTokenCSRF()
	{
	    return (isset($_POST["csrf_individual_token"]) && $_POST["csrf_individual_token"] == $this -> csrf_token);
	}


	//jQuery token

	public function useJqueryToken()
	{
		if(!self :: $jquery_token)
		    self :: $jquery_token = self :: createJqueryToken();

		$this -> used_tokens["jquery"] = true;
	    
	    return $this;
	}

	static public function createJqueryToken()
	{
		$key = self :: getCreateCookieKeyToken();
	    $token = Service :: createHash($key.Registry :: instance() -> getSetting("SecretCode").Debug :: browser(), "sha224");

	    return preg_replace("/\D/", "", $token);
	}
	
	static public function displayJqueryToken()
	{
		if(!self :: $jquery_token)
			return "";

	    $html = "\n<script type=\"text/javascript\"> $(document).ready(function(){";
	    $html .= "$(\"form\").append(\"<input type='hidden' name='jquery_check_code' ";
	    $html .= "value='".self :: $jquery_token."' />\")";
	    $html .= "}); </script>";
	    
	    return $html;
	}

	static public function createAndDisplayJqueryToken()
	{
		if(!self :: $jquery_token)
			self :: $jquery_token = self :: createJqueryToken();

		return self :: displayJqueryToken();
	}
	
	public function getJqueryToken()
	{
	    return self :: $jquery_token;
	}

	public function checkJqueryToken()
	{
	    return (isset($_POST["jquery_check_code"]) && $_POST["jquery_check_code"] == self :: $jquery_token);
	}

	//Multiple files input processing

	static public function getMultipleFilesData($field)
	{
		$files = [];
		
		if(isset($_FILES[$field]))
			foreach($_FILES[$field] as $section => $data)
				foreach($data as $key => $value)
					$files[$key][$section] = $value;

		foreach($files as $key => $data)
			if(!$data["name"] || !$data["type"] || !$data["size"])
				unset($files[$key]);

		return $files;
	}

	public function processMultipleFilesData($object)
	{
		$field = $object -> getName();
		$limit = (int) $object -> getProperty("multiple");
		$new_files = self :: getMultipleFilesData($field);
		$tmp_folder = $this -> registry -> getSetting('FilesPath')."tmp/";
		$old_files = [];

		foreach($_POST as $key => $value)
			if(preg_match("/^multiple-".$field."-\w+$/", $key))
			{
				$parts = explode("-", $key);
				$data = Service :: unserializeArray($value);

				if(md5($data["file"]) == $parts[2] && is_file($data["file"]))
					$old_files[] = $data;
			}

		if(count($old_files) <= $limit)
			$object -> setMultipleFiles($old_files);

		if(!count($new_files))
			return;

		$error = I18n :: locale("maximum-files-one-time", ["number" => $object -> getProperty("multiple")]);
		$max = $limit - count($old_files);

		if($max <= 0)
		{
			$object -> setError($error);
			return;
		}

		foreach($new_files as $file)
		{
			$object -> setValue($file);

			if(!$object -> getError())
				$object -> addMultipleFile($object);

			if(-- $max == 0)
				break;
		}

		if(count($new_files) + count($old_files) > $limit)
			$object -> setError($error);
	}
}
?>