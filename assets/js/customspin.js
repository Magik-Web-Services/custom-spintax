jQuery(document).ready(function () {
	jQuery('#lstofsheets').DataTable({
		paging: false,
		ordering: false,
		info: false,
		searching: false
	});
	jQuery('#listofitems').DataTable();
	// get_category_by_selected();
});

// Bulk Edit
jQuery(document).ready(function () {
	jQuery('#bulkedit').DataTable({
		paging: false,
		ordering: false,
		info: false,
		searching: false
	});
	jQuery('#bulkedit').DataTable();
});

/**
  * Meta box
  * */
jQuery(document).on('click', '.addmetafield', function () {

	var variable_count = jQuery('input[name="variable_count"]').val();
	jQuery('#variable tbody').append('<tr><td><input type="text" id="spintax_variable_key" name="spintax_variable_key[]" class="widefat" /></td><td><input type="text" id="spintax_variable_value" name="spintax_variable_value[]" class="widefat" /></td><td class="actionbtn"><a href="javascript:void(0)" class="button delete_row">Delete</a></td></tr>');
	var itemslength = jQuery('#variable tbody tr').length;
	if (itemslength == 1) {
		jQuery('#variable tbody tr .actionbtn').hide();
	} else {
		jQuery('#variable tbody tr .actionbtn').show();
	}
	variable_count++;
	jQuery('input[name="variable_count"]').val(variable_count)
});

jQuery(document).on('click', 'a.delete_row', function () {
	var variable_count = jQuery('input[name="variable_count"]').val();
	jQuery(this).closest('tr').remove();
	var itemslength = jQuery('#variable tbody tr').length;
	if (itemslength == 1) {
		jQuery('#variable tbody tr .actionbtn').hide();
	} else {
		jQuery('#variable tbody tr .actionbtn').show();
	}
	variable_count--;
	jQuery('input[name="variable_count"]').val(variable_count)
});

// (C) INITIALIZE UPLOADER
window.onload = () => {
	// (C1) GET HTML FILE LIST
	var list = document.getElementById("list");

	// (C2) INIT PLUPLOAD
	var uploader = new plupload.Uploader({
		runtimes: "html5",
		browse_button: "pick",
		url: MyAjax.pluginurl + 'includes/uploads.php',
		chunk_size: "10mb",
		unique_names: true,
		filters: [
			{ title: "csv upload", extensions: "csv" }
		],
		init: {
			PostInit: () => list.innerHTML = "<div></div>",
			FilesAdded: (up, files) => {
				plupload.each(files, file => {
					let row = document.createElement("div");
					row.id = file.id;
					row.innerHTML = `${file.name} (${plupload.formatSize(file.size)}) <strong></strong>`;
					list.appendChild(row);
				});
				uploader.start();
			},
			UploadProgress: (up, file) => {
				document.querySelector(`#${file.id} strong`).innerHTML = `${file.percent}% Uploaded Successfully`;
				jQuery('#pick').hide();
			},
			Error: (up, err) => {
				console.error(err);
				var decodeerror = jQuery.parseJSON(err.response);
				list.innerHTML = `${decodeerror.info}`;
			}
		}
	});
	uploader.init();
};



/**
 * Select 2 for search and maping
 */
if(jQuery('.js-data-example-ajax')){
	jQuery('.js-data-example-ajax').select2({
		placeholder: "Search for an Item",
		minimumInputLength: 2,
		// tags: true,
		multiple: false,
		allowClear: true,
		tokenSeparators: [',', ' '],
		minimumResultsForSearch: 10,
		ajax: {
			url: MyAjax.ajaxurl,
			dataType: "json",
			type: "GET",
			data: function (params) {

				var queryParameters = {
					term: params.term,
					action: 'spintax_get_list_items',
					security: MyAjax.security_nonce
				}
				return queryParameters;
			},
			processResults: function (response) {
				return {
					results: response
				};
			},
			// cache: true
		}
	});
}

/**
 * Select 2 for IDS select
 */
jQuery('.filtered_posts_ids').each(function(){
	var select_type = jQuery(this).data('select-type');
	jQuery(this).select2({
		placeholder: "Search for an Item",
		// 		tags: false,
		// 		cache: false,
		minimumInputLength: 2,
		multiple: true,
		// 		allowClear: true,
		// 		tokenSeparators: [',', ' '],
		// 		minimumResultsForSearch: 10,
		ajax: {
			url: MyAjax.ajaxurl,
			dataType: "json",
			type: "GET",
			data: function (params) {

				var queryParameters = {
					term: params.term,
					data_type : select_type,
					action: 'spintax_get_list_items_for_exclude_id',
					security: MyAjax.security_nonce
				}
				return queryParameters;
			},
			processResults: function (response) {
				return {
					results: response
				};
			},
		}
	});	
});

// Create an empty array to store the option values
var optionValuespost = [];
jQuery('#exclude-post option').each(function() {
   optionValuespost.push(jQuery(this).val());
});
jQuery('#exclude-post').val(optionValuespost).trigger('change');

var optionValuespage = [];
jQuery('#exclude-page option').each(function() {
   optionValuespage.push(jQuery(this).val());
});
jQuery('#exclude-page').val(optionValuespage).trigger('change');

var optionValuesproduct = [];
jQuery('#exclude-product option').each(function() {
   optionValuesproduct.push(jQuery(this).val());
});
jQuery('#exclude-product').val(optionValuesproduct).trigger('change');

/* jQuery(document).on('click', '.add_more_cate', function () {
  let design1terms_count = parseInt(jQuery('#design1terms_count').val());
  let design1terms_count1 = design1terms_count + 1;
  jQuery('#category_base_card .card-body').append(`<div class="design1terms my-1">
  <select name="setting[multi][select_post_type][]" class="form-control h-auto posttype">
      <option value="">--Select Category Type--</option>
      <option value="category">Post Category</option>
      <option value="product_cat">Product Category</option>
  </select>
  <select class="form-control h-auto posttype_category">
  </select>
  <input type="hidden" name="setting[multi][posttype_category][]" value="" />
  <textarea name="setting[multi][category_spintax][]" class="form-control h-auto " rows="1"></textarea>
  <div class="bg-danger text-white rounded-circle remove_single_cate">
      <i class="fa-solid fa-minus"></i>
  </div>
</div>`);
  var itemslength = jQuery('.design1terms').length;
  if (itemslength == 1) {
    jQuery('.design1terms .remove_single_cate').hide();
  } else {
    jQuery('.design1terms .remove_single_cate').show();
  }
  jQuery('#design1terms_count').val(design1terms_count1);
  get_category_by_selected();
});

jQuery(document).on('click', '.remove_single_cate', function () {
  jQuery(this).closest('.design1terms').remove();
  var itemslength = jQuery('.design1terms').length;
  if (itemslength == 1) {
    jQuery('.design1terms .remove_single_cate').hide();
  } else {
    jQuery('.design1terms .remove_single_cate').show();
  }

  let design1terms_count = parseInt(jQuery('#design1terms_count').val());
  let design1terms_count1 = design1terms_count - 1;
  jQuery('#design1terms_count').val(design1terms_count1);
});
 */

/**
 * Select 2 for multiple category spintax
 */
/* function get_category_by_selected() {
  jQuery('.posttype_category').select2({
    placeholder: "Search for an category",
    // minimumInputLength: 2,
    // tags: true,
    multiple: true,
    tokenSeparators: [',', ' '],
    minimumResultsForSearch: 10,
    ajax: {
      url: MyAjax.ajaxurl,
      dataType: "json",
      type: "GET",
      data: function (params) {
        var queryParameters = {
          post_type: jQuery(this).prev().find(":selected").val(),
          action: 'spintax_get_post_type_category',
          security: MyAjax.security_nonce
        }
        return queryParameters;
      },
      processResults: function (response) {
        return {
          results: response
        };
      },
      // cache: true
    }
  }).on("select2:select select2:unselect", function (e) {
    var items = jQuery(this).val();
    jQuery(this).next().next().val(items);
  }).trigger('change');
} */