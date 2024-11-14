<!DOCTYPE html>
<html lang="en">

<head>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'PT Sans', sans-serif;
        }

        @page {
            /* size: 2.8in 11in; */
            margin-top: 0cm;
            margin-left: 0cm;
            margin-right: 0cm;
        }

        table {
            width: 100%;
        }

        tr {
            width: 100%;

        }

        h1 {
            text-align: center;
            vertical-align: middle;
        }

        #logo {
            width: 60%;
            text-align: center;
            -webkit-align-content: center;
            align-content: center;
            padding: 5px;
            margin: 2px;
            display: block;
            margin: 0 auto;
        }

        header {
            width: 100%;
            text-align: center;
            -webkit-align-content: center;
            align-content: center;
            vertical-align: middle;
        }

        .items thead {
            text-align: center;
        }

        .center-align {
            text-align: center;
        }

        .bill-details td {
            font-size: 12px;
        }

        .receipt {
            font-size: medium;
        }

        .items .heading {
            font-size: 12.5px;
            text-transform: uppercase;
            border-top:1px solid black;
            margin-bottom: 4px;
            border-bottom: 1px solid black;
            vertical-align: middle;
        }

        .items thead tr th:first-child,
        .items tbody tr td:first-child {
            width: 47%;
            min-width: 47%;
            max-width: 47%;
            word-break: break-all;
            text-align: left;
        }

        .items td {
            font-size: 12px;
            text-align: right;
            vertical-align: bottom;
        }

        .price::before {
             content: "\20B9";
            font-family: Arial;
            text-align: right;
        }

        .sum-up {
            text-align: right !important;
        }
        .total {
            font-size: 13px;
            border-top:1px dashed black !important;
            border-bottom:1px dashed black !important;
        }
        .total.text, .total.price {
            text-align: right;
        }
        .total.price::before {
            content: "\20B9"; 
        }
        .line {
            border-top:1px solid black !important;
        }
        .heading.rate {
            width: 20%;
        }
        .heading.amount {
            width: 25%;
        }
        .heading.qty {
            width: 5%
        }
        p {
            padding: 1px;
            margin: 0;
        }
        section, footer {
            font-size: 12px;
        }
    </style>
</head>

<body>
    <header>
        <img id="logo" class="media" src="{{ asset('logo/logo.jpg') }}" alt="Logo">
    </header>
    <p>GST Number : 4910487129047124</p>
    <table class="bill-details">
        <tbody>
            <!-- Thông tin khách hàng -->
            <tr>
                <td>Customer Name: <span>{{ $data->user->ho_ten }}</span></td>
                <td>Phone: <span>{{ $data->user->so_dien_thoai  }}</span></td>
            </tr>
            <tr>
                <td>Email: <span>{{ $data->user->email  }}</span></td>
            </tr>

            <!-- Thông tin hóa đơn -->
            <tr>
                <td>Date : <span>{{ $data->ngay_mua  }}</span></td>
                <td>Time : <span>{{ $data->payment->ngay_thanh_toan }}</span></td>
            </tr>
            <tr>
                <td>Bill # : <span>{{ $data->id }}</span></td>
            </tr>
            <tr>
                <th class="center-align" colspan="2"><h2 class="receipt">{{env('APP_NAME')}}</h2></th>
            </tr>
        </tbody>
    </table>

    <p>Hàng ghế: {{ $data->ghe_ngoi }}</p>
    <p>Room: {{ $tenRoom->ten_phong_chieu }}</p>
    <table class="items">
        <thead>
            <tr>
                <th class="heading name">Item</th>
                <th class="heading qty">Qty</th>
                <th class="heading rate">Rate</th>
                <th class="heading amount">Amount</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td>{{ $tenPhim->ten_phim }}</td>
                <td>1</td>
                <td>{{ number_format($tenPhim->gia_ve, 0, ',', '.') }} VND</td>
                <td>{{ number_format($tenPhim->gia_ve, 0, ',', '.') }} VND</td>
            </tr>
            <tr>
                <td>{{ $data->food->ten_do_an }}</td>
                <td>{{ $data->so_luong_do_an }}</td>
                <td>{{ number_format($data->food->gia, 0, ',', '.') }} VND</td>
                <td>{{ number_format($data->food->gia * $data->so_luong_do_an, 0, ',', '.') }} VND</td>
            </tr>
            
            <tr>
                <td colspan="3" class="sum-up">Voucher Discount</td>
                <td>-{{ $giaTriVoucher }}%</td>
            </tr>
            <tr>
                <th colspan="3" class="total text">Total</th>
                <th class="total">{{ number_format($data->tong_tien_thanh_toan, 0, ',', '.') }} VND</th>
            </tr>
        </tbody>    
    </table>
    <section>
        <p>
            Paid by : <span>{{ $data->payment->phuong_thuc_thanh_toan }}</span>
        </p>
        <p style="text-align:center">
            Thank you for your visit!
        </p>
    </section>

    <div>
        <img src="" alt="">
    </div>
    <footer style="text-align:center">
        <p>Technology Partner Dotworld Technologies</p>
        <p>www.dotworld.in</p>
    </footer>
</body>

</html>