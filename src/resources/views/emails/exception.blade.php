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

        /* CLIENT-SPECIFIC STYLES */
        body,
        table,
        td,
        a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            -ms-interpolation-mode: bicubic;
        }

        /* RESET STYLES */
        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        table {
            border-collapse: collapse !important;
        }

        body {
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }

        /* iOS BLUE LINKS */
        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        /* MOBILE STYLES */
        @media screen and (max-width:600px) {
            h1 {
                font-size: 32px !important;
                line-height: 32px !important;
            }
        }

        /* ANDROID CENTER FIX */
        div[style*="margin: 16px 0;"] {
            margin: 0 !important;
        }

    </style>
<body>

                     
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

    <table style="width: 800px;margin: 0 auto;background-color: #f9f9f9;padding: 16px;">
    <tr style="background-color:#ff4153">
        <td><h1 style="margin-bottom:20px;text-align: center;margin: 0;font-size: 26px;padding: 20px; color: #fff;font-family: 'Roboto', sans-serif;font-weight: 700;">Exception occured</h1></td>
    </tr>
    <tr>
        <td  style="padding-top: 20px;">
            <h3 style="margin:0;color:#000;font-family: 'Roboto', sans-serif;font-weight: 700;padding:0 20px;">Hi, Something went wrong on {{config('app.name')}}</h3>
        </td>
    </tr>
    <tr>
        <td>
            <h3 style="padding:0 20px;margin:0 0 20px 0;line-height:1.8;font-size:16px;font-family: 'Roboto', sans-serif;">
            {{$exception->message}}
           </h3>
        </td>
    </tr>
    <tr>
        <td>
            <p  style="    background-color: #2F4F4F;color: #fff;padding: 20px;margin:0 0 20px 0;line-height:1.8;font-size:16px;font-family: 'Roboto', sans-serif;font-weight: 400;">
            {{print_r($exception->context)}}
          </p>
        </td>
    </tr>

    <tr>
        <td>
            <div style="text-align:center">
                <a href="{{ url('exceptions?pass=' . \Crypt::encryptString('info@hellokongo.com')) }}" style="padding:10px 30px;font-size:19px;font-family: 'Roboto', sans-serif;font-weight: 600;display: inline-block;text-decoration: none;">
                Please see error details here 
            </a>
            </div>
        </td>
    </tr>

    </table>
</body>


</html>