<?php
if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/* Andrew Meyer's Form Element Builder
 * You. Are. Going. To. Love. It!
 *
 * NOTE
 * ---------------------------------------------------------------
 * This helper only helps build the actual form elements, not the 
 * opening/closing tags and all that stuff. For those, just use CI's
 * default form helper. This is auxiliary.
 *
 * TO DO
 * ---------------------------------------------------------------
 * Revisit the final $options variable/array in the element function.
 * There might be a better way to do this. It is used only minimally
 * for putting breaks after checkboxes/radio buttons.
 */
 
// PUBLIC FUNCTIONS

// function to build form elements "the magic"
if (! function_exists("form_item")) {
	function form_item($type="text", $name=NULL, $label=NULL, $more=array())
	{
		// process name
		// if $name is not set, use field type
		$item_name = isset($name) ? $name : $type;
		
		// process label
		// if $label is set, build html string; if not, use empty.
		$item_label = isset($label) ? _labelFor($item_name, $label) : "";
		
		// pull out options from $more array
		// check for options
		if (!empty($more['options'])) {
			$options = $more['options'];
			$selected_option = isset($more['selected']) ? $more['selected'] : '';
		} else {
			$options = "";
			$selected_option = "";
		}
		
		// check for html attributes
		if (!empty($more['attributes'])) {
			$attributes = $more['attributes'];
			if (is_object($attributes)) {
				$attributes = (array)$more['attributes'];
			}
		} else {
			$attributes = "";
		}
		
		// check for value
		if (isset($more['value'])) {
			$input_value = $more['value'];
		} else {
			$input_value = "";
		}
		
		// check for help text
		if (isset($more['help_text'])) {
			$item_help = $more['help_text'];
		} else {
			$item_help = "";
		}
		
		// build input html string based on type
		// start with empty var
		$item_input="";
		
		// iterate through types to build what is called for
		switch($type) :
			case "text" :
				if (!isset($attributes['type'])) {
					$attributes['type'] = "text";
				}
				if (!isset($attributes['name'])) {
					$attributes['name'] = $item_name;
				}
				if (!isset($attributes['id'])) {
					$attributes['id'] = $item_name;
				}
				if (!isset($attributes['class'])) {
					$attributes['class'] = "text";
				}
				if (!isset($attributes['value'])) {
					$attributes['value'] = $input_value;
				}
				
				$item_input='<input '._makeAttrString($attributes).' />';
				
				break;
			
			case "textarea" :
				if (!isset($attributes['name'])) {
					$attributes['name'] = $item_name;
				}
				if (!isset($attributes['id'])) {
					$attributes['id'] = $item_name;
				}
				
				$item_input='<textarea '._makeAttrString($attributes).'>'.$input_value.'</textarea>';
				
				break;
			
			case "select" :
			case "multiselect" :
				if (!isset($attributes['name'])) {
					$attributes['name'] = $item_name;
				}
				if (!isset($attributes['id'])) {
					$attributes['id'] = $item_name;
				}
				if (!isset($attributes['multiple']) && $type=="multiselect") {
					$attributes['multiple'] = "multiple";
					if (!isset($attributes['class'])) {
						$attributes['class'] = "multi";
					}
				}
				
				$item_input = '<select '._makeAttrString($attributes).'>'._makeOptionsString($options, $selected_option).'</select>';
				
				break;
				
			case "radio" :
			case "checkbox" :
				if (!isset($attributes['name'])) {
					$attributes['name'] = $item_name.'[]';
				}
				if (!isset($attributes['type'])) {
					$attributes['type'] = $type;
				}
				
				$item_input=_makeCheckRadiosString($options, $attributes, $selected_option, true);
				
				break;
			// last option, close out.
		endswitch;
		
		// return the item
		
		return _buildItem($item_label, $item_input, $item_help);
	}
}

//Function to create button. Largely called by other functions.
function button($name='button',$value=NULL,$attributes=array(),$type="button") {
	$btn_attr_string = (!empty($type) ? ' type="'.$type.'"' : '');
	$btn_attr_string .= (!empty($name) ? ' name="'.$name.'"' : '');
	$btn_attr_string .= (!empty($value) ? ' value="'.$value.'"' : ' value="'.$name.'"');
	if (!empty($attributes)) {
		while(list($attr, $val) = each($attributes)) {
			if ($attr!='class') {
				$btn_attr_string.=' '.$attr.'="'.$val.'"';
			}
		}
	}
	$btn_attr_string .= (!empty($attributes['class']) ? ' class="'.$attributes['class'].'"' : 'class="button"');
	$input='<input'.$btn_attr_string.' />';
	
	return $input;
}


// (PRIVATE) UTILITY FUNCTIONS

// generates label
if (! function_exists("_lableFor")) {
	function _labelFor($name="", $label="")
	{
		if (!empty($name)) {
			if (empty($label)) { $label=$name; }
			$label_text='<label for="'.$name.'" class="input_heading">'.$label.'</label>';
		
			return $label_text;
		} else {
			return "";
		}
	}
}

// generates help text
if (! function_exists("_helpText")) {
	function _helpText($help_string)
	{
		$help_open = '<div class="help_text">';
		$help_close = '</div>';
		$output = $help_open . $help_string . $help_close;
		
		return $output;
	}
}

// makes string of html attributes from key value pairs
if (! function_exists("_makeAttrString")) {
	function _makeAttrString($attributes)
	{
		if (is_string($attributes) AND strlen($attributes) > 0)
		{
			$output = ' '.$attributes;
			
			return $output;
		}
		
		if (is_object($attributes) AND count($attributes) > 0)
		{
			$attributes = (array)$attributes;
		}

		if (is_array($attributes) AND count($attributes) > 0)
		{
			$output = '';
	
			foreach ($attributes as $key => $val)
			{
				$output .= ' '.$key.'="'.$val.'"';
			}
			return $output;
		}
	}
}

if (! function_exists("_makeOptionsString")) {
	function _makeOptionsString($options, $selected="")
	{	
		if (is_object($options) AND count($options) > 0)
		{
			$options = (array)$options;
		}

		if (is_array($options) AND count($options) > 0)
		{
			$output = '';
	
			foreach ($options as $key => $val)
			{
				// if selected is set...
				if ($selected != "") {
					// set selected string to proper value if it matches the $key
					$sel_string=($key==$selected) ? ' selected="selected"' : '';
				} else {
					// nothing selected
					$sel_string="";
				}
				
				$output .= '<option value="'.$key.'"'.$sel_string.'>'.$val.'</option>'."\r\n";
			}
			
			return $output;
		}
	}
}

if (! function_exists("_makeCheckRadiosString")) {
	function _makeCheckRadiosString($options, $attributes, $selected="", $break=false)
	{	
		if (is_object($options) AND count($options) > 0)
		{
			$options = (array)$options;
		}

		if (is_array($options) AND count($options) > 0)
		{
			$output = '';
			
			$attr_string = _makeAttrString($attributes);
			
			foreach ($options as $key => $val)
			{
				// if selected is set...
				if ($selected != "") {
					// set selected string to proper value if it matches the $key
					$sel_string=(strpos($selected, $key)>=0) ? ' checked="checked"' : '';
				} else {
					// nothing selected
					$sel_string="";
				}
				
				if ($break) {
					$break_string = "<br />";	
				} else {
					$break_string = "";	
				}
				
				$output .= '<label><input '.$attr_string.' value="'.$key.'"'.$sel_string.' /> '.$val.'</label>'.$break_string."\r\n";
			}
			
			return $output;
		}
	}
}

// puts everything together
if (! function_exists('_buildItem')) {
	function _buildItem($label_string="", $input_string="", $help_text="") {
		// item wrapper parts
		$wrap_before = '<div class="form_item">';
		$wrap_after = '</div>';
		
		// input wrapper parts
		$wrap_input_before = '<div class="input_holder">';
		$wrap_input_after = '</div>';
		
		// if help string exists, wrap it, and roll it.
		$help_string = ($help_text!="") ? _helpText($help_text) : '';
		
		// biuld it
		$output = $wrap_before . $label_string . $wrap_input_before . $input_string . $help_string . $wrap_input_after . $wrap_after;
		
		return $output;
	}
}