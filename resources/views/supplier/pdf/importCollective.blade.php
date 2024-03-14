

<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
<style>
@import url(https://fonts.googleapis.com/css?family=Roboto:100,300,400,900,700,500,300,100);
*{
  margin: 0;
  box-sizing: border-box;
  -webkit-print-color-adjust: exact;
}
body{
  background: #E0E0E0;
  font-family: 'Roboto', sans-serif;
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
}
.col-right {
    float: right;
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
    color: #00a63f;
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
}

[id*='invoice-']{ /* Targets all id with 'col-' */
/*  border-bottom: 1px solid #EEE;*/
  padding: 20px;
}

#invoice-top{border-bottom: 2px solid #00a63f;}
#invoice-mid{min-height: 110px;}
/*#invoice-bot{ min-height: 240px;}*/

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
    background: rgb(196, 57, 10);
    margin-right: 10px;
    min-width: 100px;
    text-align: center;
    color: #fff;
    font-size: 0.75em;
}
.cta-group .btn-primary {
    background: #00a63f;
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
  text-align: right;
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
    text-align: right;
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
     /*==================== Table ====================*/
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
        /*
        * aria-label has no advantage, it won't be read inside a table
        content: attr(aria-label);
        */
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
      <div class="logo"><img src="https://www.almonature.com/wp-content/uploads/2018/01/logo_footer_v2.png" alt="Logo" /></div>
      <div class="title">
        <h1>PO #<span class="invoiceVal invoice_num">tst-inv-23</span></h1>
      </div><!--End Title-->
    </div><!--End InvoiceTop-->


    
    <div id="invoice-mid">   
      <div id="message">
        <h2>Hello <span id="user_name">Andrea De Asmundis</span></h2>
        <p><span id="vandor_name">TESI S.P.A.</span>created an invoice without purchase order. Please create a purchase order for the invoice #<span id="invoice_num"><b>tst-inv-23</b></span></p>
      </div>
<!--
       <div class="cta-group mobile-btn-group">
            <a href="javascript:void(0);" class="btn-primary">Approve</a>
            <a href="javascript:void(0);" class="btn-default">Reject</a>
        </div> 
-->
        <div class="clearfix">
            <div class="col-left">
                <div class="clientlogo"><img src="https://cdn3.iconfinder.com/data/icons/daily-sales/512/Sale-card-address-512.png" alt="Sup" /></div>
                <div class="clientinfo">
                    <h2 id="supplier">TESI S.P.A.</h2>
                    <p><span id="address">VIA SAVIGLIANO, 48</span><br><span id="city">RORETO DI CHERASCO</span><br><span id="country">IT</span> - <span id="zip">12062</span><br><span id="tax_num">555-555-5555</span><br></p>
                </div>
            </div>
            <div class="col-right">
                <table class="table">
                    <thead>
                        <tr>
                            <th>PO Details</th>
                            <th>Summary</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span>Creation Date</span><label id="creation_date">08 Mar 2018</label></td>
                            <td><span>Total</span><label id="po_amount">245.00</label></td>
                        </tr>
                        <tr>
                            <td><span>Status</span><label id="status">Active</label></td>
                            <td><span>Quantity</span><label id="quantity">6.6</label></td>
                        </tr>
                        <tr>
                            <td><span>Authorization Status</span><label id="authorization_status">Active</label></td>
                            <td><span>Billed</span><label id="quantity_billed">2</label></td>
                        </tr>
                        <tr>
                            <td><span>Approved Date</span><label id="approved_date">04 Mar 2018</label></td>
                            <td><span>Received</span><label id="quantity_received">2</label></td>
                        </tr>
                        <tr><td colspan="2"><span>Note</span>#<label id="note">None</label></td></tr>
                    </tbody>
                </table>
            </div>
        </div>       
    </div><!--End Invoice Mid-->
    
    <div id="invoice-bot">
      
      <div id="table">
        <table class="table-main">
          <thead>    
              <tr class="tabletitle">
                <th>Line</th>
                <th>Item Code</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Recieved</th>
                <th>Billed</th>
                <th>Amount</th>
                <th>Total</th>
              </tr>
          </thead>
          <tr class="list-item">
            <td data-label="Line" class="tableitem" id="line_num">1</td>
            <td data-label="Item Code" class="tableitem" id="item_code">DP20</td>
            <td data-label="Description" class="tableitem" id="item_description">Servizio EDI + Traffico mese di novembre 2017</td>
            <td data-label="Quantity" class="tableitem" id="quantity">46.6</td>
            <td data-label="Unit Price" class="tableitem" id="unit_price">1</td>
            <td data-label="Recieved" class="tableitem" id="quantity_received">20</td>
            <td data-label="Billed" class="tableitem" id="quantity_billed">46.6</td>
            <td data-label="Amount" class="tableitem" id="amount">20</td>
            <td data-label="Total" class="tableitem">55.92</td>
          </tr>
         <tr class="list-item">
            <td data-label="Line" class="tableitem" id="line_num">2</td>
            <td data-label="Item Code" class="tableitem" id="item_code">DP21</td>
            <td data-label="Description" class="tableitem" id="item_description">Traffico mese di novembre 2017 FRESSNAPF TIERNAHRUNGS GMBH riadd. Almo DE</td>
            <td data-label="Quantity" class="tableitem" id="quantity">4.4</td>
            <td data-label="Unit Price" class="tableitem" id="unit_price">1</td>
            <td data-label="Recieved" class="tableitem" id="quantity_received">12</td>
            <td data-label="Billed" class="tableitem" id="quantity_billed">13.2</td>
            <td data-label="Amount" class="tableitem" id="amount">15</td>
            <td data-label="Total" class="tableitem">55.92</td>
          </tr>
            <tr class="list-item total-row">
                <th colspan="8" class="tableitem">Grand Total</th>
                <td data-label="Grand Total" class="tableitem">111.84</td>
            </tr>
        </table>
      </div><!--End Table-->
<!--
    <div class="cta-group">
        <a href="javascript:void(0);" class="btn-primary">Approve</a>
        <a href="javascript:void(0);" class="btn-default">Reject</a>
    </div> 
-->
      
    </div><!--End InvoiceBot-->
    <footer>
      <div id="legalcopy" class="clearfix">
        <p class="col-right">Our mailing address is:
            <span class="email"><a href="mailto:supplier.portal@almonature.com">supplier.portal@almonature.com</a></span>
        </p>
      </div>
    </footer>
  </div><!--End Invoice-->
</div><!-- End Invoice Holder-->
  
  

</body>