/**
 * custom js for child theme
 */
jQuery(document).ready(function($){

    'use strict';
    $('#NavMobileSelect').click(function(){
        $('#sub-navigation .site-navigation').slideToggle();
        $(this).toggleClass('active');
        return false;
    });

});