$(function() {
	//$(".datepicker").datepicker({ dateFormat: 'yy-mm-dd' });
	$("#search").click(runSearch);
	$('#searchPanel input').keypress(function (e) {
		if (e.which == 13) {
			runSearch();
			return false;
		}
	});
	$("#sourceAlbum").chosen();
});

function runSearch() {
	$url = "?action=search&sourceAlbum=" + $("#sourceAlbum").val() + "&includes=" + $("#includes").val() + "&excludes=" + $("#excludes").val() + "&dateFrom=" + $("#dateFrom").val() + "&dateTo=" + $("#dateTo").val()
	
	$.get($url, function( data ) {
		$("#searchResults").toggle();
		$("#searchForm").toggle();
	
		$("#searchResults").html( data );

		//register the event magic
		$("#allImagesBottom").click(function() { toggleCheckboxes(this.checked) });
		$("#allImagesTop").click(function() { toggleCheckboxes(this.checked) });
		$("#moveItems").click(moveItems);
		$(".cancelSearch").click(displaySearch);
		
		$("#destinationAlbum").chosen();
	});
	
	return false;
}

function moveItems() {
	var selectedItems = [];
	$(".imageOption").each(function( index ) {
		if (this.checked) {
			selectedItems.push(this.value);
		}
	});
	
	if (selectedItems.length == 0) {
		alert("Select an image to move!");
		return false;
	}
	
	if ($("#destinationAlbum").val().length == 0) {
		alert("Select a destination album!");
		return false;
	}
	
	var data = { 
		filenames: selectedItems,
		sourceAlbum: $("#sourceAlbum").val(),
		destinationAlbum: $("#destinationAlbum").val(),
	};
	
	var request = $.ajax({
		type: "POST",
		url: "#",
		data: data,
	});
	
	request.done(function() {
		$("#actionMessage").html( '<h2 class="messagebox">Moving ' + selectedItems.length + ' images successful!</h2>' );
	});
	request.fail(function() {
		$("#actionMessage").html( '<h2 class="errorbox">Moving ' + selectedItems.length + ' images FAILED!</h2>' );
	});
	request.always(function() {
		displaySearch();
	});

	return false;
}

function displaySearch() {
	$("#searchResults").toggle();
	$("#searchForm").toggle();
}

function toggleCheckboxes(selectAll) {
	$(".imageCheckbox").each(function( index ) {
		this.checked = selectAll;
	});
}