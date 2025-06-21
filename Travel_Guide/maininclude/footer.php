</body>
<!-- Start Contact Us -->
<div class="container mt-5 py-4" id="Contact">
  <h2 class="text-center mb-4 contact-heading">Connect With Us</h2>
  <div class="row justify-content-center">
    <div class="col-lg-8 col-md-7" id="f232">
      <div class="contact-card shadow-lg">
        <form action="process_contact.php" method="post" class="contact-form">
          <div class="form-group floating-label">
            <input type="text" class="form-control" name="name" id="name" placeholder=" " required>
            <label for="name">Your Name</label>
            <div class="underline"></div>
          </div>
          
          <div class="form-group floating-label">
            <input type="text" class="form-control" name="roll" id="roll" placeholder=" ">
            <label for="roll">IITG Roll Number (Optional)</label>
            <div class="underline"></div>
          </div>
          
          <div class="form-group floating-label">
            <select class="form-control" name="query_type" id="query_type" required>
              <option value="" disabled selected></option>
              <option value="travel_suggestion">Travel Suggestion</option>
              <option value="bug_report">Bug Report</option>
              <option value="feature_request">Feature Request</option>
              <option value="general">General Inquiry</option>
            </select>
            <label for="query_type">Query Type</label>
            <div class="underline"></div>
          </div>
          
          <div class="form-group floating-label">
            <input type="text" class="form-control" name="subject" id="subject" placeholder=" " required>
            <label for="subject">Subject</label>
            <div class="underline"></div>
          </div>
          
          <div class="form-group floating-label">
            <input type="email" class="form-control" name="email" id="email" placeholder=" " required>
            <label for="email">IITG Email Address</label>
            <div class="underline"></div>
          </div>
          
          <div class="form-group floating-label">
            <textarea class="form-control" name="message" id="message" placeholder=" " style="height:150px;" required></textarea>
            <label for="message">How can we help you?</label>
            <div class="underline"></div>
          </div>
          
          <div class="form-group">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter" checked>
              <label class="form-check-label" for="newsletter">
                Subscribe to travel updates and campus notifications
              </label>
            </div>
          </div>
          
          <div class="text-center">
            <button class="btn btn-send" type="submit" name="submit">
              <span class="btn-text">Send Message</span>
              <i class="fas fa-paper-plane btn-icon"></i>
            </button>
          </div>
        </form>
      </div>
    </div>
    
    <div class="col-lg-4 col-md-5 mt-md-0 mt-4">
      <div class="info-card shadow-lg h-100">
        <div class="info-content p-4 text-center">
          <div class="info-icon mb-3">
            <img src="logo.png" alt="IITG Travel guide Logo" style="height: 80px;">
          </div>
          <h3 class="info-title">DBMS Project by :</h3>
          <div class="info-details mt-4">
            <p class="info-item">
              <i class="fas fa-map-marker-alt mr-2"></i>
              Abhiram
            </p>
            <p class="info-item">
              <i class="fas fa-envelope mr-2"></i>
              230150015
            </p>
            <p class="info-item">
              <i class="fas fa-phone-alt mr-2"></i>
              a.madam@iitg.ac.in
            </p>
          </div>
          <div class="info-details mt-4">
            <p class="info-item">
              <i class="fas fa-map-marker-alt mr-2"></i>
              Ashmita
            </p>
            <p class="info-item">
              <i class="fas fa-envelope mr-2"></i>
              230150014
            </p>
            <p class="info-item">
              <i class="fas fa-phone-alt mr-2"></i>
              a.kudurpaka@iitg.ac.in
            </p>

        </div>
      </div>
    </div>
  </div>
</div>

<style>
/* Contact Section Styling */
.contact-heading {
  font-size: 2.5rem;
  color: #2c3e50;
  position: relative;
  padding-bottom: 15px;
  font-weight: 700;
  background: linear-gradient(90deg, #006a4e, #1a5f7a);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

/* Contact Card */
.contact-card {
  background: white;
  border-radius: 15px;
  padding: 30px;
  transition: all 0.3s ease;
  border: none;
  height: 100%;
  background-color: #f8f9fa;
  border-left: 5px solid #006a4e;
}

.contact-card:hover {
  box-shadow: 0 15px 30px rgba(0,0,0,0.1);
  transform: translateY(-5px);
}

/* Info Card */
.info-card {
  background: linear-gradient(135deg, #006a4e, #1a5f7a);
  border-radius: 15px;
  color: white;
  transition: all 0.3s ease;
  border-right: 5px solid #f8f9fa;
}

.info-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 30px rgba(0,0,0,0.1);
}

.info-content {
  display: flex;
  flex-direction: column;
  justify-content: center;
  height: 100%;
}

.info-icon {
  color: rgba(255,255,255,0.8);
}

.info-title {
  font-weight: 700;
  margin-bottom: 20px;
  position: relative;
  padding-bottom: 10px;
}

.info-title:after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 50px;
  height: 2px;
  background: white;
}

.info-item {
  margin-bottom: 15px;
  font-size: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.quick-links {
  border-top: 1px solid rgba(255,255,255,0.2);
  padding-top: 15px;
}

.quick-links h5 {
  font-weight: 600;
}

.quick-link {
  display: block;
  color: white;
  margin-bottom: 8px;
  text-decoration: none;
  transition: all 0.3s ease;
  font-size: 14px;
}

.quick-link:hover {
  color: #b3e0ff;
  text-decoration: underline;
}

.social-links {
  display: flex;
  justify-content: center;
  gap: 15px;
  margin-top: 20px;
}

.social-icon {
  color: white;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: rgba(255,255,255,0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.social-icon:hover {
  background: white;
  color: #006a4e;
  transform: translateY(-3px);
}

/* Floating Label Form */
.form-group {
  position: relative;
  margin-bottom: 30px;
}

.floating-label label {
  position: absolute;
  top: 15px;
  left: 15px;
  color: #7f8c8d;
  transition: all 0.3s ease;
  pointer-events: none;
  font-size: 16px;
}

.floating-label .form-control {
  padding: 15px;
  border: none;
  border-radius: 0;
  background-color: transparent;
  border-bottom: 2px solid #ecf0f1;
  font-size: 16px;
  box-shadow: none;
}

.floating-label select.form-control {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
}

.floating-label .form-control:focus {
  outline: none;
  box-shadow: none;
  border-color: transparent;
}

.floating-label .form-control:focus + label,
.floating-label .form-control:not(:placeholder-shown) + label {
  top: -15px;
  left: 0;
  font-size: 12px;
  color: #006a4e;
}

.underline {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 0;
  height: 2px;
  background: linear-gradient(90deg, #006a4e, #1a5f7a);
  transition: width 0.4s ease;
}

.form-control:focus ~ .underline {
  width: 100%;
}

/* Send Button Styling */
.btn-send {
  position: relative;
  padding: 12px 30px;
  border: none;
  background: linear-gradient(45deg, #006a4e, #1a5f7a);
  color: white;
  border-radius: 50px;
  font-size: 16px;
  font-weight: 600;
  overflow: hidden;
  transition: all 0.3s ease;
  box-shadow: 0 5px 15px rgba(0, 106, 78, 0.3);
}

.btn-send:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(0, 106, 78, 0.4);
}

.btn-send:active {
  transform: translateY(1px);
}

.btn-text {
  display: inline-block;
  transition: all 0.3s ease;
}

.btn-icon {
  position: absolute;
  right: 20px;
  top: 50%;
  transform: translateY(-50%);
  opacity: 0;
  transition: all 0.3s ease;
}

.btn-send:hover .btn-text {
  transform: translateX(-10px);
}

.btn-send:hover .btn-icon {
  opacity: 1;
  right: 30px;
}

/* Responsive Adjustments */
@media (max-width: 992px) {
  .contact-heading {
    font-size: 2.2rem;
  }
}

@media (max-width: 768px) {
  .contact-card, .info-card {
    padding: 20px;
  }
  
  .contact-heading {
    font-size: 2rem;
  }
  
  .info-item {
    font-size: 14px;
  }
}

/* Form checkbox styling */
.form-check-input:checked {
  background-color: #006a4e;
  border-color: #006a4e;
}

.form-check-label {
  font-size: 14px;
  color: #555;
}
</style>
<!-- End Contact Us -->
</html>