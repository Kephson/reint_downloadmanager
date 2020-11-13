jQuery(function ($) {

	$(".searchField").on("keyup", function () {
		var value = $(this).val().toLowerCase();
		var searchList = $(this).data('search');
		$(searchList + " li").filter(function () {
			$(this).toggle($(this).data('filtertext').toLowerCase().indexOf(value) > -1)
		});
	});

	// search function with filters
	// see http://api.jquerymobile.com/filterable/
	//$( "#filterItems" ).filterable({defaults:true});

	// close collections when a search is started
	/*
	$('#searchFileField').on('keyup', function () {
		$('.collection-wrapper .collection-header').removeClass('open');
		$('ul.collection-content').css('height', '0px').removeAttr('style');
	});
*/
});
