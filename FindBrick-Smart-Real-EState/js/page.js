$(document).ready(function() {
    // Highlight property cards on hover for UX
    $('.property-card').hover(
        function() { $(this).addClass('shadow-lg'); },
        function() { $(this).removeClass('shadow-lg'); }
    );

    // Filter box focus effect
    $('.wireframe-filter-box select, .wireframe-filter-box input').focus(function() {
        $(this).css('box-shadow','0 0 8px #3ec85a88');
    }).blur(function() {
        $(this).css('box-shadow','none');
    });

    // Action buttons feedback
    $('.wireframe-action-btns .btn').click(function(e){
        if($(this).hasClass('btn-success')){
            alert('Enquiry sent! Our agent will contact you soon.');
        } else if($(this).hasClass('btn-outline-light')){
            alert('Booking started! Our team will reach out for confirmation.');
        }
        e.preventDefault();
    });
});