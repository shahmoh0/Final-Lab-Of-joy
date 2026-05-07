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
</head>

<body>

  <header class="siteHeader">
    <img src="images/LOGO-remove bg (1).png" alt="Lab of Joy Logo" class="logo">
    <p class="tagline"><strong>Every Gift Tells a Story</strong></p>
  </header>

 <nav class="navBar">

<a class="pill" href="/LabOfJoy/aljury/categories.php">Categories</a>
<a class="pill" href="/LabOfJoy/munira/box-customization.php">Box Customization</a>
<a class="pill active" href="about.php">About Us</a>
<a class="pill" href="/LabOfJoy/jana/cart.php">Cart</a>

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
      <p>Email: info@labofjoy.com</p>
      <p>Phone: +966 5 123 4567</p>
      <p>Website: www.labofjoy.com</p>
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
