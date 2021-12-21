<?php
$code = app()->isDownForMaintenance() ? 'maintenance' : $exception->getStatusCode();
?>
<!DOCTYPE html>
<html lang="{!! app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{$code}}</title>
        <!-- Bootstrap core CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">
        <style>
            /* Error Page Inline Styles */
            body {
                padding-top: 20px;
            }
            /* Layout */
            .jumbotron {
                font-size: 21px;
                font-weight: 200;
                line-height: 2.1428571435;
                color: inherit;
                padding: 10px 0px;
            }
            /* Everything but the jumbotron gets side spacing for mobile-first views */
            .masthead, .body-content {
                padding-left: 15px;
                padding-right: 15px;
            }
            /* Main marketing message and sign up button */
            .jumbotron {
                text-align: center;
                background-color: transparent;
            }
            .jumbotron .btn {
                font-size: 21px;
                padding: 14px 24px;
            }
            /* Colors */
            .green {color:#5cb85c;}
            .orange {color:#f0ad4e;}
            .red {color:#d9534f;}
        </style>
    </head>
    <body>
        <!-- Error Page Content -->
        @foreach($exceptions as $exception)
        <div class="container">
            <!-- Jumbotron -->
            <div class="jumbotron">
                <h1>{{$exception->message}}</h1>
                <p class="lead">{{$exception->channel}} </p>
            </div>
        </div>
        <div class="container">
            <div class="body-content">
                <div class="row">
                  {{$exception->context}}
                </div>
            </div>
        </div>
        @endforeach
        <!-- End Error Page Content -->
        <!--Scripts-->
        <!-- jQuery library -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    </body>
</html>