<?php

function drawAlbumList()
{
    $albumlist = array();
	genAlbumList($albumlist);
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
	$itemId = 0;
	
    $sqlWhere = "a.folder = '$sourceAlbum'";
	if (strlen($includes) > 0)
	{
		$sqlWhere .= " AND (i.title LIKE " . db_quote("%" . $includes . "%") . " OR i.`desc` LIKE " . db_quote("%" . $includes . "%") . ")";
	}
	if (strlen($excludes) > 0)
	{
		$sqlWhere .= " AND (IFNULL(i.title, '') NOT LIKE " . db_quote("%" . $excludes . "%") . " AND IFNULL(i.`desc`, '') NOT LIKE " . db_quote("%" . $excludes . "%") . ")";
	}
	
	$sql = "SELECT i.id, i.filename, i.title, i.mtime, i.`desc`
			FROM " . prefix('images') . " i
			INNER JOIN " . prefix('albums') . " a ON i.albumid = a.id
			WHERE " . $sqlWhere . "
			ORDER BY i.date DESC";
	$itemResults = query_full_array($sql);
	
	foreach ($itemResults as $item)
	{
		$itemId = $item['id'];
		$filename = $item['filename'];
		$mtime = $item['mtime'];
		$caption = get_language_string($item['title'], $locale);
		$description = get_language_string($item['desc'], $locale);
		
		if (strlen($description) > 0)
		{
			$caption = '<abbr title="' . $description . '">' . $caption .'</abbr>';
		}
	?>
		<div class="imageOptionPanel">
			<input type="checkbox" id="item<?php echo $itemId ?>" value="<?php echo $filename ?>" class="imageCheckbox imageOption">
			<label for="item<?php echo $itemId ?>"><?php echo $caption ?> (<?php echo zpFormattedDate(DATE_FORMAT, $mtime) ?>)</label>
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
		$album = newAlbum($sourceAlbum);
		$imageobj = newImage($album, $filename);	
        $result = $imageobj->move($destinationAlbum);
		
		if ($result != 0)
		{
    		header("HTTP/1.1 500 Internal Server Error");
		}
		echo "<p>Moving $filename - result $result</p>";
	}
}

?>