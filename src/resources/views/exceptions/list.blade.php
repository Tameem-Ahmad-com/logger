<?php
$code = app()->isDownForMaintenance() ? 'maintenance' : 'error logs';
?>
<!DOCTYPE html>
<html lang="{!! app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $code }}</title>
    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link href="//cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
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
        .masthead,
        .body-content {
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
        .green {
            color: #5cb85c;
        }

        .orange {
            color: #f0ad4e;
        }

        .red {
            color: #d9534f;
        }

    </style>
</head>

<body>
    <!-- Error Page Content -->

        <div class="container">
            <table id="myTable" class="display">
            <thead>
                <tr>
                    <th>Channel</th>
                    <th>Message</th>
                    <th>Exception</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($exceptions as $exception)
                <tr>
                <td>{{$exception->channel}}</td>
                <td>{{$exception->message}}</td>
                <td>{{$exception->context}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        </div>

    <!-- End Error Page Content -->
    <!--Scripts-->
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable();
        });
    </script>
</body>

</html>
