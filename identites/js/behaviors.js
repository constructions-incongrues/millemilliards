$(document).ready(function() {
	
	// Initial identity. Say hello to Raymond !
	$('#top').css('backgroundImage', 'url(images/static/cidrolin_top.png)');
	$('#middle').css('backgroundImage', 'url(images/static/cidrolin_middle.png)');
	$('#bottom').css('backgroundImage', 'url(images/static/cidrolin_bottom.png)');
	
	$('#ln-top').load(function() {
		$('#top').css('backgroundImage', 'url(' + $(this).attr('src') + ')');
		$('#top').show('slide', {direction: 'left'});
		if ($('#ln-top').attr('complete') && $('#ln-top').attr('complete')) {
			$('body').css('backgroundImage', '');
		}
	});
	$('#ln-middle').load(function() {
		$('#middle').css('backgroundImage', 'url(' + $(this).attr('src') + ')');
		$('#middle').show('slide', {direction: 'right'});
		if ($('#ln-middle').attr('complete') && $('#ln-middle').attr('complete')) {
			$('body').css('backgroundImage', '');
		}
	});
	$('#ln-bottom').load(function() {
		$('#bottom').css('backgroundImage', 'url(' + $(this).attr('src') + ')');
		$('#bottom').show('slide', {direction: 'left'});
		if ($('#ln-bottom').attr('complete') && $('#ln-bottom').attr('complete')) {
			$('body').css('backgroundImage', '');
		}
	});
	$('a#random').click(function(event) {
		event.preventDefault();
		$('#footer').hide();
		$('body').css('backgroundImage', 'url(images/static/loader-pattern.gif)');
		$('body').css('backgroundRepeat', 'repeat');
		$('input#permalinkUrl').hide('fade');
		$('#top').hide('slide', {direction: 'right'});
		$('#middle').hide('slide', {direction: 'left'});
		$('#bottom').hide('slide', {direction: 'right'}, function() {
			$.getJSON($('a#random').attr('href'), null, function(data, textStatus, jqXHR) {
				// Reload identity parts
				$('#ln-top').attr('src', 'images/parts/1/' + data.top);
				$('#ln-middle').attr('src', 'images/parts/2/' + data.middle);
				$('#ln-bottom').attr('src', 'images/parts/3/' + data.bottom);

				// Update permalink
				$('a#permalink').attr('href', urlRoot + '/'+'?part1='+data.top+'&part2='+data.middle+'&part3='+data.bottom);
				$('input#permalinkUrl').val(urlRoot + '/' + '?part1=' + data.top + '&part2=' + data.middle + '&part3=' + data.bottom);

				// Update download link
				$('a#download').attr('href', 'download.php'+'?part1='+data.top+'&part2='+data.middle+'&part3='+data.bottom);
			});
		});
	});
	
	$('a#permalink').click(function(event) {
		event.preventDefault();
		$('input#permalinkUrl').toggle('fade');
	});

	$('#content').hover(function() {
		$('#footer').show('fade');
	});
	
	$('#content').click(function() {
		$('#info, #bubble').show('fade');
	});
	
	$('#footer').hide();
	
	$('#info a#close').click(function(event) {
		event.preventDefault();
		$('#info, #bubble, #footer').hide('fade');
	})
});