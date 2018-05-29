// JavaScript Document
$( function() {
	var max_price = $('#max_price').val(); 
	$( "#slider" ).slider({
		range: true,
		min: 1,
		max: max_price,
		values: [ 1, max_price ],
		slide: function( event, ui ) {
			$( "#amount_start" ).val(ui.values[ 0 ]);
			$( "#amount_end" ).val(ui.values[ 1 ]);
		},
		change: function( event, ui ) {
			$( "#amount_start" ).val(ui.values[ 0 ]);
			$( "#amount_end" ).val(ui.values[ 1 ]);	
		}
    });
	$( "#amount_start" ).val( $( "#slider" ).slider( "values", 0 ) );
	$( "#amount_end" ).val( $( "#slider" ).slider( "values", 1 ) );
	  
	$(document).on('click', '#search_books', function (){
		var book_name = $('#book_name').val();
		var author = $('#author').val();
		var publisher = $('#publisher').val();
		var rating = $('#rating').val();
		var price_start = $('#amount_start').val();
		var price_end = $('#amount_end').val();
		var pagination = '';
				
		var params = {"book_name":book_name,"author":author,"publisher":publisher,"rating":rating,"price_start":price_start,"price_end":price_end, "pagination":pagination, "action":"get_book_list"}  
		
		ajax_book_list(params);						
		
	});
	
	$(document).on('click', '.pagination a', function (){
		var book_name = $('#book_name').val();
		var author = $('#author').val();
		var publisher = $('#publisher').val();
		var rating = $('#rating').val();
		var price_start = $('#amount_start').val();
		var price_end = $('#amount_end').val();	
		var pagination = $(this).data('pageno');	
		
		var params = {"book_name":book_name,"author":author,"publisher":publisher,"rating":rating,"price_start":price_start,"price_end":price_end, "pagination":pagination, "action":"get_book_list"}     
		
		ajax_book_list(params);			
		
	});

	function ajax_book_list(params) {
		$.post(ajax_object.ajaxurl, params, function(data){
			if(data.trim() != 'error') {              
				$('#books_list').html(data);
				$('#books_list').show();
			} else {
				$('#books_list').html('<p>No data found, please try again!</p>');                
                return false;
            }
		});
	}
});