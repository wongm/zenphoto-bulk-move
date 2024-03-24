<?php

function drawAlbumList()
{
    global $_zp_gallery;
	$albumlist = $_zp_gallery->getAllAlbumsFromDB();
	echo "<option value=''>Select</option>\n";
    foreach ($albumlist as $fullfolder => $albumtitle) {
		$singlefolder = $fullfolder;
		$saprefix = "";
		$selected = "";
		// Get rid of the slashes in the subalbum, while also making a subalbum prefix for the menu.
		while (strstr($singlefolder, '/') !== false) {
			$singlefolder = substr(strstr($singlefolder, '/'), 1);
			$saprefix = "&nbsp; &nbsp;&nbsp;" . $saprefix;
		}
		echo '<option value="' . $fullfolder . '">' . $saprefix . $singlefolder . "</option>\n";
	}
}

function drawResults()
{
	global $_zp_db;
	$locale = null;
?>
<form id="imageForm">
	<div class="imageOptionPanel">
		<label for="allImagesTop"><?php echo gettext("All") ?><input type="checkbox" id="allImagesTop" class="imageCheckbox" /></label>
	</div>
	<div class="imageOptionPanel">
		<label class="cancelSearch">Cancel</label>
	</div>
	<div class="imageOptionPanel">
		<label for="destinationAlbum">Destination
			<select id="destinationAlbum">
				<?php drawAlbumList(); ?>
			</select>
		</label>
	</div>
<?php
	$sourceAlbum = isset($_GET['sourceAlbum']) ? $_GET['sourceAlbum'] : "";
	$includes = isset($_GET['includes']) ? $_GET['includes'] : "";
	$excludes = isset($_GET['excludes']) ? $_GET['excludes'] : "";
	$dateFrom = isset($_GET['dateFrom']) ? $_GET['dateFrom'] : "";
	$dateTo = isset($_GET['dateTo']) ? $_GET['dateTo'] : "";
	$itemId = 0;
	
    $sqlWhere = "a.folder = '$sourceAlbum'";
	if (strlen($includes) > 0)
	{
		$sqlWhere .= " AND (i.title LIKE " . $_zp_db->quote("%" . $includes . "%") . " OR i.`desc` LIKE " . $_zp_db->quote("%" . $includes . "%") . ")";
	}
	if (strlen($excludes) > 0)
	{
		$sqlWhere .= " AND (IFNULL(i.title, '') NOT LIKE " . $_zp_db->quote("%" . $excludes . "%") . " AND IFNULL(i.`desc`, '') NOT LIKE " . $_zp_db->quote("%" . $excludes . "%") . ")";
	}
	if (strlen($dateFrom) > 0)
	{
		$sqlWhere .= " AND i.date >= " . $_zp_db->quote($dateFrom);
	}
	if (strlen($dateTo) > 0)
	{
		$sqlWhere .= " AND i.date <= " . $_zp_db->quote($dateTo);
	}
	
	$sql = "SELECT i.id, i.filename, i.title, i.date, i.`desc`
			FROM " . $_zp_db->prefix('images') . " i
			INNER JOIN " . $_zp_db->prefix('albums') . " a ON i.albumid = a.id
			WHERE " . $sqlWhere . "
			ORDER BY i.date DESC";
	$itemResults = $_zp_db->queryFullArray($sql);
	
	foreach ($itemResults as $item)
	{
		$itemId = $item['id'];
		$filename = $item['filename'];
		$date = $item['date'];
		$caption = get_language_string($item['title'], $locale);
		$description = get_language_string($item['desc'], $locale);
		
		if (strlen($description) > 0)
		{
			$caption = '<abbr title="' . $description . '">' . $caption .'</abbr>';
		}
	?>
		<div class="imageOptionPanel">
			<input type="checkbox" id="item<?php echo $itemId ?>" value="<?php echo $filename ?>" class="imageCheckbox imageOption">
			<label for="item<?php echo $itemId ?>"><?php echo $caption ?> (<?php echo $date; ?>)</label>
		</div>
	<?php
	}
	
	if ($itemId > 0) 
	{
?>
	<div class="imageOptionPanel">
		<label for="allImagesTop"><?php echo gettext("All") ?><input type="checkbox" id="allImagesTop" class="imageCheckbox" /></label>
	</div>
	<p class="buttons">
		<input type="hidden" id="sourceAlbum" value="<?php echo $sourceAlbum ?>" />
		<button type="submit" id="moveItems" value="Tag selected items">Move selected items</button>
	</p>
	<?php 
	}
	else
	{
?>
	<p class="buttons">
		<button type="submit" class="cancelSearch" value="Return">Return</button>
	</p>
<?php
	}
	?>
</form>
<?php
}

function processRequest()
{
	$filenames = $_POST["filenames"];
	$sourceAlbum = $_POST["sourceAlbum"];
	$destinationAlbum = $_POST["destinationAlbum"];
	
	foreach($filenames as $filename)
	{
		$album = AlbumBase::newAlbum($sourceAlbum);
		$imageobj = Image::newImage($album, $filename);
        $result = $imageobj->move($destinationAlbum);
		
		if ($result != 0)
		{
    		header("HTTP/1.1 500 Internal Server Error");
		}
		echo "<p>Moving $filename - result $result</p>";
	}
}

?>