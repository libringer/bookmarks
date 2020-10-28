$(document).ready(function () {
	$('body').on('submit', '[name="bookmarks_add"]', function (e) {
		var _this = this;
		$('.js-bookmarks-add-status', $(_this)).text('');

		var serialize = $(_this).serialize();
		var button = $(_this).find('button');
		if (button.attr('name') !== undefined) {
			serialize += '&' + button.attr('name') + '=' + button.val();
		}
		$.ajax({
			url: $(_this).attr('action'),
			method: $(_this).attr('method'),
			data: serialize,
			dataType: "json",
			success: function (response) {
				if (response.status) {
					location.href = response.href;
				} else {
					if (response.msg) {
						for (key in response.msg) {
							$('.js-bookmarks-add-status', $(_this)).append('<p>' + response.msg[key] + '</p>');
						}
					}
				}
			}
		});

		return false;
	});
});