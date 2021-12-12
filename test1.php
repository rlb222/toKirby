// Add repeating region content
function addRepeatRegionData($data, $TemplateName, $regionName) {
	$content = '    {' .PHP_EOL.
						 '        "content": {'.PHP_EOL;
	$spaces = "            ";	


	foreach($data as $fieldName => $myFieldValue) {

		$fieldType = get_field_definitions($templateName, $fieldName);	

		if (!empty($fieldType['type'])) {
			$typeOfField = $fieldType['type'];
		} else {
			$typeOfField = "unknown";
		}

		switch ($typeOfField) {
			case "_id":
				// do nothing with this field
				break;
			case "_title":
				// do nothing with this field
				break;
			case "text":
				$thisFieldValue = $myFieldValue;
				break;
			case "textarea":
				$thisFieldValue = filter_raw_textarea($myFieldValue->raw);
				break;
			case "image":
				// echo "<pre>";
				// print_r($myFieldValue);
				// echo "</pre>";
				if (strlen($myFieldValue->path) > 0) { 
					$thisFieldValue = PHP_EOL.PHP_EOL. '- '. $myFieldValue->path;
				} else { $thisFieldValue = ""; }
				break;
			case "checkbox":
				// In Perch this is a one value field. So this becomes a one value Kirby checkbox.
				// in kirby checkbox is possibly a multi-value field. Comma seprated in the content(.txt)  
				$thisFieldValue = $myFieldValue;
				break;	
			case "radio":
				$thisFieldValue = $myFieldValue;
				break;	
			case "date":
				// echo "<pre>";
				// print_r($myFieldValue);
				// echo "</pre>";
				$thisFieldValue = $myFieldValue;
				break;	
			case "period":
				// Not working yet
				// not present in Kirby, so string
				// $thisFieldValue = $myFieldValue;
				break;	
			case "select":
				break;	
			default:
				$thisFieldValue = $typeOfField . " is unsupported ";
				break;
		}
		if ($fieldName[0] != "_") {     // _id field and _title field are not put into 'content.txt'
			$content .= $spaces . '"' . cleanFieldName($fieldName) . '": "'.$thisFieldValue.'",'.PHP_EOL;
		}
	}
	$content .= '        },' .PHP_EOL;
	$content .= '        "id": "",'.PHP_EOL;
	$content .= '        "isHidden": false,'.PHP_EOL;
	$content .= '        "type": "'.strtolower(cleanFieldName($regionName)).'"'.PHP_EOL;
	$content .= '    },'.PHP_EOL;

	return $content;
}