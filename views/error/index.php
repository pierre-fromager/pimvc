<html>
    <head>
        <title>Error</title>
        <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
        <link href="/public/error.css" rel="stylesheet">
    </head>
    <body>
        <h1 class="textCenter title">Oops!</h1>
        <div class="block center">
            <h2>Origin</h2>
            <ul>
                <li>Controller : <?= $controller; ?></li>
                <li>Action : <?= $action; ?></li>
            </ul>
        </div>
        <div class="block center">
            <h2>Errors</h2>
            <ul>
                <?php
                foreach ($errors as $error) {
                    echo '<li>[ ' . $error['code']
                        . ' ]&nbsp;:&nbsp;'
                        . $error['message'] . '</li>';
                }
                ?>
            </ul>
        </div>
        <div class="block center">
            <h2>Request</h2>
            <?= '<pre>' . print_r($request, true) . '</pre>'; ?>
        </div>
    </body>
</html>