<div id="invoiceholder">

    @php
    use Carbon\Carbon;
    $formattedDate = Carbon::parse($data['created_at'])->format('d,m Y');
@endphp


    <div id="headerimage"></div>
    <div id="invoice" class="effect2">

        <div id="invoice-top">
            <div class="logo"></div>
            <div class="info">
                <h2>Michael Truong</h2>
                <p> hello@michaeltruong.ca </br>
                    289-335-6503
                </p>
            </div><!--End Info-->
            <div class="title">
                <h1>Invoice #{{ $data['id']}}</h1>
                <p>Issued: {{ $formattedDate }}</br>

                </p>
            </div><!--End Title-->
        </div><!--End InvoiceTop-->



        <div id="invoice-mid">

            <div class="clientlogo"></div>
            <div class="info">
                <h2>Client Name</h2>
                <p>JohnDoe@gmail.com</br>
                    555-555-5555</br>
            </div>

            {{-- <div id="project">
                <h2>Project Description</h2>
                <p>Proin cursus, dui non tincidunt elementum, tortor ex feugiat enim, at elementum enim quam vel purus.
                    Curabitur semper malesuada urna ut suscipit.</p>
            </div> --}}

        </div><!--End Invoice Mid-->

        <div id="invoice-bot">

            <div id="table">
                <table>
                    <tr class="tabletitle">
                        <td class="item">
                            <h2>Item Description</h2>
                        </td>
                        <td class="Hours">
                            <h2>Stripe Id</h2>
                        </td>
                        <td class="Rate">
                            <h2>Amount</h2>
                        </td>

                        <td class="subtotal">
                            <h2>Paid</h2>
                        </td>
                        <td class="subtotal">
                            <h2>Paid At</h2>
                        </td>
                    </tr>

                    <tr class="service">
                        <td class="tableitem">
                            <p class="itemtext">{{ $data['description']}}</p>
                        </td>
                        <td class="tableitem">
                            <p class="itemtext">{{ $data['stripe_id']}}</p>
                        </td>
                        <td class="tableitem">
                            <p class="itemtext">${{ $data['amount']}}</p>
                        </td>
                        <td class="tableitem">
                            <p class="itemtext">{{ ($data['paid'] == 1) ? 'Paid' : 'Not Paid' }}</p>
                        </td>
                        <td class="tableitem">
                            <p class="itemtext">{{ $data['created_at'] }} </p>
                        </td>
                    </tr>



                    <tr class="tabletitle">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="Rate">
                            <h2>Total</h2>
                        </td>
                        <td class="payment">
                            <h2>${{ $data['amount']}}</h2>
                        </td>
                    </tr>

                </table>
            </div><!--End Table-->
            {{-- <form method="get" action="{{ route('view.invoice', $id) }}">
                <input type="hidden" name="download" value="true">
                <button type="submit" class="button"> Download </button>
            </form> --}}



            <div id="legalcopy">
                <p class="legal"><strong>Thank you for your business!</strong>  </p>
            </div>

        </div><!--End InvoiceBot-->
    </div><!--End Invoice-->
</div><!-- End Invoice Holder-->
<style>
    @import url(https://fonts.googleapis.com/css?family=Roboto:100,300,400,900,700,500,300,100);

    * {
        margin: 0;
        box-sizing: border-box;

    }

    .button {
        background-color: #04AA6D;
        /* Green */
        border: none;
        color: white;
        padding: 15px 32px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
    }

    body {
        background: #E0E0E0;
        font-family: 'Roboto', sans-serif;
        background-image: url('');
        background-repeat: repeat-y;
        background-size: 100%;
    }

    ::selection {
        background: #f31544;
        color: #FFF;
    }

    ::moz-selection {
        background: #f31544;
        color: #FFF;
    }

    h1 {
        font-size: 1.5em;
        color: #222;
    }

    h2 {
        font-size: .9em;
    }

    h3 {
        font-size: 1.2em;
        font-weight: 300;
        line-height: 2em;
    }

    p {
        font-size: .7em;
        color: #666;
        line-height: 1.2em;
    }

    #invoiceholder {
        width: 100%;
        hieght: 100%;
        padding-top: 50px;
    }

    #headerimage {
        z-index: -1;
        position: relative;
        top: -50px;
        height: 350px;

        overflow: hidden;
        background-attachment: fixed;
        background-size: 1920px 80%;
        background-position: 50% -90%;
    }

    #invoice {
        position: relative;
        top: -290px;
        margin: 0 auto;
        width: 700px;
        background: #FFF;
    }

    [id*='invoice-'] {
        /* Targets all id with 'col-' */
        border-bottom: 1px solid #EEE;
        padding: 30px;
    }

    #invoice-top {
        min-height: 120px;
    }

    #invoice-mid {
        min-height: 120px;
    }

    #invoice-bot {
        min-height: 250px;
    }

    .logo {
        float: left;
        height: 60px;
        width: 60px;
        background: url(http://michaeltruong.ca/images/logo1.png) no-repeat;
        background-size: 60px 60px;
    }

    .clientlogo {
        float: left;
        height: 60px;
        width: 60px;
        background: url(http://michaeltruong.ca/images/client.jpg) no-repeat;
        background-size: 60px 60px;
        border-radius: 50px;
    }

    .info {
        display: block;
        float: left;
        margin-left: 20px;
    }

    .title {
        float: right;
    }

    .title p {
        text-align: right;
    }

    #project {
        margin-left: 52%;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    td {
        padding: 5px 0 5px 15px;
        border: 1px solid #EEE
    }

    .tabletitle {
        padding: 5px;
        background: #EEE;
    }

    .service {
        border: 1px solid #EEE;
    }

    .item {
        width: 50%;
    }

    .itemtext {
        font-size: .9em;
    }

    #legalcopy {
        margin-top: 30px;
    }

    form {
        float: right;
        margin-top: 30px;
        text-align: right;
    }


    .effect2 {
        position: relative;
    }

    .effect2:before,
    .effect2:after {
        z-index: -1;
        position: absolute;
        content: "";
        bottom: 15px;
        left: 10px;
        width: 50%;
        top: 80%;
        max-width: 300px;
        background: #777;
        -webkit-box-shadow: 0 15px 10px #777;
        -moz-box-shadow: 0 15px 10px #777;
        box-shadow: 0 15px 10px #777;
        -webkit-transform: rotate(-3deg);
        -moz-transform: rotate(-3deg);
        -o-transform: rotate(-3deg);
        -ms-transform: rotate(-3deg);
        transform: rotate(-3deg);
    }

    .effect2:after {
        -webkit-transform: rotate(3deg);
        -moz-transform: rotate(3deg);
        -o-transform: rotate(3deg);
        -ms-transform: rotate(3deg);
        transform: rotate(3deg);
        right: 10px;
        left: auto;
    }



    .legal {
        width: 70%;
    }
</style>
