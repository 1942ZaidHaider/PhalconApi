<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PATH</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Oldenburg&display=swap');
        body {
            background: url("https://i.pinimg.com/originals/b9/80/cb/b980cb9c44216082799dfc463c0c50d5.jpg");
            background-repeat: repeat;
        }

        #main {
            backdrop-filter: blur(2px);
            width: 50%;
            background-color: rgba(64, 100, 127, 0.5);
            border: solid 2px #00e0ff;
            border-radius: 10px;
            padding: 1em;
            color: white;
            font-family: 'Oldenburg', 'cursive';
        }

        a {
            text-decoration: none;
            font-weight: bolder;
            color: white;
        }

        div.link {
            backdrop-filter: blur(10px);
            display: inline-block;
            margin: 0.5em;
            padding: 10px;
            border: outset 2px #00e0ff;
            border-radius: 5px;
            /* background-color: #222222; */
        }

        @media (max-width: 1080px) {
            #main {
                width: 80%;
                margin: auto;
            }
        }
    </style>
</head>

<body>
    <div id="main">
        <h1>Choose your path</h1>
        <div class='link'>
            <a href='/api'>API</a>
        </div>
        <div class='link'>
            <a href='/app'>Website</a>
        </div>
        <div class='link'>
            <a href='/frontend'>Frontend</a>
        </div>
    </div>
</body>

</html>