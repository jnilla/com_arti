(function($){
	$(document).ready(function(){
		// ---------------------------------------------
		// Initialization
		// ---------------------------------------------
		
        var formSignature = '';
		
		// ---------------------------------------------
		// Events
		// ---------------------------------------------
		
		// Hide commands if form state signature changes. 
        // This forces users to save the form before executing a command
        setInterval(()=>{
            var newFormSignature = calculateFormStateSignature();
            if(formSignature === ''){
                formSignature = newFormSignature;
            }

            if(formSignature !== newFormSignature){
                $('#save-changes-alert').removeClass('hidden');
                $('#commands-accordion').hide();
                formSignature = newFormSignature;
            }

            console.log(formSignature) // DEBUG
        }, 1000);
		
		
		// ---------------------------------------------
		// Functions
		// ---------------------------------------------
		
		// calculate form state signature
		function calculateFormStateSignature(){
            var signature = '';

			$inputs = $('#commands-tab-0 input, #commands-tab-0 select, #commands-tab-0 textarea, #commands-tab-1 input, #commands-tab-1 select, #commands-tab-1 textarea').each(function(){
                signature = signature+$(this).attr('id')+$(this).val(); 
            });

            return signature;
		}
		
	});	
})(jQuery);
