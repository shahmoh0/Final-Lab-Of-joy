<?php
// Load helpers and protect page
require_once '../includes/functions.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>About Us - Lab of Joy</title>
  <link rel="stylesheet" href="web-project-css-about us (1).css">
  <link rel="stylesheet" href="/LabOfJoy/accessibility.css">
  <script src="/LabOfJoy/accessibility.js" defer></script>
</head>

<body>

  <header class="siteHeader">
    <img src="images/LOGO-remove bg (1).png" alt="Lab of Joy Logo" class="logo">
    <p class="tagline"><strong>Every Gift Tells a Story</strong></p>
  </header>

 <nav class="navBar">
<a class="pill" href="/LabOfJoy/aljury/categories.php">Categories</a>
<a class="pill" href="/LabOfJoy/munira/box-customization.php">Box Customization</a>
<a class="pill" href="/LabOfJoy/shahad/about.php">About Us</a>
<a class="pill" href="/LabOfJoy/jana/cart.php">🛒 Cart </a>

</nav>

  <main class="container">

    <section class="aboutSection">
      <h2>About Lab of Joy</h2>
      <p>
        Lab of Joy is an online gift store that offers perfumes, flowers, chocolates,
        and accessories. Our goal is to make every special occasion unforgettable
        through carefully selected and beautifully packaged gifts.
      </p>
    </section>

    <section class="aboutSection">
      <h2>Our Vision</h2>
      <p>
        Our vision is to become the leading online gift store that spreads happiness
        and joy through creative and high-quality gift experiences.
      </p>
    </section>

    <section class="aboutSection">
      <h2>Our Values</h2>
      <ul class="aboutList">
        <li>High quality products</li>
        <li>Creative gift packaging</li>
        <li>Customer satisfaction</li>
        <li>Fast and reliable delivery</li>
      </ul>
    </section>

    <section class="aboutSection">
      <h2>Why Choose Us</h2>
      <ul class="aboutList">
        <li>Unique gift collections</li>
        <li>Elegant packaging</li>
        <li>Affordable prices</li>
      </ul>
    </section>

    <section class="aboutSection">
      <h2>Frequently Asked Questions</h2>

      <details class="faqItem">
        <summary>Do you offer gift wrapping?</summary>
        <p>Yes, we provide elegant gift wrapping options.</p>
      </details>

      <details class="faqItem">
        <summary>How long does delivery take?</summary>
        <p>Delivery usually takes 2-3 business days.</p>
      </details>
    </section>

    <section class="aboutSection">
      <h2>Contact Us</h2>

      <div class="contactGrid">

        <!-- Address & contact details -->
        <div class="contactDetails">
          <div class="contactItem">
            <span class="contactIcon">📍</span>
            <div>
              <strong>Address</strong>
              <p>King Fahd Street, Al Jubail Industrial City<br>Eastern Province, Saudi Arabia</p>
            </div>
          </div>
          <div class="contactItem">
            <span class="contactIcon">📞</span>
            <div>
              <strong>Phone</strong>
              <p>+966 5 123 4567</p>
            </div>
          </div>
          <div class="contactItem">
            <span class="contactIcon">📧</span>
            <div>
              <strong>Email</strong>
              <p>info@labofjoy.com</p>
            </div>
          </div>
          <div class="contactItem">
            <span class="contactIcon">🌐</span>
            <div>
              <strong>Website</strong>
              <p>www.labofjoy.com</p>
            </div>
          </div>
          <div class="contactItem">
            <span class="contactIcon">⏰</span>
            <div>
              <strong>Working Hours</strong>
              <p>Sat – Thu: 9:00 AM – 10:00 PM<br>Friday: 4:00 PM – 10:00 PM</p>
            </div>
          </div>
        </div>

        <!-- Google Maps embed for Jubail, Saudi Arabia -->
        <div class="mapWrapper">
          <iframe
            title="Lab of Joy Location — Jubail, Saudi Arabia"
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d57868.45!2d49.6580!3d27.0046!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e35e5b5b5b5b5b5%3A0x0!2sJubail%2C+Saudi+Arabia!5e0!3m2!1sen!2ssa!4v1700000000000"
            width="100%"
            height="100%"
            style="border:0;"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
          </iframe>
        </div>

      </div>
    </section>

    <section class="aboutSection">
      <h2>Follow Us</h2>
      <div class="socialRow">
        <a href="#" class="socialLink">Instagram</a>
        <a href="#" class="socialLink">Twitter</a>
        <a href="#" class="socialLink">Snapchat</a>
        <a href="#" class="socialLink">TikTok</a>
      </div>
    </section>

  </main>

  <footer class="siteFooter">
    © Lab of Joy
  </footer>

</body>
</html>
