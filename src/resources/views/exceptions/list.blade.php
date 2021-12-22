<?php
$code = app()->isDownForMaintenance() ? 'maintenance' : 'error logs';
?>
<!DOCTYPE html>
<html lang="{!! app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $code }}</title>
    <link rel="stylesheet" href="//cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <style>
        body {
            margin: 2em;
        }

        h2 {
            margin: 1em 0;
        }

        /*
.collapse-accordion a[data-toggle=collapse] {display: block;padding:.75rem 1.25rem;}
*/
        .collapse-accordion .card a[data-toggle=collapse] {
            display: block;
            padding: .75rem 1.25rem;
        }

        .collapse-accordion .card-header {
            padding: 0;
        }

    </style>

</head>

<body>
    <div class="container">
        <div class="collapse-accordion" id="accordion2" role="tablist" aria-multiselectable="true">
            @foreach($exceptions as $exception)
            <div class="card">
                <div class="card-header" role="tab" id="headingOne1">
                    <h5 class="mb-0">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapseOne2" aria-expanded="true"
                            aria-controls="collapseOne">
                            {{$exception->message}}
                            <span class="float-right">See details</span>

                        </a>
                    </h5>
                </div>

                <div id="collapseOne2" class="collapse show" role="tabpanel" aria-labelledby="headingOne2">
                    <div class="card-block">
                        {{$exception->context}}
                    </div>
                </div>
            </div>
            @endforeach
            {{ $exceptions->links() }}
           
        </div>
    </div>

    <!-- End Error Page Content -->
    <!--Scripts-->
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable();
        });
    </script>
</body>

</html>
