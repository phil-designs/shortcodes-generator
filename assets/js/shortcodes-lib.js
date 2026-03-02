jQuery(document).ready(function($) {
	"use strict";
	/* TABS */
	$(".tabs").tabs();

	/*WOW.JS*/
	new WOW().init();
	
	/* ACCORDIONS */
	 $('.accordion').each(function(){
		 var $this = $(this);
            var toggle = $(this).hasClass('toggle') ? true : false,
            $sections = $(this).children('section'),
            $opened = $(this).data('opened') === '-1' ? null : $sections.eq(parseInt($(this).data('opened')));
            if($opened !== null){
                $opened.addClass('opened');
                $opened.children('.accordion-content').slideDown(0);
            }
            $(this).children('section').children('.accordion-title').click(function(){
                $this = $(this).parent();
                if(!toggle){
                    if($opened !== null){
                        $opened.removeClass('opened');
                        $opened.children('.accordion-content').stop().slideUp(300);
                    }
                }
                if($this.hasClass('opened') && toggle){
                    $this.removeClass('opened');
                    $this.children('.accordion-content').stop().slideUp(300);
                } else if(!$this.hasClass('opened')){
                    $opened = $this;
                    $this.addClass('opened');
                    $this.children('.accordion-content').stop().slideDown(300);
                }
            });
        });

	/* TOGGLES */
	$(".toggle").each( function () {
		var $this = $(this);
		if( $this.attr('data-id') === 'closed' ) {
			$this.accordion({ header: '.toggle-title', collapsible: true, active: false  });
		} else {
			$this.accordion({ header: '.toggle-title', collapsible: true});
		}

		$this.on('accordionactivate', function() {
			$this.accordion('refresh');
		});

		$(window).on('resize', function() {
			$this.accordion('refresh');
		});
	});
	
	/* ALERTS */
	$('.alert-close').on('click', function(){
			$(this).parent().fadeOut('slow', function(){
			});
		});	

});