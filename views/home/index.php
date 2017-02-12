<html>
    <head>
        <title>Home</title>
        <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
        <link href="/public/css/main.css" rel="stylesheet">
    </head>
    <body>
        <h1 class="textCenter title">Welcome Home !</h1>
        <div class="block center">
            <h2>Ip</h2>
            <p>Your @ip : <?=$ip;?></p>
        </div>
        <div class="block center">
            <h2>Uri</h2>
            <p>Path : <?=$uri;?></p>
        </div>
        <div class="block center">
            <h2>Params</h2>
            <?= '<pre>' . print_r($params, true) . '</pre>'; ?>
        </div>
    </body>
</html>

