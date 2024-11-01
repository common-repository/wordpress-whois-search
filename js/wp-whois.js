jQuery(document).ready(function() {
	jQuery("[id*=checkboxall]").click(function() {
		var status = this.checked;
		
		jQuery("[id*=checklist]").each(function() {
			this.checked = status;	
		});
	});
	
	jQuery("input[id*=checkinvert]").click(function() {
		this.checked = false;
	
		jQuery("input[id*=checklist]").each(function() {
			var status = this.checked;
			
			if (status == true) {
				this.checked = false;
			} else {
				this.checked = true;
			}
		});
	});
});

function wpwhoisform(number) {
	var formValues = jQuery("#wpwhoisform" + number).serialize();
	var updateDiv = "wpwhoisinside-" + number;
	
	jQuery("#" + updateDiv).html('<div style="text-align:center; margin:15px 0;" align="center"><img src="' + wpwhoisUrl + '/images/loading.gif" alt="loading" /></div>');
	jQuery('#wpwhoisresults' + number).hide();
	
	jQuery.post(ajaxurl + "action=wpwhoisform&ms=" + number, formValues, function(data) {
		var htmlCode = data;
		jQuery("#" + updateDiv).html(htmlCode);
	});	
}