<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>About Us | FreshMart</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background: #f8fff9;
        color: #333;
        line-height: 1.7;
    }

    /* HERO SECTION */
    .hero {
        background: linear-gradient(135deg, #28a745, #1e7e34);
        color: white;
        padding: 80px 20px;
        text-align: center;
    }

    .hero h1 {
        font-size: 42px;
        margin-bottom: 10px;
    }

    .hero p {
        font-size: 18px;
        max-width: 700px;
        margin: auto;
        opacity: 0.95;
    }

    /* MAIN CONTENT */
    .container {
        max-width: 1100px;
        margin: auto;
        padding: 60px 20px;
    }

    /* ABOUT SECTION */
    .about-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        align-items: center;
    }

    .about-text h2 {
        color: #218838;
        margin-bottom: 15px;
        font-size: 30px;
    }

    .about-text p {
        color: #555;
        margin-bottom: 15px;
    }

    .about-image {
        text-align: center;
    }

    .about-image img {
        max-width: 100%;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    /* VALUES SECTION */
    .values {
        margin-top: 80px;
        text-align: center;
    }

    .values h2 {
        font-size: 32px;
        color: #218838;
        margin-bottom: 40px;
    }

    .values-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 25px;
    }

    .value-card {
        background: white;
        padding: 30px 20px;
        border-radius: 16px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        transition: 0.3s;
    }

    .value-card:hover {
        transform: translateY(-6px);
    }

    .value-card span {
        font-size: 40px;
        display: block;
        margin-bottom: 15px;
    }

    .value-card h3 {
        margin-bottom: 10px;
        color: #28a745;
    }

    /* CTA SECTION */
    .cta {
        margin-top: 80px;
        background: #e8f5e9;
        padding: 50px 20px;
        text-align: center;
        border-radius: 20px;
    }

    .cta h2 {
        font-size: 30px;
        color: #218838;
        margin-bottom: 10px;
    }

    .cta p {
        max-width: 700px;
        margin: 15px auto;
        color: #555;
    }

    .cta a {
        display: inline-block;
        margin-top: 20px;
        padding: 12px 30px;
        background: #28a745;
        color: white;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: 0.3s;
    }

    .cta a:hover {
        background: #1e7e34;
    }

    /* FOOTER */
    footer {
        margin-top: 80px;
        background: #218838;
        color: white;
        text-align: center;
        padding: 20px;
    }

    /* RESPONSIVE */
    @media(max-width:768px) {
        .hero h1 {
            font-size: 32px
        }

        .about-grid {
            grid-template-columns: 1fr
        }
    }
    </style>
</head>

<body>

    <!-- HERO -->
    <section class="hero">
        <h1>About FreshMart</h1>
        <p>Your trusted online grocery partner — delivering freshness, quality, and care to your doorstep.</p>
    </section>

    <!-- ABOUT -->
    <section class="container">
        <div class="about-grid">
            <div class="about-text">
                <h2>Who We Are</h2>
                <p>
                    FreshMart is an online grocery store built with one simple mission —
                    to make daily shopping easier, faster, and more reliable.
                </p>
                <p>
                    From fresh foods to personal care and household essentials,
                    we carefully select products that meet high quality standards
                    while keeping prices affordable.
                </p>
                <p>
                    We believe shopping should be stress-free, convenient,
                    and accessible to everyone.
                </p>
            </div>

            <div class="about-image">
                <img src="https://images.unsplash.com/photo-1604719312566-8912e9227c6a" alt="Fresh grocery shopping">
            </div>
        </div>

        <!-- VALUES -->
        <div class="values">
            <h2>Our Core Values</h2>

            <div class="values-grid">
                <div class="value-card">
                    <span>🥦</span>
                    <h3>Freshness</h3>
                    <p>We ensure high-quality and fresh products for every customer.</p>
                </div>

                <div class="value-card">
                    <span>🤝</span>
                    <h3>Trust</h3>
                    <p>Transparent pricing and reliable service you can depend on.</p>
                </div>

                <div class="value-card">
                    <span>🚚</span>
                    <h3>Convenience</h3>
                    <p>Fast delivery and easy shopping from anywhere, anytime.</p>
                </div>

                <div class="value-card">
                    <span>💚</span>
                    <h3>Care</h3>
                    <p>Customer satisfaction and care are always our top priority.</p>
                </div>
            </div>
        </div>

        <!-- CTA -->
        <div class="cta">
            <h2>Shop Smart. Live Fresh.</h2>
            <p>
                Join thousands of happy customers who trust FreshMart
                for their daily needs.
            </p>
            <a href="index.php">Start Shopping</a>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <p>© 2025 FreshMart. All rights reserved. Created by Mahbuba</p>
    </footer>

</body>

</html>