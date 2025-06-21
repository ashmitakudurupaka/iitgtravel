<?php
include('./config/dbConnection.php');
include('./maininclude/header.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IITG Travel Guide - Smart Trip Planning for Students</title>
    
    <!-- Three.js Library for 3D Animations -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.min.js"></script>
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #1a5f7a;
            --secondary-color: #57C5B6;
            --accent-color: #159895;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }
        
        /* Video Background Styles */
        .remove-vid-marg {
            margin: 0;
            padding: 0;
            position: relative;
            height: 100vh;
            overflow: hidden;
        }
        
        .vid-parent {
            position: relative;
            height: 100vh;
            overflow: hidden;
        }
        
        video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .vid-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1;
        }
        
        .vid-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            z-index: 2;
            color: white;
            width: 80%;
        }
        
        .my-content {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        
        /* 3D Globe Container */
        #globe-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            opacity: 0.3;
        }
        
        /* Features Section */
        .features-section {
            padding: 5rem 0;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        
        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            height: 100%;
            border-left: 5px solid var(--primary-color);
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }
        
        .feature-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }
        
        /* Testimonials */
        .testimonial-section {
            padding: 5rem 0;
            background: var(--primary-color);
            color: white;
        }
        
        .testimonial-card {
            background: rgba(255,255,255,0.1);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
        }
        
        .testimonial-card:hover {
            background: rgba(255,255,255,0.2);
        }
        
        .testimonial-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid white;
        }
        
        /* Stats Section */
        .stats-section {
            padding: 5rem 0;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            color: white;
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        /* CTA Section */
        .cta-section {
            padding: 5rem 0;
            background: var(--light-color);
        }
        
        .cta-card {
            background: white;
            border-radius: 15px;
            padding: 3rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        /* Animation Classes */
        .float {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .my-content {
                font-size: 2rem;
            }
            
            .feature-icon {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Start Video Background with 3D Globe -->
    <div class="container-fluid remove-vid-marg">
        <div id="globe-container"></div>
        <div class="vid-parent">
            <video playsinline autoplay muted loop>
                <source src="videos/nature.mp4" type="video/mp4">
            </video>
            <div class="vid-overlay"></div>
        </div>
        <div class="vid-content">
            <h1 class="my-content">Welcome to IITG Travel Guide</h1>
            <small class="my-content">A smart planning platform for students</small><br>
            <a href="login.php" class="btn btn-primary btn-lg mt-3 pulse">Get Started</a>
        </div>
    </div>

    <!-- Project Overview Section -->
    <section class="py-5 bg-light" id="f231">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center" data-aos="fade-up">
                    <h2 class="display-4 mb-4">Your Ultimate Travel Companion</h2>
                    <p class="lead">
                        The IITG Travel Guide is a comprehensive platform designed specifically for IIT Guwahati students 
                        to plan, organize, and share their travel experiences. Our intelligent system helps you discover 
                        the best destinations, optimize your itinerary.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Key Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center" data-aos="fade-up">
                    <h2 class="display-4 mb-4">Key Features</h2>
                    <p class="lead">Plan your perfect trip from IIT Guwahati</p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <h3>Smart Trip Planning</h3>
                        <p>
                            Our intelligent algorithm suggests optimal routes and destinations based on your 
                            preferences, budget, and time constraints. It initially displays distance from IITG and then as you plan your trip it displays distances from last selected destination, making trip planning easier
                        </p>
                    </div>
                </div>
                
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-rupee-sign"></i>
                        </div>
                        <h3>Budget Management</h3>
                        <p>
                            Estimate costs for transportation, accommodation, and food. Our system provides 
                            realistic budget expectations based on student-friendly options and real data from 
                            previous travelers.
                        </p>
                    </div>
                </div>
                
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3>Community Reviews</h3>
                        <p>
                            Read authentic reviews from fellow IITG students. Get insights on the best times 
                            to visit, hidden gems, and student-friendly accommodations near popular destinations.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="400">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h3>Seasonal Recommendations</h3>
                        <p>
                            Discover the best places to visit each month. 
                        </p>
                    </div>
                </div>
                
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="500">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-route"></i>
                        </div>
                        <h3>Distance Calculator</h3>
                        <p>
                            Know exactly how far destinations are from IITG campus. Our integrated mapping system 
                            calculates travel distances and estimated transit times for various transportation modes.
                        </p>
                    </div>
                </div>
                
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="600">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-share-alt"></i>
                        </div>
                        <h3>Integrated Maps</h3>
                        <p>
                           Visualize your trip plan on maps to get visual clarity of routes and also helps in local discovery.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="cta-card" data-aos="zoom-in">
                        <h2 class="display-4 mb-4">Ready to Explore?</h2>
                        <p class="lead mb-4">
                            Join hundreds of IITG students who are already planning their next adventure with our 
                            comprehensive travel guide platform. It's free, easy to use, and designed specifically 
                            for student travelers.
                        </p>
                        <div class="d-flex justify-content-center">
                            <a href="register.php" class="btn btn-primary btn-lg mx-2">Sign Up Now</a>
                            <a href="login.php" class="btn btn-outline-primary btn-lg mx-2">Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3D Globe Script -->
    <script>
        // Three.js Globe Animation
        document.addEventListener('DOMContentLoaded', function() {
            // Set up the scene
            const scene = new THREE.Scene();
            const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
            const renderer = new THREE.WebGLRenderer({ alpha: true });
            renderer.setSize(window.innerWidth, window.innerHeight);
            document.getElementById('globe-container').appendChild(renderer.domElement);
            
            // Create a globe
            const geometry = new THREE.SphereGeometry(5, 32, 32);
            const texture = new THREE.TextureLoader().load('images/earth_texture.jpg');
            const material = new THREE.MeshBasicMaterial({ 
                map: texture,
                transparent: true,
                opacity: 0.7
            });
            const globe = new THREE.Mesh(geometry, material);
            scene.add(globe);
            
            // Add ambient light
            const ambientLight = new THREE.AmbientLight(0x404040);
            scene.add(ambientLight);
            
            // Add directional light
            const directionalLight = new THREE.DirectionalLight(0xffffff, 1);
            directionalLight.position.set(1, 1, 1);
            scene.add(directionalLight);
            
            // Position camera
            camera.position.z = 10;
            
            // Add controls
            const controls = new THREE.OrbitControls(camera, renderer.domElement);
            controls.enableZoom = false;
            controls.enablePan = false;
            controls.autoRotate = true;
            controls.autoRotateSpeed = 0.5;
            
            // Handle window resize
            window.addEventListener('resize', function() {
                camera.aspect = window.innerWidth / window.innerHeight;
                camera.updateProjectionMatrix();
                renderer.setSize(window.innerWidth, window.innerHeight);
            });
            
            // Animation loop
            function animate() {
                requestAnimationFrame(animate);
                controls.update();
                renderer.render(scene, camera);
            }
            
            animate();
        });
    </script>
    
    <!-- AOS Animation Initialization -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            mirror: false
        });
    </script>
    
    <!-- Font Awesome for Icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>

<?php 
include('./maininclude/footer.php'); 
?>