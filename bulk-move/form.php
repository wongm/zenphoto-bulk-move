<?php

printAdminHeader('overview', gettext('Bulk move images'));
printZenJavascripts()
?>
<script src="bulk-move.js?v=22"></script>
<script src="chosen.jquery.min.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="bulk-move.css" />
<?php
echo '</head>';
?>
<body>
	<?php printLogoAndLinks(); ?>
	<div id="main">
		<?php printTabs(); ?>
		<div id="content">
			<div class="tabbox">
				<h1>Bulk move images</h1>
				<form id="searchForm">
					<div id="searchPanel">
						<div id="actionMessage"></div>
						<label for="sourceAlbum">Source</label>
						<select id="sourceAlbum"><?php drawAlbumList(); ?></select>
						<label for="includes">Includes</label>
						<input type="text" id="includes" class="panel"/>
						<label for="excludes">Excludes</label>
						<input type="text" id="excludes" class="panel"/>
						<label for="dateFrom">From</label>
						<input type="text" id="dateFrom" class="panel datepicker" placeholder="2015-01-01" />
						<label for="dateTo">To</label>
						<input type="text" id="dateTo" class="panel datepicker" placeholder="2015-12-12" />
						<br style="clear:both" />
					</div>
					<p class="buttons">
						<button type="submit" id="search" value="Search for items">Search for items</button>
					</p>
				</form>
				<div id="searchResults" style="display:none"></div>
				<br style="clear:both" />
			</div><!-- content -->
		</div><!-- content -->
	</div><!-- main -->
	<?php printAdminFooter(); ?>
</body>
<?php
echo "</html>";
?>
