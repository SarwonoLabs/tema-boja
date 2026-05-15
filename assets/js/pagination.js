/**
 * TEMA BOJA - Pagination
 */
function initPagination(paginationSelector, itemsSelector, perPage) {
    perPage = perPage || 10;
    var $pagination = $(paginationSelector);
    var $items = $(itemsSelector);
    var totalItems = $items.length;
    var totalPages = Math.ceil(totalItems / perPage);
    var currentPage = 1;

    function showPage(page) {
        currentPage = page;
        $items.hide();
        $items.slice((page - 1) * perPage, page * perPage).show();
        renderPagination();
    }

    function renderPagination() {
        $pagination.empty();
        if (totalPages <= 1) return;

        var html = '<ul class="pagination-list">';

        // Prev
        html += '<li class="' + (currentPage === 1 ? 'disabled' : '') + '">';
        html += '<a href="#" data-page="' + (currentPage - 1) + '">&laquo;</a></li>';

        // Pages
        for (var i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                html += '<li class="' + (i === currentPage ? 'active' : '') + '">';
                html += '<a href="#" data-page="' + i + '">' + i + '</a></li>';
            } else if (i === currentPage - 3 || i === currentPage + 3) {
                html += '<li class="dots"><span>...</span></li>';
            }
        }

        // Next
        html += '<li class="' + (currentPage === totalPages ? 'disabled' : '') + '">';
        html += '<a href="#" data-page="' + (currentPage + 1) + '">&raquo;</a></li>';

        html += '</ul>';
        $pagination.html(html);
    }

    // Events
    $pagination.on('click', 'a[data-page]', function(e) {
        e.preventDefault();
        var page = parseInt($(this).data('page'));
        if (page >= 1 && page <= totalPages && page !== currentPage) {
            showPage(page);
        }
    });

    if (totalItems > 0) {
        showPage(1);
    }
}
