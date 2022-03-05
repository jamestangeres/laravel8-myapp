<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <title>Laravel 7 PDF Example</title>

    <style>
        p {
            margin-bottom: 2px;
            padding-top: 0px;
            margin-top: 3px;
            font-size: 1em;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
        }

        table {
            border-collapse: collapse;
            border-spacing: 0;
        }

        .logo-receipt {
            height: 40px;
        }

        .section {
            margin-top: 80px;
        }

        .bill-to {}

        strong {
            color: rgb(70, 70, 70);
        }

        h2 {
            color: rgb(70, 70, 70);
        }

        .page {
            margin: 10px;
        }

        th {
            background-color: rgb(70, 70, 70);
            color: white;
            border: 1px solid gray;
            text-align: center !important;
            font-size: 0.85em;
        }

        td {
            border: 1px solid gray;
            height: 30px;
            text-align: center;
            font-size: 0.85em;
            padding: 3px;
            white-space: pre-wrap;
            /* css-3 */
            white-space: -moz-pre-wrap;
            /* Mozilla, since 1999 */
            white-space: -pre-wrap;
            /* Opera 4-6 */
            white-space: -o-pre-wrap;
            /* Opera 7 */
            word-wrap: break-word;
            /* Internet Explorer 5.5+ */
        }

        .section-2 {
            margin-top: 20px;
        }

        .amount {
            border-top: 1px solid gray;
            position: absolute;
            top: 0%;
            right: 0%;
        }

        .retainer-details {
            position: absolute;
            top: 7%;
            left: 0%;
        }

        .sales-label {
            position: absolute;
            top: 0%;
            right: 0%;
        }

        .top {
            position: relative;
            top: 0%;
        }

        .billing {
            position: relative;
            top: 260px;
        }

        .bill {
            position: absolute;
            top: 0%;
            left: 0%;
        }

        .receipt {
            position: absolute;
            top: 0%;
            right: 0%;
        }

        .retainer-table {
            position: relative;
            top: 50px;

        }

        .total {
            position: relative;
            top: 390px;
        }

        .totalright {
            position: absolute;
            top: 0%;
            right: 0%;
        }

        .totalleft {
            position: absolute;
            top: 0px;
            right: 200px;
        }

        .amount-paid {
            position: relative;
            top: 500px;
        }

        .notes {
            position: relative;
            top: 620px;
        }

        .bottom {
            position: relative;
            top: 630px;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="page">
        <main>
            <div class="top">
                <h2 style="text-align: center">Laravel HTML to PDF Example</h2>
                <div class="retainer-details">

                    <p>Client: <b> Company name</b></p>
                    <p>Contractor: <b> Laracast </b></p>
                    <p>Balance: <b> 99999 </b></p>

                </div>
                <div class="section retainer-table">
                    <table style="width:100%;table-layout:fixed;break;overflow-wrap: break-word;">
                        <thead>
                            <tr>
                                <th style="width:3%">#</th>
                                <th style="width:10%">Name</th>
                                <th style="width:13%">Email</th>
                                <th style="width:7%">Phone</th>
                                <th style="width:7%">DOB</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $data)
                            <tr>
                                <td>{{ $data->id }}</td>
                                <td>{{ $data->name }}</td>
                                <td>{{ $data->email }}</td>
                                <td>{{ $data->phone_number }}</td>
                                <td>{{ $data->dob }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
        </main>
        <script src="{{ asset('js/app.js') }}" type="text/js"></script>
        <script src="https://dhbhdrzi4tiry.cloudfront.net/cdn/sites/foundation.js"></script>
        <script>
            $(document).foundation();
        </script>
    </div>
</body>
</html>