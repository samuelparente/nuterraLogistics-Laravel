<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site em Manutenção</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f8f9fa;
            font-family: Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .card {
            background: #fff;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 90%;
        }

        .card h1 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #333;
        }

        .card p {
            font-size: 1rem;
            color: #555;
            margin-bottom: 2rem;
        }

        .card a {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.3s;
        }

        .card a:hover {
            background: #0056b3;
        }

        .footer {
            margin-top: 2rem;
            font-size: 0.8rem;
            color: #999;
        }
    </style>
</head>
<body>

    <div class="card">
        <h1>Estamos em Manutenção</h1>
        <p>Voltamos em breve! Agradecemos a tua compreensão.</p>
        <a href="/akSK5hhcuUKen-V6OgMltc72OxJ8J8V8kYO9-IiWWPYZcVRaShMTdbBiYutPzx4M/login">Ir para o Login</a>
        <div class="footer">&copy; {{ date('Y') }} Nuterra</div>
    </div>

</body>
</html>
