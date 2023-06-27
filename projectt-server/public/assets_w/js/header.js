$(document).on('scroll', function() {
	if ($(document).scrollTop() > 50) {
		$('.nav').addClass('nav-shrink');
	} else {
		$('.nav').removeClass('nav-shrink');
	}
});