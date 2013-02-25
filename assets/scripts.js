/**
 * Theme Blvd Featured Videos
 */
 
jQuery(document).ready(function($){
	$('#section-_tb_fv_url').hide();
	$('#section-_tb_fv_replace input:radio:checked').each(function() {
		if( $(this).val() == 'true' )
		{
			$('#section-_tb_fv_url').show();
		}
	});
	$('#section-_tb_fv_replace input:radio').change(function(){
		if( $(this).val() == 'true' )
		{
			$('#section-_tb_fv_url').show();
		} 
		else 
		{
			$('#section-_tb_fv_url').hide();
		}
	});
});