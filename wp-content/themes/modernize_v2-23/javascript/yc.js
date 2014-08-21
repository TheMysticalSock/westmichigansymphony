
        var SlideWidth = 550;
        var SlideSpeed = 500;

        //jQuery(document).ready(function ($) {
           // SetNavigationDisplay();
			//})(jQuery);	
        
        function CurrentMargin() {
            var currentMargin = jQuery("#yc_slider-wrapper").css("margin-left");
            if (currentMargin == "auto") {
                currentMargin = 0;
            }
            return parseInt(currentMargin);
        }

        function SetNavigationDisplay() {
            var currentMargin = CurrentMargin();
            if (currentMargin == 0) {
                jQuery("#PreviousButton").hide();
            }
            else {
                jQuery("#PreviousButton").show();
            }
            var wrapperWidth = jQuery("#yc_slider-wrapper").width();
            if ((currentMargin * -1) == (wrapperWidth - SlideWidth)) {
                jQuery("#NextButton").hide();
            }
            else {
                jQuery("#NextButton").show();
            }
        }

        function NextSlide() {
            var newMargin = CurrentMargin() - SlideWidth;
            jQuery("#yc_slider-wrapper").animate({ marginLeft: newMargin }, SlideSpeed, function () { SetNavigationDisplay() });
        }

        function PreviousSlide() {
            var newMargin = CurrentMargin() + SlideWidth;
            jQuery("#yc_slider-wrapper").animate({ marginLeft: newMargin }, SlideSpeed, function () { SetNavigationDisplay() });
        } 
	
