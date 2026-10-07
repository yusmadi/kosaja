/**
 * KosAja - Public UI Script (jQuery & Bootstrap 5)
 */

$(document).ready(function() {
    console.log("KosAja Platform Initialized.");

    // Quick filter button interaction
    $('.btn-quick-filter').on('click', function(e) {
        e.preventDefault();
        $('.btn-quick-filter').removeClass('active');
        $(this).addClass('active');

        const filterType = $(this).data('filter');
        if (filterType === 'all') {
            $('.property-card-col').fadeIn(200);
        } else {
            $('.property-card-col').hide();
            $('.property-card-col[data-gender="' + filterType + '"]').fadeIn(200);
        }
    });

    // Form search submit handler
    $('#searchForm').on('submit', function(e) {
        const keyword = $('#searchKeyword').val().toLowerCase();
        const type = $('#searchType').val();
        const gender = $('#searchGender').val();

        $('.property-card-col').each(function() {
            let cardText = $(this).text().toLowerCase();
            let cardGender = $(this).data('gender');
            let cardType = $(this).data('type');

            let matchesKeyword = !keyword || cardText.indexOf(keyword) !== -1;
            let matchesGender = !gender || cardGender === gender;
            let matchesType = !type || cardType === type;

            if (matchesKeyword && matchesGender && matchesType) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        e.preventDefault();
    });
});
