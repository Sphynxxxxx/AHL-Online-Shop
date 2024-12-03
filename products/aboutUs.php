<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #F6EEE0;
            color: #2D1D1D;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #2D1D1D;
            color: #F6EEE0;
            padding: 30px 20px;
            text-align: center;
            display: flex;
            justify-content: space-between; 
            align-items: center; 
        }

        header h1 {
            font-size: 2.5em;
            margin: 0;
            flex-grow: 1; 
            text-align: center;
        }

        .btn-back-dashboard {
            text-decoration: none;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 30px;
            display: inline-flex; 
            align-items: center;
            gap: 10px; 
            transition: background-color 0.3s ease;
        }

        .btn-back-dashboard i {
            font-size: 30px; 
        }

        .btn-back-dashboard:hover {
            color: #0056b3;
        }
        section {
            padding: 40px 20px;
            text-align: center;
            max-width: 1000px;
            margin: 0 auto;
        }

        section h2 {
            font-size: 2em;
            margin-bottom: 20px;
            color: #2D1D1D;
        }

        section p {
            font-size: 1.2em;
            line-height: 1.6;
            margin-bottom: 30px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .team {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 50px;
        }

        .team-member {
            background-color: #2D1D1D;
            color: #F6EEE0;
            padding: 20px;
            border-radius: 10px;
            width: 250px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .team-member img {
            width: 100%;
            border-radius: 50%;
            max-width: 150px;
            margin-bottom: 20px;
        }

        .team-member h3 {
            margin: 10px 0;
            font-size: 1.5em;
        }

        .team-member p {
            font-size: 1em;
            margin-bottom: 10px;
        }

        footer {
            background-color: #2D1D1D;
            color: #F6EEE0;
            text-align: center;
            padding: 20px;
            margin-top: 50px;
        }

        footer a {
            color: #F6EEE0;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header>
        <a href="customer.php" class="btn-back-dashboard"><i class="fa-solid fa-house"></i></a>
        <h1>About Us</h1>
    </header>

    <section>
        <h2>AHL Online Shop</h2>
        <p>
        Our dedicated administrative team at AHL School Supplies Website brings best experience in e-commerce, logistics, and customer service to ensure a seamless and efficient shopping experience.  We're committed to providing exceptional support to our customers and maintaining the highest standards of quality and service.  Our team works tirelessly behind the scenes to manage inventory, process orders, and handle customer inquiries, ensuring that your experience with AHL School Supplies Website is smooth and enjoyable.  We're passionate about education and dedicated to providing students and educators with the best possible resources.
        </p>

        <h2>Contact Us</h2>
        <p>09515152433</p>
    </section>

    <section>
        <h2>Meet the Team</h2>

        <div class="team">
            <div class="team-member">
                <img src="" alt="Team Member 1">
                <h3>Name1</h3>
                <p>Position</p>
                <p>Small Info About your self</p>
            </div>

            <div class="team-member">
                <img src="" alt="Team Member 2">
                <h3>Name1</h3>
                <p>Position</p>
                <p>Small Info About your self</p>
            </div>

            <div class="team-member">
                <img src="" alt="Team Member 3">
                <h3>Name1</h3>
                <p>Position</p>
                <p>Small info About your self</p>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; 2024 AHL Online Shop. All rights reserved.</p>
        <p><a href="privacy-policy.html">Privacy Policy</a> | <a href="terms-of-service.html">Terms of Service</a></p>
    </footer>
</body>
</html>
