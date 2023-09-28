// Bulk Edit
jQuery(document).ready(function () {
    jQuery('#bulkedit').DataTable({
        "ajax": {
            url: MyAjax.ajaxurl,
            type: "POST",
    
            data: {
                "action": 'get_post_data',
            }
        },
    });
});




// extra

// var userdataTable = jQuery('#pwds_data').DataTable({
//     "processing": true,
//     "order": [[1, 'desc']],
//     "language": {
//         "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw border-0"></i>',
//         "lengthMenu": MyAjax.shw_menu + " _MENU_",
//         "sSearch": MyAjax.search_bar + ':',
//         "sZeroRecords": MyAjax.no_records,
//         "sInfo": MyAjax.filter_record,
//         "sInfoEmpty": MyAjax.empty_record,
//         "oPaginate": {
//             "sNext": MyAjax.nxt_pg,
//             "sPrevious": MyAjax.prev_pg,
//         },

//     },
//     "serverSide": true,
//     "ajax": {
//         url: MyAjax.ajaxurl,
//         type: "POST",

//         data: {
//             "module": 'password',
//             "action": 'get_new_pass',
//             'security_nonce': security_nonce
//         }
//     },
//     "columnDefs": [
//         {
//             "targets": [1, 2],
//             "orderable": false,
//         },
//         { className: "td-text-center text-center", targets: [6, 5, 3] },
//         { className: "td-text-center text-end", targets: [4] },
//         { width: "150px", targets: [6] },
//     ],

//     "pageLength": 2,
//     rowReorder: {
//         selector: 'td:nth-child(2)'
//     },
//     responsive: true
// });