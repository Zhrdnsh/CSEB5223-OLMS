
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>OLMS | Online Local Mart</title>

    <!-- External Stylesheet -->
    <link rel="stylesheet" href="css/style.css">

    <style>
        /* General Page Styling */
        * {
            box-sizing: border-box;
        }

        .home-page {
            min-height: 100vh;
            background: #fffaf5;
            font-family: Arial, sans-serif;
            color: #554b47;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        /* Navigation Bar */
        .home-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 8%;
            background: #ffffff;
            gap: 20px;
        }

        .home-logo {
            font-size: 24px;
            font-weight: bold;
            color: #ad687b;
        }

        .home-nav-links {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .home-nav-links a {
            color: #705c60;
            font-weight: bold;
            text-decoration: none;
        }

        .home-nav-links a:hover {
            color: #ad687b;
        }

        /* Keyboard Accessibility */
        .home-nav-links a:focus-visible,
        .home-button:focus-visible {
            outline: 3px solid #ad687b;
            outline-offset: 4px;
            border-radius: 5px;
        }

        /* Main Content */
        main {
            flex: 1;
            width: 100%;
        }

        /* Homepage Hero Section */
        .home-hero {
            max-width: 900px;
            margin: 90px auto;
            padding: 65px 35px;
            text-align: center;
            background: #fbe6ea;
            border-radius: 25px;
        }

        .home-tag {
            color: #a56b7b;
            font-size: 13px;
            letter-spacing: 2px;
            font-weight: bold;
        }

        .home-hero h1 {
            font-size: clamp(32px, 5vw, 52px);
            color: #76515d;
            margin: 20px 0;
        }

        .home-hero p {
            font-size: 17px;
            line-height: 1.7;
            color: #806d70;
        }

        /* Explore Products Button */
        .home-button {
            display: inline-block;
            background: #a5b99c;
            color: #ffffff;
            padding: 15px 30px;
            border-radius: 30px;
            margin-top: 25px;
            font-weight: bold;
            text-decoration: none;
            transition: background 0.2s ease;
        }

        .home-button:hover {
            background: #829a78;
        }

        /* Footer */
        .home-footer {
            text-align: center;
            color: #9a8986;
            padding: 25px;
        }

        /* Responsive Design */
        @media (max-width: 600px) {

            .home-nav {
                padding: 20px;
                flex-direction: column;
                align-items: flex-start;
            }

            .home-nav-links {
                gap: 15px;
            }

            .home-hero {
                margin: 35px 15px;
                padding: 45px 20px;
            }

            .home-hero p {
                font-size: 15px;
            }
        }
    </style>

</head>

<body class="home-page">

    <!-- Navigation Bar -->
    <nav class="home-nav" aria-label="Main navigation">

        <div class="home-logo">
            OLMS ♡
        </div>

        <div class="home-nav-links">

            <a href="index.php">
                Home
            </a>

            <a href="customer/customer_browse.php">
                Browse Products
            </a>

            <a href="admin/admin_login.php">
                Admin Login
            </a>

        </div>

    </nav>

    <!-- Main Homepage Content -->
    <main>

        <section class="home-hero">

            <div class="home-tag">
                YOUR FRIENDLY ONLINE LOCAL MART
            </div>

            <h1>
                Everyday essentials, made simple.
            </h1>

            <p>
                Welcome to OLMS! Discover everyday products,
                explore our local mart, and find what you need
                in just a few clicks.
            </p>

            <!-- Explore Products Button -->
            <a
                href="customer/customer_browse.php"
                class="home-button"
            >
                Explore Products ♡
            </a>

        </section>

    </main>

    <!-- Footer -->
    <footer class="home-footer">
        Made with ♡ | Online Local Mart System
    </footer>

</body>

</html>
