<!DOCTYPE html>
<html>

<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <style type="text/css">
        @media screen {
            @font-face {
                font-family: 'Lato';
                font-style: normal;
                font-weight: 400;
                src: local('Lato Regular'), local('Lato-Regular'), url(https://fonts.gstatic.com/s/lato/v11/qIIYRU-oROkIk8vfvxw6QvesZW2xOQ-xsNqO47m55DA.woff) format('woff');
            }

            @font-face {
                font-family: 'Lato';
                font-style: normal;
                font-weight: 700;
                src: local('Lato Bold'), local('Lato-Bold'), url(https://fonts.gstatic.com/s/lato/v11/qdgUG4U09HnJwhYI-uK18wLUuEpTyoUstqEm5AMlJo4.woff) format('woff');
            }

            @font-face {
                font-family: 'Lato';
                font-style: italic;
                font-weight: 400;
                src: local('Lato Italic'), local('Lato-Italic'), url(https://fonts.gstatic.com/s/lato/v11/RYyZNoeFgb0l7W3Vu1aSWOvvDin1pK8aKteLpeZ5c0A.woff) format('woff');
            }

            @font-face {
                font-family: 'Lato';
                font-style: italic;
                font-weight: 700;
                src: local('Lato Bold Italic'), local('Lato-BoldItalic'), url(https://fonts.gstatic.com/s/lato/v11/HkF_qI1x_noxlxhrhMQYELO3LdcAZYWl9Si6vvxL-qU.woff) format('woff');
            }
        }

        table {
            width: 750px;
            border-collapse: collapse;
            margin: 50px auto;
        }

        /* Zebra striping */
        tr:nth-of-type(odd) {
            background: #eee;
        }

        th {
            background: #3498db;
            color: white;
            font-weight: bold;
        }

        td,
        th {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
            font-size: 18px;
        }

        /*
Max width before this PARTICULAR table gets nasty
This query will take effect for any screen smaller than 760px
and also iPads specifically.
*/
        @media only screen and (max-width: 760px),
        (min-device-width: 768px) and (max-device-width: 1024px) {

            table {
                width: 100%;
            }

            /* Force table to not be like tables anymore */
            table,
            thead,
            tbody,
            th,
            td,
            tr {
                display: block;
            }

            /* Hide table headers (but not display: none;, for accessibility) */
            thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }

            tr {
                border: 1px solid #ccc;
            }

            td {
                /* Behave  like a "row" */
                border: none;
                border-bottom: 1px solid #eee;
                position: relative;
                padding-left: 50%;
            }

            td:before {
                /* Now like a table header */
                position: absolute;
                /* Top/left values mimic padding */
                top: 6px;
                left: 6px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
                /* Label the data */
                content: attr(data-column);

                color: #000;
                font-weight: bold;
            }

        }


        /* ANDROID CENTER FIX */
        div[style*="margin: 16px 0;"] {
            margin: 0 !important;
        }

    </style>
</head>

<body style="background-color: #e5e7eb; margin: 0 !important; padding: 0 !important;">
    <!-- HIDDEN PREHEADER TEXT -->
    <div
        style="display: none; font-size: 1px; color: #fefefe; line-height: 1px; font-family: 'Lato', Helvetica, Arial, sans-serif; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;">
        This email is provide you reporting about integration projects status.
    </div>
    <table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
        <table bgcolor="white">
            <table bgcolor="white">
                <tr>
                    <td style="text-align:center">
                        <h1 style="align-content:center">{{ ucfirst(config('app.name')) }}</h1>
                    </td>
                </tr>
            </table>
            </tbody>
        </table>
        <table bgcolor="white">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Exception</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $i = 1;
                @endphp
                @foreach ($exceptions as $exception)
                    <tr>
                        <td data-column="No">{{ $i++ }}</td>
                        <td data-column="Exception">
                            <a href="{{ url('exceptions/' . $exception->id . '/show') }}"
                                title="please click on link to view the details" style="text-decoration: none;">
                                {{ $exception->message }}</a>
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>
        <table style="margin-bottom:15px;">
            <th style="text-align:center">
                <a href="{{ url('exceptions?pass=' . \Crypt::encryptString('info@hellokongo.com')) }}" target="_blank"
                    style="text-align:center;color:white">
                    View All</a>
            </th>
        </table>
    </table>
    </table>



</body>

</html>
