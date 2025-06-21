<?php
/**
 * Premium Footer for IITG Travel Guide
 */
?>
<footer class="bg-dark text-white pt-5 pb-4 footer-fixed" >
    <div class="container">
        <div class="row justify-content-center">
            <!-- Brand Information -->
            <div class="col-lg-5 mb-4 text-center text-lg-start">
                <h3 class="text-uppercase mb-3" style="color: #1a5f7a;">IITG Travel Guide</h3>
                <p class="text-muted"></p>
                <div class="mt-4">
                    <a href="https://x.com/Panther07_03?t=W2yl5HVPbMoCNrTyMx1o7Q&s=08" class="text-white me-3" data-bs-toggle="tooltip" title="Twitter"><i class="fab fa-twitter fa-lg"></i></a>
                    <a href="https://github.com/Abhiram0703" class="text-white me-3" data-bs-toggle="tooltip" title="Github"><i class="fab fa-github fa-lg"></i></a>
                    <a href="https://www.instagram.com/abhiram_0703/" class="text-white me-3" data-bs-toggle="tooltip" title="Instagram"><i class="fab fa-instagram fa-lg"></i></a>
                    <a href="https://www.linkedin.com/in/abhiram-madam/" class="text-white" data-bs-toggle="tooltip" title="LinkedIn"><i class="fab fa-linkedin-in fa-lg"></i></a>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="col-lg-3 mb-4 text-center text-lg-start">
                <h5 class="text-uppercase mb-3" style="color: #1a5f7a;">Contact</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fas fa-envelope me-2"></i> a.madam@iitg.ac.in</li>
                    <li class="mb-2"><i class="fas fa-phone me-2"></i> +91 6305081229</li>
                    <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> IIT Guwahati, Assam 781039</li>
                </ul>
            </div>

            <!-- Legal Links -->
            <div class="col-lg-4 mb-4 text-center text-lg-start">
                <h5 class="text-uppercase mb-3" style="color: #1a5f7a;">Contributors</h5>
                <div class="d-flex flex-column flex-lg-row">
                    <a href="#" class="text-white text-decoration-none mb-2 me-lg-3">Abhiram</a>
                    <a href="#" class="text-white text-decoration-none mb-2 me-lg-3">230150015</a>
                    <a href="#" class="text-white text-decoration-none mb-2">DSAI Department</a>
                </div>
                <div class="d-flex flex-column flex-lg-row">
                    <a href="#" class="text-white text-decoration-none mb-2 me-lg-3">Ashmita</a>
                    <a href="#" class="text-white text-decoration-none mb-2 me-lg-3">230150014</a>
                    <a href="#" class="text-white text-decoration-none mb-2">DSAI Department</a>
                </div>
            </div>
        </div>

        <hr class="my-4 bg-light opacity-50">
        <div class="row align-items-center justify-content-center text-center">
    <div class="col-12">
        <p class="mb-0 text-muted">Crafted with <i class="fas fa-heart text-danger"></i> for the IITG Community</p>
    </div>
</div>
    </div>
</footer>

<!-- Back to Top Button -->
<button type="button" class="btn btn-primary btn-floating btn-lg rounded-circle" id="btn-back-to-top">
    <i class="fas fa-arrow-up"></i>
</button>

<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>

<!-- Footer Scripts -->
<script>
// Back to top button
$(document).ready(function(){
    // Show/hide button
    $(window).scroll(function() {
        if ($(this).scrollTop() > 200) {
            $('#btn-back-to-top').fadeIn();
        } else {
            $('#btn-back-to-top').fadeOut();
        }
    });

    // Smooth scroll to top
    $('#btn-back-to-top').click(function() {
        $('html, body').animate({scrollTop: 0}, 'smooth');
        return false;
    });

    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Position footer correctly - fixed solution
    function positionFooter() {
        var footerHeight = $('footer').outerHeight();
        var bodyHeight = $('body').height();
        var windowHeight = $(window).height();
        
        if (bodyHeight < windowHeight) {
            $('footer').addClass('footer-fixed');
            $('body').css('padding-bottom', footerHeight + 'px');
        } else {
            $('footer').removeClass('footer-fixed');
            $('body').css('padding-bottom', '0');
        }
    }
    
    // Initial positioning
    positionFooter();
    
    // Reposition on window resize
    $(window).resize(function() {
        positionFooter();
    });
});
</script>

<style>
/* Footer Styles */
footer {
    background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%);
    border-top: 3px solid #1a5f7a;
    width: 100%;
}

/* Fixed Footer CSS */
.footer-fixed {
    /* position: fixed; */
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 999;
}

#btn-back-to-top {
    position: fixed;
    bottom: 20px;
    right: 20px;
    display: none;
    width: 50px;
    height: 50px;
    z-index: 1000;
    box-shadow: 0 4px 12px rgba(0,0,0,0.25);
    transition: all 0.3s ease;
}

#btn-back-to-top:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.3);
}

footer a:hover {
    color: #1a5f7a !important;
    transition: color 0.3s ease;
}

.modal-content {
    border: 1px solid #1a5f7a;
}

/* Responsive adjustments */
@media (max-width: 992px) {
    footer .text-lg-start {
        text-align: center !important;
    }
    
    footer .d-flex.flex-lg-row {
        justify-content: center;
    }
}
footer .text-muted {
    color: #cccccc !important; /* or a light grey like #dddddd */
}
</style>