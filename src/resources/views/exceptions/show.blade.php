
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Integration error details</title>
<meta name="description" content="500 Internal Server Error">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
</head>
<body class="py-5" onload="javascript:loadDomain();">
<!-- Error Page Content -->
<div class="container">
    <div class="hero text-center my-4">
        <h1 class="display-5"><i class="bi bi-emoji-frown text-danger mx-3"></i></h1>
        <h1 class="display-5 fw-bold">{{ucfirst($exception->channel)}}</h1>
        <p class="lead">{{ucfirst($exception->message)}} - <em><span id="display-domain"></span></em>.
        </p>
    </div>

    <div class="content">
        <div class="row">
            <div class="col-md-12">
                @php
                $data=json_decode($exception->context);
                @endphp
                <div class="my-5 p-5 card">
                    <h3>Where to look?</h3>
                    <p class="fs-5"><?php dump(isset($data[0])?$data[0]:$exception->context) ?></p>
                </div>
                <div class="my-5 p-5 card">
                    <h3>Complete information</h3>
                    @php
                        $data=json_decode($exception->context);
                        $trace=isset($data[0]->file)?$data[0]->file:'';
                        $line=isset($data[0]->file)?$data[0]->file:'';
                    @endphp
                   {{dd($exception->context);}}
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function loadDomain() {
        var display = document.getElementById("display-domain");
        display.innerHTML = document.domain;
    }
    // CTA button actions
    function goToHomePage() {
        window.location = '/';
    }
    function reloadPage() {
        document.location.reload(true);
    }
</script>
</body>
</html>
