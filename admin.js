jQuery(document).ready(function($){
    // Admin edit MCQ handler (example, actual logic may need AJAX)
    $('.mcq-edit').on('click', function(e){
        e.preventDefault();
        let row = $(this).closest('tr');
        $('#mcq_id').val( row.find('td:eq(0)').text().trim() );
        $('#question').val( row.find('td:eq(1)').text().trim() );
        let opts = row.find('td:eq(2)').html().split('<br>');
        $('#option_a').val( $(opts[0]).text().replace('A:', '').trim() );
        $('#option_b').val( $(opts[1]).text().replace('B:', '').trim() );
        $('#option_c').val( $(opts[2]).text().replace('C:', '').trim() );
        $('#option_d').val( $(opts[3]).text().replace('D:', '').trim() );
        $('#correct_option').val( row.find('td:eq(3)').text().trim() );
        $('#difficulty').val( row.find('td:eq(4)').text().trim() );
        // Category selection not filled here; add logic as needed
    });
    // Delete MCQ handler
    $('.mcq-delete').on('click', function(e){
        e.preventDefault();
        if(confirm('Are you sure you want to delete this MCQ?')) {
            var id = $(this).data('id');
            window.location.href = mcqsDev.ajaxurl + '?action=delete_mcq&id=' + id + '&_wpnonce=' + mcqsDev.nonce;
        }
    });
});