<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hello World</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f3f4f6;
            color: #111827;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        }

        h1 {
            margin: 0;
            font-size: clamp(2.5rem, 10vw, 5rem);
            font-weight: 800;
            letter-spacing: -0.04em;
        }

        h1 span {
            color: #f53003;
        }
    </style>
</head>
<body>
    <main>
        <h1>Hello <span>World</span></h1>
    </main>
</body>
</html>
