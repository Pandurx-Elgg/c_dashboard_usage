<?php

// load library for the graphs and charts (vendor)
elgg_load_library('jgraph');
elgg_load_library('jgraph_pie');
elgg_load_library('jgraph_bar');
elgg_load_css('collap_tree');

// colours
$pie_colours_var = array('#800000','#339933','#99ff66','#ff6699','#0099ff','#002e4d','#990099', '#003300');

// get all site guid (since there are subsites)
$query = 'SELECT DISTINCT site_guid FROM elgg_entities';
$site_guid_list_results = get_data($query);

$site_guid_list = array();
foreach ($site_guid_list_results as $result)
	$site_guid_list[] = (int)$result->site_guid;


// get all files
$elgg_files = elgg_get_entities(array(
	'subtype' => 'file',
	'type' => 'object',
	'site_guid' => $site_guid_list,
));

?>


<hr>

<?php

$elgg_files = elgg_get_entities(array(
	'subtype' => 'file',
	'type' => 'object',
	'site_guid' => $site_guid_list,
));
$space_used = 0;
$num_of_file = 0;
$total_space_allocated = disk_total_space(elgg_get_data_path());
foreach ($elgg_files as $elgg_file){
	$num_of_file++;
	$space_used = (int)$space_used + (int)$elgg_file->size();
}
$total_space_avail = $total_space_allocated - $space_used;

$space_used = $space_used;
$total_space_avail = $total_space_avail;


// 1- make sure it has permission to save pie graph in directory
// 2- make sure not to make pie charf if no file issue #5 (but display 0 anyways textually)
if ($num_of_file > 0) {
	// CREATE PIE CHART FOR VISUAL

	//$data = array($pleio_main_size,$subsites_size,$groups_size,$users_size);
	$data = array($space_used, $total_space_avail);
	// Create the Pie Graph. 
	$graph = new PieGraph(350,250);

	$theme_class="DefaultTheme";
	//$graph->SetTheme(new $theme_class());

	// Set A title for the plot
	$graph->title->Set("Total Disk Space Usage");
	$graph->SetBox(true);

	// Create
	$p1 = new PiePlot($data);
	$graph->Add($p1);

	$p1->ShowBorder();
	$p1->SetColor('black');
	$p1->SetSliceColors(array('#800000','#339933'));

	$fp = elgg_get_plugins_path()."c_dashboard_usage/img/pie_chart.png";
	$file = $graph->Stroke($fp);
	echo '<img src="'.elgg_get_site_url().'mod/c_dashboard_usage/img/pie_chart.png">';
} else {
	echo 'This site has no files uploaded!<br/>';
}

echo '<br/>Total Space Available: '.$total_space_avail.' bytes <br/> Total Disk Space Used: '.$space_used.' bytes <br/> Total Space Allocated: '.$total_space_allocated.' bytes <br/> Number of Files: '.$num_of_file;


// get all site guid (since there are subsites)
$query = 'SELECT * FROM elgg_sites_entity';
$elgg_sites = get_data($query);

$query = 'SELECT * FROM elgg_groups_entity';
$elgg_groups = get_data($query);

$query = 'SELECT * FROM elgg_users_entity';
$elgg_users = get_data($query);

$site_entities = array("Sites","Groups","Users");
echo "<hr>";


// SITES AND SUBSITES
echo $site_entities[0];
echo "<hr>";




echo '<ol class="tree">';

foreach ($elgg_sites as $elgg_site) {
	echo '<li>';
	//echo "..<strong>".(int)$elgg_site->guid." - ".$elgg_site->name."</strong><br/>";
	echo '<label>'."..<strong>".(int)$elgg_site->guid." - ".$elgg_site->name."</strong>".'</label><input type="checkbox" id="folder1" />';
	$ext_count = array();
	// get all files
	$elgg_files = elgg_get_entities(array(
		'subtype' => 'file',
		'type' => 'object',
		'site_guid' => $elgg_site->guid,
	));

	echo '<ol>';
	
	foreach ($elgg_files as $elgg_file) {
		$url = elgg_add_action_tokens_to_url(elgg_get_site_url().'action/file/delete?guid='.$elgg_file->getGUID());
		$ext = pathinfo($elgg_file->getFilename(), PATHINFO_EXTENSION);
		//echo '......'.$elgg_file->getFilename()." | ".$elgg_file->size()." bytes"." <a href='".$url."'> X </a><br/>"; // make sure after deletion, forward url back to this admin page...
		//echo '..........'.$ext.' | '.$elgg_file->getOwnerEntity()->username." | <a href='".$elgg_file->getURL()."'>File Location</a> | ".$elgg_file->getTimeUpdated()."<br/>"; // time is displayed in epoch time
		echo '<li>';
		echo '<label for="subsubfolder2">'.'......'.$elgg_file->getFilename()." | ".$elgg_file->size()." bytes"." <a href='".$url."'> X </a>".'</label> <input type="checkbox" id="subsubfolder2" />';
			echo '<ol>';
				echo '<li class="file">'.'..........'.$ext.' | '.$elgg_file->getOwnerEntity()->username." | <a href='".$elgg_file->getURL()."'>File Location</a> | ".$elgg_file->getTimeUpdated().'</li>';
			echo '</ol>';
		echo '</li>';
		
		if (!$ext_count[$ext])
			$ext_count[$ext] = 1;
		else
			$ext_count[$ext]++;
	}

	
	echo '</ol>';


	// CREATE PIE CHART FOR VISUAL
	//$data = array($pleio_main_size,$subsites_size,$groups_size,$users_size);
	//$data = array($space_used, $total_space_avail);
	$ext_name = array();
	$data = array();
	foreach ($ext_count as $extension => $total_ext) {
		$ext_name[] = $extension."\n%.1f%%";
		$data[] = $total_ext;
	}


	if ($data) {
		// Create the Pie Graph. 
		$graph = new PieGraph(350,250);

		$theme_class="DefaultTheme";
		//$graph->SetTheme(new $theme_class());

		// Set A title for the plot
		$graph->title->Set("");
		$graph->SetBox(true);

		// Create
		$p1 = new PiePlot($data);
		$p1->SetLabelType(PIE_VALUE_PER);
		$p_lbl = $ext_name;
		$p1->SetLabels($p_lbl);
		$graph->Add($p1);

		$p1->ShowBorder();
		$p1->SetColor('black');
		$p1->SetSliceColors(array('#800000','#339933','#99ff66','#ff6699','#0099ff'));

		$fp = elgg_get_plugins_path()."c_dashboard_usage/img/pie_chart-".$num_pie.".png";
		$file = $graph->Stroke($fp);
		echo '<br/><img src="'.elgg_get_site_url().'mod/c_dashboard_usage/img/pie_chart-'.$num_pie.'.png"><br/>';
		$num_pie++;
	} else {
		echo '<center><strong><p>No Data Available</p></strong></center>';
	}

	echo '</li>';

} // end loop for each site

echo '</ol>';


echo "<hr>";




// GROUPS
echo $site_entities[1];
echo "<hr>";
foreach ($elgg_groups as $elgg_group) {
	echo "..<strong>".(int)$elgg_group->guid." - ".$elgg_group->name."</strong><br/>";
	$ext_count = array();
	// get all files
	$elgg_files = elgg_get_entities(array(
		'subtype' => 'file',
		'type' => 'object',
		'container_guids' => array($elgg_group->guid),
	));

	foreach ($elgg_files as $elgg_file) {
		$url = elgg_add_action_tokens_to_url(elgg_get_site_url().'action/file/delete?guid='.$elgg_file->getGUID());
		$ext = pathinfo($elgg_file->getFilename(), PATHINFO_EXTENSION);
		if (($elgg_file->getContainerEntity()->getType() === 'group') && ($elgg_file->getContainerEntity()->getGUID() == (int)$elgg_group->guid)) {
			echo '......'.$elgg_file->getFilename()." | ".$elgg_file->size()." bytes"." <a href='".$url."'> X </a><br/>";
			echo '..........'.$elgg_file->getOwnerEntity()->username." | <a href='".$elgg_file->getURL()."'>File Location</a> | ".$elgg_file->getTimeUpdated()." | ".$elgg_file->getContainerEntity()->name."<br/>";
		
			if (!$ext_count[$ext])
				$ext_count[$ext] = 1;
			else
				$ext_count[$ext]++;
		}
	}

	$ext_name = array();
	$data = array();
	foreach ($ext_count as $extension => $total_ext) {
		$ext_name[] = $extension."\n%.1f%%";
		$data[] = $total_ext;
	}

	if ($data) {
		// Create the Pie Graph. 
		$graph = new PieGraph(350,250);

		$theme_class="DefaultTheme";
		//$graph->SetTheme(new $theme_class());

		// Set A title for the plot
		$graph->title->Set("");
		$graph->SetBox(true);

		// Create
		$p1 = new PiePlot($data);
		$p1->SetLabelType(PIE_VALUE_PER);
		$p_lbl = $ext_name;
		$p1->SetLabels($p_lbl);
		$graph->Add($p1);

		$p1->ShowBorder();
		$p1->SetColor('black');
		$p1->SetSliceColors(array('#800000','#339933','#99ff66','#ff6699','#0099ff'));

		$fp = elgg_get_plugins_path()."c_dashboard_usage/img/pie_chart-".$num_pie.".png";
		$file = $graph->Stroke($fp);
		echo '<br/><img src="'.elgg_get_site_url().'mod/c_dashboard_usage/img/pie_chart-'.$num_pie.'.png"><br/>';
		$num_pie++;
	} else {
		echo '<center><strong><p>No Data Available</p></strong></center>';
	}
	
} // end loop for groups

echo "<hr>";






// users
echo $site_entities[2];
echo "<hr>";
foreach ($elgg_users as $elgg_user) {
	echo "..<strong>".(int)$elgg_user->guid." - ".$elgg_user->username."</strong><br/>";
	$ext_count = array();
	// get all files
	$elgg_files = elgg_get_entities(array(
		'subtype' => 'file',
		'type' => 'object',
		'container_guids' => array($elgg_user->guid),
	));

	foreach ($elgg_files as $elgg_file) {
		$url = elgg_add_action_tokens_to_url(elgg_get_site_url().'action/file/delete?guid='.$elgg_file->getGUID());
		$ext = pathinfo($elgg_file->getFilename(), PATHINFO_EXTENSION);
		if (($elgg_file->getContainerEntity()->getType() === 'user') && ($elgg_file->getContainerEntity()->getGUID() == (int)$elgg_user->guid)) {
			echo '......'.$elgg_file->getFilename()." | ".$elgg_file->size()." bytes"." <a href='".$url."'> X </a><br/>";
			echo '..........'.$ext.' | '.$elgg_file->getOwnerEntity()->username." | <a href='".$elgg_file->getURL()."'>File Location</a> | ".$elgg_file->getTimeUpdated()." | ".$elgg_file->getContainerEntity()->name."<br/>";
			if (!$ext_count[$ext])
				$ext_count[$ext] = 1;
			else
				$ext_count[$ext]++;
		}
	}

	$ext_name = array();
	$data = array();
	foreach ($ext_count as $extension => $total_ext) {
		$ext_name[] = $extension."\n%.1f%%";
		$data[] = $total_ext;
	}
	
	if ($data) {
		// Create the Pie Graph. 
		$graph = new PieGraph(350,250);

		$theme_class="DefaultTheme";
		//$graph->SetTheme(new $theme_class());

		// Set A title for the plot
		$graph->title->Set("");
		$graph->SetBox(true);

		// Create
		$p1 = new PiePlot($data);
		$p1->SetLabelType(PIE_VALUE_PER);
		$p_lbl = $ext_name;
		$p1->SetLabels($p_lbl);
		$graph->Add($p1);

		$p1->ShowBorder();
		$p1->SetColor('black');
		$p1->SetSliceColors(array('#800000','#339933','#99ff66','#ff6699','#0099ff'));

		$fp = elgg_get_plugins_path()."c_dashboard_usage/img/pie_chart-".$num_pie.".png";
		$file = $graph->Stroke($fp);
		echo '<br/><img src="'.elgg_get_site_url().'mod/c_dashboard_usage/img/pie_chart-'.$num_pie.'.png"><br/>';
		$num_pie++;
	} else {
		echo '<center><strong><p>No Data Available</p></strong></center>';
	}
}
echo "<br/><br/>";


?>



<!-- Testing Collapsible Tree List (Visually appealing) -->
<ol class="tree">


<li>
	<label>Site1</label><input type="checkbox" id="folder1" />
		<ol>
			<li>
				<label for="subsubfolder2">File</label> <input type="checkbox" id="subsubfolder2" />
				<ol>
					<li class="file"><a href="">File Info</a></li>
				</ol>
			</li>
			<li>
				<label for="subsubfolder2">File</label> <input type="checkbox" id="subsubfolder2" />
				<ol>
					<li class="file"><a href="">File Info</a></li>
				</ol>
			</li>
		</ol>
	</li>

<li>
	<label>Site1</label><input type="checkbox" id="folder1" />
		<ol>
			<li>
				<label for="subsubfolder2">File</label> <input type="checkbox" id="subsubfolder2" />
				<ol>
					<li class="file"><a href="">File Info</a></li>
				</ol>
			</li>
			<li>
				<label for="subsubfolder2">File</label> <input type="checkbox" id="subsubfolder2" />
				<ol>
					<li class="file"><a href="">File Info</a></li>
				</ol>
			</li>
		</ol>
	</li>

</ol>

<br/><br/>