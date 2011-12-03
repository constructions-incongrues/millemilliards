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
		$.data($('#middle')[0], 'previousBackgroundImage', $('#middle').css('backgroundImage'));
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
		$('#sharebox').hide('fade');
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
				$('a.share').parent('li').show('fade');
				
				// Update download link
				$('a.download').attr('href', 'download.php'+'?part1='+data.top+'&part2='+data.middle+'&part3='+data.bottom);
				$('a.download').parent('li').show('fade');
			});
		});
	});
	
	$('a.share').live('click', function(event) {
		event.preventDefault();
		var urlShare = $(this).attr('href');
		// Shorten URL using bit.ly
		var urlBitly = 'http://api.bit.ly/v3/shorten/?login=millemilliards&apiKey=R_e32285dd4c3f112da1790f771fbc1b2f&longUrl=' + encodeURIComponent(urlShare);
		$.get(urlBitly, function(data, textStatus, jqXHR) {
			if (data.status_code == 200) {
				console.log(data);
				$('#sharebox p').html(data.data.url);
				$('#sharebox').show('fade');
			} else {
				$('#sharebox p').html(urlShare);
				$('#sharebox').show('fade');
			}
		});
	});
	
	$('#sharebox a.close').click(function(event) {
		event.preventDefault();
		$('#sharebox').hide('fade');
	});
	

	$('#content').one('mouseenter', function() {
		$.data($('#middle')[0], 'previousBackgroundImage', $('#middle').css('backgroundImage'));
		$('#middle').css('backgroundImage', 'url(images/static/reload.gif)');
	});
	$('#content').mouseleave(function() {
		$('#middle').css('backgroundImage', $.data($('#middle')[0], 'previousBackgroundImage'));
		$('#content').one('mouseenter', function() {
			$('#middle').css('backgroundImage', 'url(images/static/reload.gif)');
		});
	});
	
	$('#content').click(function() {
		$('#random').click();
	});
	
	$('#menu a.about').click(function(event) {
		event.preventDefault();
		$('#content').flip({direction: 'rl', color:'#fff', content: $('#info').html()});
		$(this).parent('li').hide();
		$(this).parent('li').siblings().hide('fade');
		$('#menu a.back').parent('li').show();
	});
	
	$('#menu a.contribute').click(function(event) {
		event.preventDefault();
		$('#content').flip({direction: 'rl', color:'#fff', content: $('#contribute').html()});
		$(this).parent('li').hide();
		$(this).parent('li').siblings().hide('fade');
		$('#menu a.back').parent('li').show();
	});
	
	$('#menu a.back').live('click', function(event) {
		event.preventDefault();
		$(this).parent('li').siblings().show('fade');
		$(this).parent('li').hide();
		$('#content').revertFlip();
	});

	
	$('#container').hover(
		function() {
			$('#container ul#menu').animate({opacity: 1});
		}, function() {
			$('#container ul#menu').animate({opacity: 0});
		}
	);
});