<!DOCTYPE html>
<html>
    <head>
        <title>Findaloo</title>

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                display: table;
                font-weight: 100;
                font-family: 'Lato';
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 96px;
            }
            .top {    
                position: absolute;
                top: 10px;
                text-align: center;
            }
            .bottom {    
                position: absolute;
                bottom: 10px;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div class="container">          
            <div class="top">    
                <img src="{{url('img/EyantraLogoLarge.png')}}" width="70%" height="70%">
            </div>
                <div class="title">Get Lucky Draw</div>
                <div class="field">
                <form method="POST" action="{{route('luckyDrawResult')}}">
                    <button type="submit" class="btn btn-primary">Draw</button>
                </form>
                @if(isset($data))
                    <h2>{{$data}} </h2>
                @endif
                </div>
                <div class="bottom">
                    <img src="{{url('img/Illustration.png')}}" width="60%"> 
                </div>
        </div>
    </body>
</html>
