<?php
$code = app()->isDownForMaintenance() ? 'maintenance' : 'error logs';
?>
<!DOCTYPE html>
<html lang="{!! app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $code }}</title>
    <link rel="stylesheet"  href="//cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
  
</head>

<body>
    <!-- Error Page Content -->

        <div class="container">
            <table id="myTable" class="display">
            <thead>
                <tr>
                    <th>Channel</th>
                    <th>Message</th>
                   
                </tr>
            </thead>
            <tbody>
                @foreach ($exceptions as $exception)
                <tr>
                <td>{{$exception->channel}}</td>
                <td>{{$exception->message}}</td>
               
                </tr>
                @endforeach
            </tbody>
        </table>

        </div>

    <!-- End Error Page Content -->
    <!--Scripts-->
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable();
        });
    </script>
</body>

</html>
