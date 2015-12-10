<?php

// load library for the graphs and charts (vendor)
elgg_load_library('jgraph');
elgg_load_library('jgraph_pie');
elgg_load_library('jgraph_bar');




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


// CREATE PIE CHART FOR VISUAL

// $data = array($pleio_main_size,$subsites_size,$groups_size,$users_size);
// $data = array($space_used, $total_space_avail);
// // Create the Pie Graph. 
// $graph = new PieGraph(350,250);

// $theme_class="DefaultTheme";
// //$graph->SetTheme(new $theme_class());

// // Set A title for the plot
// $graph->title->Set("Total Disk Space Usage");
// $graph->SetBox(true);

// // Create
// $p1 = new PiePlot($data);
// $graph->Add($p1);

// $p1->ShowBorder();
// $p1->SetColor('black');
// $p1->SetSliceColors(array('#800000','#339933'));

// $fp = elgg_get_plugins_path()."c_dashboard_usage/img/pie_chart1.png";
// $file = $graph->Stroke($fp);
//echo '<img src="'.elgg_get_site_url().'mod/c_dashboard_usage/img/pie_chart1.png">';


echo 'Total Space Available: '.$total_space_avail.' bytes <br/> Total Disk Space Used: '.$space_used.' bytes <br/> Total Space Allocated: '.$total_space_allocated.' bytes <br/> Number of Files: '.$num_of_file;


// get all site guid (since there are subsites)
$query = 'SELECT * FROM elgg_sites_entity';
$elgg_sites = get_data($query);

$query = 'SELECT * FROM elgg_groups_entity';
$elgg_groups = get_data($query);

$query = 'SELECT * FROM elgg_users_entity';
$elgg_users = get_data($query);

$site_entities = array("Sites","Groups","Users");
echo "<hr>";


// sites
echo $site_entities[0];
echo "<hr>";
foreach ($elgg_sites as $elgg_site) {
	echo "..<strong>".(int)$elgg_site->guid." - ".$elgg_site->name."</strong><br/>";

	// get all files
	$elgg_files = elgg_get_entities(array(
		'subtype' => 'file',
		'type' => 'object',
		'site_guid' => $elgg_site->guid,
	));

	foreach ($elgg_files as $elgg_file) {
		$url = elgg_add_action_tokens_to_url(elgg_get_site_url().'action/file/delete?guid='.$elgg_file->getGUID());
		echo '......'.$elgg_file->getFilename()." | ".$elgg_file->size()." bytes"." <a href='".$url."'> X </a><br/>"; // make sure after deletion, forward url back to this admin page...
		echo '..........'.$elgg_file->getOwnerEntity()->username." | <a href='".$elgg_file->getURL()."'>File Location</a> | ".$elgg_file->getTimeUpdated()."<br/>"; // time is displayed in epoch time
	}
}
echo "<hr>";


// groups
echo $site_entities[1];
echo "<hr>";
foreach ($elgg_groups as $elgg_group) {
	echo "..<strong>".(int)$elgg_group->guid." - ".$elgg_group->name."</strong><br/>";

	// get all files
	$elgg_files = elgg_get_entities(array(
		'subtype' => 'file',
		'type' => 'object',
		'container_guids' => array($elgg_group->guid),
	));

	foreach ($elgg_files as $elgg_file) {
		if (($elgg_file->getContainerEntity()->getType() === 'group') && ($elgg_file->getContainerEntity()->getGUID() == (int)$elgg_group->guid)) {
			echo '......'.$elgg_file->getFilename()." | ".$elgg_file->size()." bytes"." <a href='".$url."'> X </a><br/>";
			echo '..........'.$elgg_file->getOwnerEntity()->username." | <a href='".$elgg_file->getURL()."'>File Location</a> | ".$elgg_file->getTimeUpdated()." | ".$elgg_file->getContainerEntity()->name."<br/>";
		}
	}
}
echo "<hr>";



// users
echo $site_entities[2];
echo "<hr>";
foreach ($elgg_users as $elgg_user) {
	echo "..<strong>".(int)$elgg_user->guid." - ".$elgg_user->username."</strong><br/>";

	// get all files
	$elgg_files = elgg_get_entities(array(
		'subtype' => 'file',
		'type' => 'object',
		'container_guids' => array($elgg_user->guid),
	));

	foreach ($elgg_files as $elgg_file) {
		if (($elgg_file->getContainerEntity()->getType() === 'user') && ($elgg_file->getContainerEntity()->getGUID() == (int)$elgg_user->guid)) {
			echo '......'.$elgg_file->getFilename()." | ".$elgg_file->size()." bytes"." <a href='".$url."'> X </a><br/>";
			echo '..........'.$elgg_file->getOwnerEntity()->username." | <a href='".$elgg_file->getURL()."'>File Location</a> | ".$elgg_file->getTimeUpdated()." | ".$elgg_file->getContainerEntity()->name."<br/>";

		}
	}
}
echo "<br/><br/>";

elgg_load_css('collap_tree');
?>



<!-- Testing Collapsible Tree List (Visually appealing) -->

<ol class="tree">
		<li>
			<label>dsfsd</label>
			 <input type="checkbox" checked disabled id="folder1" /> 
			<ol>
				<li class="file"><a href="document.html.pdf">File 1</a></li>
				<li>
					<label for="subfolder1">Subfolder 1</label> <input type="checkbox" id="subfolder1" /> 
					<ol>
						<li class="file"><a href="">Filey 1</a></li>
						<li>
							<label for="subsubfolder1">Subfolder 1</label> <input type="checkbox" id="subsubfolder1" /> 
							<ol>
								<li class="file"><a href="">File 1</a></li>
								<li>
									<label for="subsubfolder2">Subfolder 1</label> <input type="checkbox" id="subsubfolder2" /> 
									<ol>
										<li class="file"><a href="">Subfile 1</a></li>
										<li class="file"><a href="">Subfile 2</a></li>
										<li class="file"><a href="">Subfile 3</a></li>
										<li class="file"><a href="">Subfile 4</a></li>
										<li class="file"><a href="">Subfile 5</a></li>
										<li class="file"><a href="">Subfile 6</a></li>
									</ol>
								</li>
							</ol>
						</li>
						<li class="file"><a href="">File 3</a></li>
						<li class="file"><a href="">File 4</a></li>
						<li class="file"><a href="">File 5</a></li>
						<li class="file"><a href="">File 6</a></li>
					</ol>
				</li>
			</ol>
		</li>
		<li>
			<label for="folder2">Folder 2</label> <input type="checkbox" id="folder2" /> 
			<ol>
				<li class="file"><a href="">File 1</a></li>
				<li>
					<label for="subfolder2">Subfolder 1</label> <input type="checkbox" id="subfolder2" /> 
					<ol>
						<li class="file"><a href="">Subfile 1</a></li>
						<li class="file"><a href="">Subfile 2</a></li>
						<li class="file"><a href="">Subfile 3</a></li>
						<li class="file"><a href="">Subfile 4</a></li>
						<li class="file"><a href="">Subfile 5</a></li>
						<li class="file"><a href="">Subfile 6</a></li>
					</ol>
				</li>
			</ol>
		</li>
		<li>
			<label for="folder3">Folder 3</label> <input type="checkbox" id="folder3" /> 
			<ol>
				<li class="file"><a href="">File 1</a></li>
				<li>
					<label for="subfolder3">Subfolder 1</label> <input type="checkbox" id="subfolder3" /> 
					<ol>
						<li class="file"><a href="">Subfile 1</a></li>
						<li class="file"><a href="">Subfile 2</a></li>
						<li class="file"><a href="">Subfile 3</a></li>
						<li class="file"><a href="">Subfile 4</a></li>
						<li class="file"><a href="">Subfile 5</a></li>
						<li class="file"><a href="">Subfile 6</a></li>
					</ol>
				</li>
			</ol>
		</li>
		<li>
			<label for="folder4">Folder 4</label> <input type="checkbox" id="folder4" /> 
			<ol>
				<li class="file"><a href="">File 1</a></li>
				<li>
					<label for="subfolder4">Subfolder 1</label> <input type="checkbox" id="subfolder4" /> 
					<ol>
						<li class="file"><a href="">Subfile 1</a></li>
						<li class="file"><a href="">Subfile 2</a></li>
						<li class="file"><a href="">Subfile 3</a></li>
						<li class="file"><a href="">Subfile 4</a></li>
						<li class="file"><a href="">Subfile 5</a></li>
						<li class="file"><a href="">Subfile 6</a></li>
					</ol>
				</li>
			</ol>
		</li>
		<li>
			<label for="folder5">Folder 5</label> <input type="checkbox" id="folder5" /> 
			<ol>
				<li class="file"><a href="">File 1</a></li>
				<li>
					<label for="subfolder5">Subfolder 1</label> <input type="checkbox" id="subfolder5" /> 
					<ol>
						<li class="file"><a href="">Subfile 1</a></li>
						<li class="file"><a href="">Subfile 2</a></li>
						<li class="file"><a href="">Subfile 3</a></li>
						<li class="file"><a href="">Subfile 4</a></li>
						<li class="file"><a href="">Subfile 5</a></li>
						<li class="file"><a href="">Subfile 6</a></li>
					</ol>
				</li>
			</ol>
		</li>
	</ol>