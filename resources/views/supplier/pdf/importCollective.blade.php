

<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
<style>
@import url(https://fonts.googleapis.com/css?family=Roboto:100,300,400,900,700,500,300,100);
*, *::before, *::after{
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  -webkit-print-color-adjust: exact;
}
body, html {
  font-family: 'Roboto', sans-serif;
  width: 100%;
  height: 100%;
  overflow: hidden;
}
::selection {background: #f31544; color: #FFF;}
::moz-selection {background: #f31544; color: #FFF;}
.clearfix::after {
    content: "";
    clear: both;
    display: table;
}
.col-left {
    float: left;
    width: 50%; /* Ajuste para evitar sobreposição */

}
.col-right {
    float: right;
    width: 50%; /* Ajuste para evitar sobreposição */

}
h1{
  font-size: 1.5em;
  color: #444;
}
h2{font-size: .9em;}
h3{
  font-size: 1.2em;
  font-weight: 300;
  line-height: 2em;
}
p{
  font-size: .75em;
  color: #666;
  line-height: 1.2em;
}
a {
    text-decoration: none;
    color: #444;
}

#invoiceholder{
  width:100%;
  height: 100%;
  padding: 50px 0;
}
#invoice{
  position: relative;
  margin: 0 auto;
  width: 700px;
  background: #FFF;
  page-break-inside: avoid
}

[id*='invoice-']{ /* Targets all id with 'col-' */
/*  border-bottom: 1px solid #EEE;*/
  padding: 20px;
}

#invoice-top{border-bottom: 2px solid #444;}
#invoice-mid{min-height: 110px;}

.logo{
    display: inline-block;
    vertical-align: middle;
  width: 110px;
    overflow: hidden;
}
.info{
    display: inline-block;
    vertical-align: middle;
    margin-left: 20px;
}
.logo img,
.clientlogo img {
    width: 100%;
}
.clientlogo{
    display: inline-block;
    vertical-align: middle;
  width: 50px;
}
.info-fields {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
}

.info-field {
    flex-basis: 48%;
    margin-bottom: 10px;
}
.clientinfo {
    display: inline-block;
    vertical-align: middle;
    margin-left: 20px
}
.title{
  float: right;
  margin-top: 15px;
}
.title p{text-align: right;}
#message{margin-bottom: 30px; display: block;}
h2 {
    margin-bottom: 5px;
    color: #444;
}
.col-right td {
    color: #666;
    padding: 5px 10px;
    border: 0;
    font-size: 0.75em;
    border-bottom: 1px solid #eeeeee;
}
.col-right td label {
    margin-left: 5px;
    font-weight: 600;
    color: #444;
}
.cta-group a {
    display: inline-block;
    padding: 7px;
    border-radius: 4px;
    margin-right: 10px;
    min-width: 100px;
    text-align: center;
    color: #fff;
    font-size: 0.75em;
}
.cta-group .btn-primary {
    background: #444;
}
.cta-group.mobile-btn-group {
    display: none;
}
table{
  width: 100%;
  border-collapse: collapse;
}
td{
    padding: 10px;
    border-bottom: 1px solid #cccaca;
    font-size: 0.70em;
    text-align: left;
}

.tabletitle th {
  text-align: left;
}
.tabletitle th:nth-child(3) {
    text-align: left;
}
th {
    font-size: 0.7em;
    text-align: left;
    padding: 5px 10px;
    border-bottom: 2px solid #ddd;
}
.item{width: 50%;}
.list-item td {
    text-align: left;
}
.list-item td:nth-child(3) {
    text-align: left;
}
.total-row th,
.total-row td {
    text-align: right;
    font-weight: 700;
    font-size: .75em;
    border: 0 none;
}
.table-main {
    
}
footer {
    border-top: 1px solid #eeeeee;;
    padding: 15px 20px;
}
.effect2
{
  position: relative;
}
.effect2:before, .effect2:after
{
  z-index: -1;
  position: absolute;
  content: "";
  bottom: 15px;
  left: 10px;
  width: 50%;
  top: 80%;
  max-width:300px;
  -webkit-transform: rotate(-3deg);
  -moz-transform: rotate(-3deg);
  -o-transform: rotate(-3deg);
  -ms-transform: rotate(-3deg);
  transform: rotate(-3deg);
}
.effect2:after
{
  -webkit-transform: rotate(3deg);
  -moz-transform: rotate(3deg);
  -o-transform: rotate(3deg);
  -ms-transform: rotate(3deg);
  transform: rotate(3deg);
  right: 10px;
  left: auto;
}
@media screen and (max-width: 767px) {
    h1 {
        font-size: .9em;
    }
    #invoice {
        width: 100%;
    }
    #message {
        margin-bottom: 20px;
    }
    [id*='invoice-'] {
        padding: 20px 10px;
    }
    .logo {
        width: 140px;
    }
    .title {
        float: none;
        display: inline-block;
        vertical-align: middle;
        margin-left: 40px;
    }
    .title p {
        text-align: left;
    }
    .col-left,
    .col-right {
        width: 100%;
    }
    .table {
        margin-top: 20px;
    }
    #table {
        white-space: nowrap;
        overflow: auto;
    }
    td {
        white-space: normal;
    }
    .cta-group {
        text-align: center;
    }
    .cta-group.mobile-btn-group {
        display: block;
        margin-bottom: 20px;
    }
    .table-main {
        border: 0 none;
    }  
      .table-main thead {
        border: none;
        clip: rect(0 0 0 0);
        height: 1px;
        margin: -1px;
        overflow: hidden;
        padding: 0;
        position: absolute;
        width: 1px;
      }
      .table-main tr {
        border-bottom: 2px solid #eee;
        display: block;
        margin-bottom: 20px;
      }
      .table-main td {
        font-weight: 700;
        display: block;
        padding-left: 40%;
        max-width: none;
        position: relative;
        border: 1px solid #cccaca;
        text-align: left;
      }
      .table-main td:before {
        content: attr(data-label);
        position: absolute;
        left: 10px;
        font-weight: normal;
        text-transform: uppercase;
      }
    .total-row th {
        display: none;
    }
    .total-row td {
        text-align: left;
    }
    footer {text-align: center;}
}</style>
</head>
<body>
  <div id="invoiceholder">
  <div id="invoice" class="effect2">
    
    <div id="invoice-top">
      <div class="logo"><img src="assets/img/brand/logo.png?v=2" alt="Logo" /></div>
      <div class="title">
        <h1>ID #<span class="invoiceVal invoice_num">{{ $collectiveImport->id }}</span></h1>
      </div>
    </div>
    <div id="invoice-mid">   
      <div id="message">
        <h2>Hello! <span id="user_name"></span></h2>
        <p><span id="vandor_name">Stockhub</span> created an invoice for a purchase order. Please create a quote for invoice!</span></p>
      </div>
        <div class="clearfix">
            <div class="col-left">
                <div class="clientinfo">
                <h2 id="supplier">{{ $collectiveImport->type_order == 1 ? $collectiveImport->shop->responsible_name : $collectiveImport->shop->corporate_name }}</h2>
                    <div class="info-fields">
                        <div class="info-field">
                            <p><span id="phone">Phone: {{ $collectiveImport->shop->phone }}</span><br></p>
                        </div>
                        <div class="info-field">
                            <p><span id="email">E-mail: {{ $collectiveImport->shop->email }}</span><br></p>
                        </div>
                        <div class="info-field">
                            <p><span id="document">Document: {{ $collectiveImport->type_order == 1 ? $collectiveImport->shop->responsible_document : $collectiveImport->shop->document }}</span><br></p>
                        </div>
                        <div class="info-field">
                            <p><span id="client_id">Cliente ID: {{ $collectiveImport->shop->id }}</span><br></p>
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="col-right">
                <table class="table">
                    <thead>
                        <tr>
                            <th>PO Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="2"><span>Note</span>: <span id="rejection_reason">{{ $collectiveImport->rejection_reason }}</span></td></tr>
                        <tr><td colspan="2"><span>Product Description</span>: <span id="rejection_reason">{{ $collectiveImport->product_description }}</span></td></tr>
                    </tbody>
                </table>
            </div>
        </div>       
    </div>
    
    <div id="invoice-bot">
      
    <div id="table">
    <table class="table-main">
        <tr class="list-item">
            <th>Creation Date:</th>
            <td>{{ $collectiveImport->created_at }}</td>
        </tr>
        <tr class="list-item">
            <th>Status:</th>
            <td>{{ $collectiveImport->status }}</td>
        </tr>
        <tr class="list-item">
        <th>Address:</th>
            <td>
                {{ $collectiveImport->type_order == 1 
                    ? $collectiveImport->shop->address->street . ', ' . $collectiveImport->shop->address->number . ' ' . $collectiveImport->shop->address->complement . ', ' . $collectiveImport->shop->address->district . ' ' . $collectiveImport->shop->address->city . ', ' . $collectiveImport->shop->address->state_code . ' - ' . $collectiveImport->shop->address->country . ', ' . $collectiveImport->shop->address->zipcode 
                    : $collectiveImport->shop->address_business->street_company . ', ' . $collectiveImport->shop->address_business->number_company . ' ' . $collectiveImport->shop->address_business->complement_company . ', ' . $collectiveImport->shop->address_business->district_company . ' ' . $collectiveImport->shop->address_business->city_company . ', ' . $collectiveImport->shop->address_business->state_code_company . ' - ' . $collectiveImport->shop->address_business->country_company . ', ' . $collectiveImport->shop->address_business->zipcode_company 
                }}
            </td>
        </tr>
        <tr class="list-item">
            <th>Product Link:</th>
            <td>{{ $collectiveImport->produto_link }}</td>
        </tr>
        <tr class="list-item">
            <th>Supplier Name:</th>
            <td>{{ $collectiveImport->china_supplier_name }}</td>
        </tr>
        <tr class="list-item">
            <th>Supplier Contact:</th>
            <td>{{ $collectiveImport->china_supplier_contact }}</td>
        </tr>
        <tr class="list-item">
            <th>Product HS Code:</th>
            <td>{{ $collectiveImport->product_hs_code }}</td>
        </tr>
    </table>
</div>
    <footer>
      <div id="legalcopy" class="clearfix">
        <p class="col-right">Our mailing address is:
            <span class="email">arthur@s2m2company.com</span>
        </p>
      </div>
    </footer>
  </div>
</div>
</body>
</html>
