jQuery(document).ready(function ($) {
  // Handle search form submission
  $('#psych-search-form').on('submit', function (e) {
    e.preventDefault();

    var data = {
      action: 'psych_search',
      lat: $('#search-lat').val(),
      lng: $('#search-lng').val(),
      radius: $('#search-radius').val(),
      nonce: wpPsychSearch.nonce,
    };

    $.ajax({
      url: wpPsychSearch.ajaxUrl,
      method: 'POST',
      data: data,
      success: function (response) {
        var resultsContainer = $('#psych-search-results');
        resultsContainer.empty();

        if (response.success && response.data.length > 0) {
          $.each(response.data, function (index, item) {
            resultsContainer.append(
              '<li>' +
                '<a href="' +
                item.link +
                '" class="psych-search-link">' +
                '<span class="psych-search-title">' +
                item.title +
                '</span>' +
                '</a>' +
                '</li>',
            );
          });
        } else {
          resultsContainer.append('<li>No results found.</li>');
        }
      },
      error: function () {
        alert('An error occurred while searching.');
      },
    });
  });
});
