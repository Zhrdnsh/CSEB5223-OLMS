
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OLMS | Online Local Mart</title>
    <link rel="stylesheet" href="css/style.css">

    <style>
        .home-page {
            min-height: 100vh;
            background: #fffaf5;
            font-family: Arial, sans-serif;
            color: #554b47;
        }

        .home-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 8%;
            background: #ffffff;
            border-radius: 0;
            margin: 0;
        }

        .home-logo {
            font-size: 24px;
            font-weight: bold;
            color: #ad687b;
        }

        .home-nav a {
            color: #705c60;
            font-weight: bold;
            margin-left: 20px;
        }

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

        .home-button {
            display: inline-block;
            background: #a5b99c;
            color: #ffffff;
            padding: 15px 30px;
            border-radius: 30px;
            margin-top: 25px;
            font-weight: bold;
        }

        .home-button:hover {
            background: #829a78;
        }

        .home-footer {
            text-align: center;
            color: #9a8986;
            padding: 25px;
        }

        @media (max-width: 600px) {
            .home-nav {
                padding: 20px;
                flex-wrap: wrap;
                gap: 15px;
            }

            .home-hero {
                margin: 35px 15px;
                padding: 45px 20px;
            }
        }
    </style>
</head>

<body class="home-page">

    <nav class="home-nav">
        <div class="home-logo">OLMS ♡</div>

        <div>
            <a href="index.php">Home</a>
            <a href="product/customer_browse.php">Browse Products</a>
            <a href="product/admin_login.php">Admin Login</a>
        </div>
    </nav>

    <main>
        <section class="home-hero">

            <div class="home-tag">
                YOUR FRIENDLY ONLINE LOCAL MART
            </div>

            <h1>Everyday essentials, made simple.</h1>

            <p>
                Welcome to OLMS! Discover everyday products,
                explore our local mart, and find what you need
                in just a few clicks.
            </p>

            <a href="product/customer_browse.php"
               class="home-button">
                Explore Products ♡
            </a>

        </section>
    </main>

    <footer class="home-footer">
        Made with ♡ | Online Local Mart System
    </footer>

</body>
</html>
