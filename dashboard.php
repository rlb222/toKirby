<?php 

include_once(PERCH_CORE.'/apps/content/PerchContent_Pages.class.php');
include_once(PERCH_CORE.'/apps/content/PerchContent_Page.class.php');
include_once(PERCH_CORE.'/apps/content/PerchContent_Regions.class.php');
include_once(PERCH_CORE.'/apps/content/PerchContent_Region.class.php');
include_once(PERCH_CORE.'/apps/content/PerchContent_Items.class.php');
include_once(PERCH_CORE.'/apps/content/PerchContent_Item.class.php');
include_once(PERCH_CORE.'/apps/content/PerchContent_NavGroup.class.php');
include_once(PERCH_CORE.'/apps/content/PerchContent_NavGroups.class.php');
include_once(PERCH_CORE.'/lib/PerchXMLTag.class.php');

// Objects to get the data
$Pages = new PerchContent_Pages;
$Regions = new PerchContent_Regions;
$Items = new PerchContent_Items;

// Arrays for the Kirby files
$Kirby_blueprints = array();
$Kirby_templates  = array();
$Kirby_contents   = array();


// Retrieve all pages 
$pages = $Pages->get_page_tree();

// include the Stylesheet for this dashboard app
include_once('dashboard_styles.php');
?>

<div class="workspace tokirby">
	<h1 class="tokirby">Creating an export from this Perch site</h1>
	<hr/>
	<p class="tokirby">
		This will extract the data from all Perch Pages: Regions, Blocks, and all fieldtypes: text, textarea, image, radio, checkbox.
		The data will be put in the /perch/kirby_sitename/ folder.
	</p>
	<p class="tokirby">
		The following is created
		<ul>
			<li>/perch/kirby_sitename/  - This is the root folder of this export.</li>
			<li>/perch/kirby_sitename/content/ folder, and within that folder for every page its own folder and content.txt (json) files.</li>
			<li>/perch/kirby_sitename/site/blueprints/</li>
			<li>for every page a .yml blueprint file in the blueprints folder.</li>
		</ul>
	</p>



<?php
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";  
$CurPageURL = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];  

echo '<form class="tokirby" action="'.$CurPageURL.'" method="post">';
echo '	<input class="tokirby" type="submit" name="startExport" value="Start the Export" />';
echo '</form>';
?>
</div><!-- end 'tokirby' workspace -->

<h1 class="tokirby">List of Pages and content</h1>
<p class="tokirby">
	All items are clickable to jump directly to the region in admin. 
	<ul>
		<li>Perch PageID and Subpage number / Title of the Page [ Filename ]</li>
		<li>Title of regionitem (field with title=true in template), Region Name, (region filename, repeated region or not)</li>
	</ul>

<?php

// 
// Loop through pages 
// 

if (PerchUtil::count($pages)) {
	foreach($pages as $Page) {

		// Remove first slash and extension
		$pageFileName = getCleanFileName($Page->pagePath());

		$Kirby_blueprints[$pageFileName]['filename']  = $pageFileName.'.yml';
		$Kirby_blueprints[$pageFileName]['pagetitle'] = PerchUtil::html($Page->pageNavText());
		$Kirby_blueprints[$pageFileName]['content']   = fill_start_blueprint(PerchUtil::html($Page->pageNavText()));

		$Kirby_templates [$pageFileName]['filename']  = $pageFileName.'.php';
		$Kirby_templates [$pageFileName]['pagetitle'] = PerchUtil::html($Page->pageNavText());
		$Kirby_templates [$pageFileName]['content']  = '';

		$Kirby_contents  [$pageFileName]['folder']    = $pageFileName;
		$Kirby_contents  [$pageFileName]['filename']  = $pageFileName.'.txt';
		$Kirby_contents  [$pageFileName]['pagetitle'] = PerchUtil::html($Page->pageNavText());
		$Kirby_contents  [$pageFileName]['content']  = 'Title: '
																										.PerchUtil::html($Page->pageNavText())
																										.PHP_EOL;

	
		//
		// Html for the list on screen
		//
		// Make html page string to echo on screen later
		$PageHtml  =  '<tr ><th colspan="2"><a class="pageName" href="'.PerchUtil::html(PERCH_LOGINPATH.'/core/apps/content/page/?id='.$Page->id()).'">';
		$PageHtml .=  $Page->pageTreePosition(). ' / '.PerchUtil::html($Page->pageNavText()).'  ['.$Page->pagePath().']</a></th></tr>';

		//
		// Start to fill the regions of this page
		//
		$regions = $Regions->get_for_page($Page->id(), $include_shared=true, $new_only=false, $template=false);
		
		// DEBUG
		// print_r($regions);

		if (PerchUtil::count($regions)) {
			$ItemsHtml = "";

			foreach($regions as $Region) {
				$thisTemplate = $Region->regionTemplate();
				$thisRegionName = $Region->regionKey();
				$cols = $Region->get_edit_columns();
				$items = $Items->get_for_Region($Region->regionID(), $Region->regionRev());
				// $items = $Items->get_for_Region($Region->regionID(), $this->regionLatestRev())
				// $items = $Items->get_for_region($Region->regionID(), $Region->regionLatestRev());

				// BLUEPRINT
				// If its a repeating region, add extra lines for this in the blueprint 
				if ($Region->regionMultiple() == 1 ){
					// aanvullen blueprint met code voor blocks
					$Kirby_blueprints[$pageFileName]['content'] .= add_block_to_blueprint($Region->regionKey(), $titleField);	
					$number_of_spaces = 8;	
				} else {
					$number_of_spaces = 0;
				}
					
				// Add each field to the page's blueprint
				$templateFields = get_field_definitions($Region->regionTemplate());
				
				// DEBUG
				// echo "<H1>RegionTemplate Name: ".$Region->regionTemplate()."</H1>";
				// echo "<pre>";
				// print_r($templateFields);
				// echo "</pre>";


				foreach($templateFields as $fieldName => $fieldValue) {
					$Kirby_blueprints[$pageFileName]['content'] .= 
						add_field_to_blueprint($fieldName, $fieldValue['type'], $number_of_spaces);
				}	
				// end BLUEPRINT
		
				// All items from the Region
				if (PerchUtil::count($items)) {

					// CONTENT Create the start of a repeating Region
					if ($Region->regionMultiple()==1)  {
						$Kirby_contents[$pageFileName]['content'] .=  addStartRepeatRegion($details, $thisTemplate, $thisRegionName);
					}

					foreach($items as $Item) {
						$details = PerchUtil::json_safe_decode($Item->itemJSON());

						// CONTENT Add normal region content
						if ($Region->regionMultiple()==0) {
							$Kirby_contents[$pageFileName]['content'] .= addRegionData($details, $thisTemplate);
						}
						// Add repeating region content
						if ($Region->regionMultiple()==1) {
							$Kirby_contents[$pageFileName]['content'] .= addRegionData($details, $thisTemplate, true, $thisRegionName);
						}
						// end CONTENT


						// Items on this Dashboard Page 
						$ItemsHtml .= '<tr><td><a class="dashboardItem" href="'.PerchUtil::html(PERCH_LOGINPATH)
									.'/core/apps/content/edit/?id='.PerchUtil::html($Region->id()).'&amp;itm='.PerchUtil::html($Item->itemId()).'">';
						if ($details->_title != "") {
							$ItemsHtml .= $details->_title;  // Make sure you have a template field which has the argument: title="true"
						} else {
							// There is no template field which has the argument: title="true", so display 'Item' and a warning
							// old text: (add title=true-field in template)
							$ItemsHtml .= "item <span class='special'>(no title=true in template for this item)</span>";
						}		
						$ItemsHtml 	.= '</a></td><td class="regionName">'.$Region->regionKey().' ('.$Region->regionTemplate() 
												.($Region->regionMultiple()==1 ? " Repeat": ""). ')</td>';
					}
				

				// CONTENT Create the end of a repeating Region 
				if ($Region->regionMultiple() == 1) {
					// last comma of the block should be removed again
					$Kirby_contents[$pageFileName]['content'] = substr($Kirby_contents[$pageFileName]['content'], 0, -2).PHP_EOL;
					// add the end of the block
					$Kirby_contents[$pageFileName]['content'] .= addEndRepeatRegion($details, $thisTemplate);
				}
			}	
		}		

		// Write the Page contents
		echo '<div class="no-widget"><div class="bd">';
		// Only echo the Page if there are showable items on it
		if ($ItemsHtml != "") echo '<table class="pageslist">'.$PageHtml.$ItemsHtml.'</table>';
		echo '</div></div>';

		
		// For the GO button
    if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['startExport']))
    {
			// Write content to Blueprint file
			write_my_file(
				PERCH_PATH.'/kirby_site_name/site/blueprints/pages/',
				$Kirby_blueprints[$pageFileName]['filename'], 
				$Kirby_blueprints[$pageFileName]['content']
			);
			// Write content to Content file
			write_my_file(
				PERCH_PATH.'/kirby_site_name/content/'.
				$Kirby_contents[$pageFileName]['folder'].'/',
				$Kirby_contents[$pageFileName]['filename'], 
				$Kirby_contents[$pageFileName]['content']
			);
		}

		// DEBUG
		// echo "<pre><h1>Page ".$pageFileName." </h1>";
		// print_r($Kirby_blueprints[$pageFileName]);
		// print_r($Kirby_templates);
		// print_r($Page);
		// print_r($Kirby_contents);
		// print_r($out);
		// echo "</pre>";

		}
	}
	// DEBUG
	echo "<pre><h1>$_POST</h1>";
	print_r($_POST);
	echo "</pre>";
	
	unset($_POST['startExport']);
	
	// DEBUG
	echo "<pre><h1>$_POST</h1>";
	print_r($_POST);
	echo "</pre>";

}
// echo "<pre><h1>Inhoud</h1>";

// $myContent = perch_content_custom('alinea', array(
// 	'template'=>'alinea.html',
// 	'page'=>'/index.php',
// 	'skip-template'=>true,
// 	'raw'=>true,
// 	'return-html'=>false,
// 	'html'=>false
// ));

// $opts = array(
// 		'template'=>'grotekop.html',
// 		'page'=>'/index.php',
// 		'skip-template'=>true,
// 		'raw'=>true,
// 		'return-html'=>false,
// 		'html'=>false
// );

// $Content = PerchContent::fetch();
// $out     = $Content->get_custom('Pagina Titel', $opts);


// echo "<pre><h1>Inhoud</h1>";
	// print_r($Kirby_blueprints);
	// print_r($Kirby_templates);
	// print_r($Page);
	// print_r($Kirby_contents);
	// print_r($out);
// echo "</pre>";
?>



<?php
// 
// ---  FUNCTIONS   ---
// 


// Extract only the name from the filename
function getCleanFileName($long_filename)
{
	$long_filename = substr_replace($long_filename, '', 0, 1);
	$long_filename = substr_replace($long_filename, '', -4, 4);
	return $long_filename;
}


// Get field definitions from the content template 
// of this page
function get_field_definitions($contentTemplate, $templateFieldName=false) {
	static $Perch_TemplateDefinition; 	// If the template has been parsed before, then get the info from this static field.


	// If the fields for this content template have not been loaded:
	if(!PerchUtil::count($Perch_TemplateDefinition[$contentTemplate])) {

		// Read the page region template from file						
		$regionTemplate = file_get_contents(PERCH_TEMPLATE_PATH.'/content/'.$contentTemplate);

		// TODO 
		//	- Error Handling for the file
		//  - '<!--* REMARK *-->' doesnt work: if <perch:content inside <!--* *--> it WILL be added
		//

		// store all perch tags into an array: $templateFields
		preg_match_all('\'<perch:content\\s(.*?)\\.*?/>\'', $regionTemplate, $templateFields, PREG_SET_ORDER);

		// Remove double field ids from array
		$string_of_fields = "";
		$myFieldArray = [];
		foreach($templateFields as $checkField) {
			// get the tag attributes for this field
			$field_tag_attributes = get_field_tag_attributes($checkField[1]);

			// check if field already exist and if not hidden
			if (!strpos($string_of_fields, $field_tag_attributes['id'])) { 
				// only the first perch:content id=<fieldID> will be added to the list 
				$string_of_fields .= $field_tag_attributes['id'].", "; // Add found field_id to 'stack'
				$myFieldArray = $field_tag_attributes;
			}
			$Perch_TemplateDefinition[$contentTemplate][$field_tag_attributes['id']] = $myFieldArray;
		}
	}
	// return the complete fieldlist for this template or only for one field if the field is listed, otherwise return 'false'
	if (!$templateFieldName){
			return $Perch_TemplateDefinition[$contentTemplate];
	} elseif (PerchUtil::count($Perch_TemplateDefinition[$contentTemplate][$templateFieldName])) {
			return $Perch_TemplateDefinition[$contentTemplate][$templateFieldName];
	} else return false;
}


function get_field_tag_attributes($fieldTagAttributes) {
	$FieldTag = new PerchXMLTag(' '.$fieldTagAttributes); // the extra space will include 'id=
	$attributes = $FieldTag->get_attributes();

	return $attributes;
}



function filter_raw_textarea($rawTextArea, $block) {
	// Replace markdown links into '(link: linkurl text: linktext)'
	$rawTextArea = preg_replace('/\[(.*?)\]\((.*?)\)/', "(link: $2 text: $1)", $rawTextArea);
	// Replace htlm links into '(link: linkurl text: linktext)'
	$rawTextArea = preg_replace('\'<a[^>]+href=\"(.*?)\"[^>]*>(.*?)</a>\'', "(link: $1 text: $2)", $rawTextArea);

	// Replace hard returns with \n
	if ($block) $rawTextArea = preg_replace("/[\n\r]/", '\n', $rawTextArea);

	return $rawTextArea;
}


//
// Creating the Content file
//

// Create the start of a repeating Region
function addStartRepeatRegion($data, $templateName, $regionName) {
	$content  = PHP_EOL . '----' . PHP_EOL . PHP_EOL;
	$content .= cleanFieldName($regionName).':'.PHP_EOL.PHP_EOL."[".PHP_EOL;
	return $content;
}

// Add Region Content
// This is where the CONTENT of Perch Fields from a Perch Region are transformed into Kirby Fields.
// The Perch content, Perchfieldtype and Perch field=definition is translated for Kirby here 
// for repeating regions and non repeating regions
function addRegionData($data, $TemplateName, $repeatRegion=false, $regionName='') {
	if ($repeatRegion) {
		$content = '    {' .PHP_EOL.'        "content": {'.PHP_EOL;
		$spaces = "            ";	
		$extraLines = PHP_EOL.PHP_EOL;
		$afterString = '';
		$afterString .= '        },' .PHP_EOL;
		//$afterString .= '        "id": "",'.PHP_EOL;
		$afterString .= '        "isHidden": false,'.PHP_EOL;
		$afterString .= '        "type": "'.strtolower(cleanFieldName($regionName)).'"'.PHP_EOL;
		$afterString .= '    },'.PHP_EOL;
	} else {
		$extraLines = '';
		$content = '';
		$afterString = '';
	}

	foreach($data as $fieldName => $myFieldValue) {

		$fieldType = get_field_definitions($TemplateName, $fieldName);
		// DEBUG
		// echo "<br/>>>>>>".$templateName."----".$fieldName."----<pre>";
		// print_r ($fieldType);
		// echo "</pre><<<<<".PHP_EOL."<br/>".PHP_EOL;	

		if (!empty($fieldType['type'])) {
			$typeOfField = $fieldType['type'];
		} else {
			$typeOfField = "skip_field";
		}

		// 
		// Field types content addition
		//
		switch ($typeOfField) {
			case "skip_field":
				// do nothing with this _id or _title (maybe others) field
				$thisFieldValue = "skip_this_field";
				break;
			case "text":
				$thisFieldValue = $myFieldValue;
				break;
			case "textarea":
				$thisFieldValue = filter_raw_textarea($myFieldValue->raw, $repeatRegion);
				break;
			case "image":
				// TODO: it's not ready 

				// echo "<pre>";
				// print_r($myFieldValue);
				// echo "</pre>";
				
				if (strlen($myFieldValue->path) > 0) { 
					$thisFieldValue = $myFieldValue->path;
				} else { 
					$thisFieldValue = ""; 
				}
				break;
			case "checkbox":
				// In Perch this is a one value field. So this becomes a one value Kirby checkbox.
				// In kirby a checkbox is possibly a multi-value field. Comma seprated in the content(.txt)  
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
				// TODO: Could be made into 2 fields: ->count and ->unit.
				// not present in Kirby, so it becomes a string: 'number<space>unit'
				// print_r($myFieldValue);
				$thisFieldValue = $myFieldValue->count." ".$myFieldValue->unit;
				break;	
			case "select":
				$thisFieldValue = $myFieldValue;
				break;	
			default:
				$thisFieldValue = ">" . $typeOfField . "<  is unsupported";
				break;
		}
		if ($thisFieldValue != "skip_this_field") { 
			if ($repeatRegion) 
			{
				if ($typeOfField == "image") {
					$content .= $spaces . '"' . cleanFieldName($fieldName) . '": ['.PHP_EOL.$spaces."    "
									 .'"'.$thisFieldValue.'"'.PHP_EOL.$spaces."]";
				} else {
					$content .= $spaces . '"' . cleanFieldName($fieldName) . '": "'.$thisFieldValue.'"';
				}
				$content .= ",".PHP_EOL;
			}else{ // not a repeat region
				if($typeOfField == "image") 
				{	
					$content .= $extraLines. '- '. $thisFieldValue ;
				} else {	
					$content .= PHP_EOL . '----' . PHP_EOL . PHP_EOL;
					$content .= cleanFieldName($fieldName) . ': '.$thisFieldValue. PHP_EOL;
				}
			}	
			// $content .= "SKIPPERD";
		}
	}
	if ($repeatRegion) {
		$content = rtrim($content,','.PHP_EOL);
		$content .= PHP_EOL;
	}
	$content .= $afterString;
	return $content;
}


// Create the end of a repeating Region 
function addEndRepeatRegion($data, $TemplateName) {
	$content = ']'.PHP_EOL;
	return $content;
}


// Field and Region names in Kirby cannot contain _underscores
function cleanFieldName($nameOfField){
	// underscores are not allowed in kirby fields:
	$nameOfField = str_replace("_", "-", $nameOfField);
	$nameOfField = str_replace(" ", "-", $nameOfField);
	return $nameOfField;
}



//
// Blueprint
//

// -- Fill the blueprint file
function fill_start_blueprint($pageTitle)
{
	$blueprint_lines  = 
	"title: " . $pageTitle.PHP_EOL.PHP_EOL.
	"columns:".PHP_EOL.
	" - width: 1/1".PHP_EOL.
	"   fields: " .PHP_EOL ;
	return $blueprint_lines;
}

function add_block_to_blueprint($blockName, $titleField)   {
	$blueprint_lines  = 
	'      ' . cleanFieldName($blockName) . ':'.PHP_EOL.
	'        type: blocks'.PHP_EOL.
	'        pretty: true'.PHP_EOL.
	'        label: " "'.PHP_EOL.
	'        fieldsets:'.PHP_EOL.
	'          '.strtolower($blockName).':'.PHP_EOL.
	'            name: ' . $blockName.PHP_EOL.
	'            preview: fields  '.PHP_EOL.
	'            wysiwyg: true'.PHP_EOL.
	'            label: "- {{' . $titleField .'}}" '.PHP_EOL.
	'            fields:'.PHP_EOL;
	return $blueprint_lines;
}

function add_field_to_blueprint($fieldname, $fieldtype, $spaces) {
	// Field types conversion 
	switch ($fieldtype) {
		case "image":
			$fieldtype = "files";
			break;
		case "checkbox":
			$fieldtype = "checkboxes";
			break;
		case "period":
			$fieldtype = "text";
			break;
	}

	$preSpaces = str_repeat(" ", $spaces);
	$blueprint_lines  = 
	$preSpaces . '      ' . cleanFieldName($fieldname) . ':' .PHP_EOL.
	$preSpaces . '        type: '.$fieldtype .PHP_EOL;
	return $blueprint_lines;
}

//
// Template
//

// NOT IMPLEMENTED Perch Template to Kirby Template conversion 

function fill_start_template($page)
{
	return $template_lines;
}

function add_block_to_template($page)
{
	return $template_lines;
}

function add_field_to_template($page)
{
	return $template_lines;
}


//
// Write files to system
//

function write_my_file($filepath, $filename, $somecontent)
{

	// Add folder if it doesnt exist 
	if (!is_dir($filepath)) {
		if (!mkdir($filepath, 0777, true) ) {
			echo('Failed to create directories' . $filepath);
		}
	}

	$filename = $filepath . $filename;

	if (!is_writable($filename)) {
		$handle = fopen($filename, 'w');
	}

	// Let's make sure the file exists and is writable first.
	if (is_writable($filename)) {
	
			// In our example we're opening $filename in append mode.
			// The file pointer is at the bottom of the file hence
			// that's where $somecontent will go when we fwrite() it.
			if (!$handle = fopen($filename, 'w')) {
					 echo "Cannot open file ($filename)";
					 exit;
			}
	
			// Write $somecontent to our opened file.
			if (fwrite($handle, $somecontent) === FALSE) {
					echo "Cannot write to file ($filename)";
					exit;
			}
	
			//echo "Success";//, wrote ($somecontent) to file ($filename)";
	
			fclose($handle);
	
	} else {
			echo "The file $filename is not writable";
	}

}