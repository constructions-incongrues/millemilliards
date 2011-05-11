$(document).ready(function() {
	
	$('#ln-top').load(function() {
		$('#top').css('backgroundImage', 'url(' + $(this).attr('src') + ')');
		$('#top').show('slide', {direction: 'left'});
		if ($('#ln-middle').attr('complete') && $('#ln-bottom').attr('complete')) {
			$('body').css('backgroundImage', '');
		}
	});
	$('#ln-middle').load(function() {
		$('#middle').css('backgroundImage', 'url(' + $(this).attr('src') + ')');
		$('#middle').show('slide', {direction: 'right'});
		if ($('#ln-top').attr('complete') && $('#ln-bottom').attr('complete')) {
			$('body').css('backgroundImage', '');
		}
	});
	$('#ln-bottom').load(function() {
		$('#bottom').css('backgroundImage', 'url(' + $(this).attr('src') + ')');
		$('#bottom').show('slide', {direction: 'left'});
		if ($('#ln-top').attr('complete') && $('#ln-middle').attr('complete')) {
			$('body').css('backgroundImage', '');
		}
	});
	$('a#random').click(function(event) {
		event.preventDefault();
		$('body').css('backgroundImage', 'url(images/static/loader-pattern.gif)');
		$('body').css('backgroundRepeat', 'repeat');
		$('input#permalinkUrl').hide();
		$('#top').hide('slide', {direction: 'right'});
		$('#middle').hide('slide', {direction: 'left'});
		$('#bottom').hide('slide', {direction: 'right'}, function() {
			$.getJSON($('a#random').attr('href'), null, function(data, textStatus, jqXHR) {
				// Reload identity parts
				$('#ln-top').attr('src', 'images/parts/1/' + data.top);
				$('#ln-middle').attr('src', 'images/parts/2/' + data.middle);
				$('#ln-bottom').attr('src', 'images/parts/3/' + data.bottom);

				// Update permalink
				$('a.share').attr('href', urlRoot + '/'+'?part1=' + data.top + '&part2=' + data.middle + '&part3=' + data.bottom);
				$('input#permalinkUrl').val($('a.share').attr('href'));

				// Update download link
				$('a.download').attr('href', 'download.php'+'?part1='+data.top+'&part2='+data.middle+'&part3='+data.bottom);
			});
		});
	});
	
	$('a.share').live('click', function(event) {
		event.preventDefault();
		$('input#permalinkUrl').val($(this).attr('href'));
		$('input#permalinkUrl').toggle('fade');
	});

	$('#content').hover(function() {
		$('#middle').css('backgroundImage', 'url(images/static/reload.gif)');
	}, function() {
		$('#middle').css('backgroundImage', 'url('+$('#ln-middle').attr('src')+')');
	});
	
	$('#content').click(function() {
		$('#random').click();
	});
	
	$('#menu a.about').click(function(event) {
		event.preventDefault();
		$('#content').flip({direction: 'rl', color:'#fff', content: $('#info').html()});
		$(this).parent('li').hide();
		$(this).parent('li').siblings().hide('fade');
		$('#menu a.about-back').parent('li').show();
	});
	
	$('#menu a.about-back').live('click', function(event) {
		event.preventDefault();
		$(this).parent('li').siblings().show('fade');
		$('#menu a.about').parent('li').show();
		$(this).parent('li').hide();
		$('#content').revertFlip();
	});
	
	$('#container').hover(
		function() {
			$('#container ul#menu').show('fade');
		}, function() {
			$('#container ul#menu').hide('fade');
		}
	);
});