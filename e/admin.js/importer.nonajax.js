$(function()
{		
	$('#file_import_form input[type="file"]').change(function()
	{
		$(this).parents('form:first').submit();
	});
	
	$('#file_import_form').submit(function()
	{
		$('#file_import_form button[type="submit"]').prop('disabled', true);
		
		$('#uploadTarget').removeClass('visible');
		
		$('#messageUploadError').hide();
		$('#ImportSuccessMessage .destroyable').remove();
		$('#ImportSuccessMessage').removeClass('alert-error').addClass('alert-success').hide();
		
		$('#messagePreUpload').hide();
		$('#messageUploadStatus').slideDown();
		
		$('.help-guide').popover('destroy');
	});
	
	$('a[href="#report"]').click(function()
	{
		$('#uploadTarget').toggleClass('visible');
	});
});

function uploadFinish(result)
{	
	if (result.resultCode == 'ok' || result.resultCode == 'soft_soft_fail' || result.resultCode == 'soft_fail' || result.resultCode == 'import_failed')
	{
		$('#ImportSuccessMessage').prepend('<div class="destroyable">'+result.result+'</div>').slideDown();
		
		if (result.resultCode == 'ok' || result.resultCode == 'soft_soft_fail' || result.resultCode == 'soft_fail')
		{
			$('#file_import_form').fadeOut();
			$('#messageUploadStatus').hide();
			
			if (result.resultCode == 'soft_fail')
			{
				$('#ImportSuccessMessage').removeClass('alert-success');
			}
		}
		else if (result.resultCode == 'import_failed')
		{
			$('#ImportSuccessMessage').removeClass('alert-success').addClass('alert-error');
			
			$('#file_import_form button[type="submit"]').prop('disabled', false);
			$('#messageUploadStatus').hide();
		}
	}
	else if (result.resultCode == 'upload_failed')
	{			
		$('#messageUploadStatus').hide();
		$('#messageUploadError').html(result.result).show();
		
		$('#file_import_form button[type="submit"]').prop('disabled', false);
	}
}