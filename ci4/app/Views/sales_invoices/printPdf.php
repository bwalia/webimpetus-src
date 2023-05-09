<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    
    <style>
      body {
        font-family: Mulish, sans-serif;
        font-size: 12px;
      }
      .col1, .col2, .col3,{
        float:left;
	      width:30%;
      }
      .floatleft{
	     float:left;
      }

       .floatright{
	      float:right;
      }
       .detail-1, .detail-2 {
        border: 1px solid #d4d4d4;
        background-color: #d4d4d4;
        padding: 10px;
        margin: 5px;
        width: 50%;

       }
        
      
    </style>
    
<?php 
$customers = getResultArray("customers");
$templates = getResultArray("templates");
$items = getResultArray("sales_invoice_items", ["sales_invoices_id" => @$sales_invoice->id], false);
$notes = getResultArray("sales_invoice_notes", ["sales_invoices_id" => @$sales_invoice->id], false);
//$no = 1;
if(isset($sales_invoice->id)){

    $is_tax_readonly = "disabled";
}
?>




<table border="0" cellpadding="3" style="font-family: Mulish, sans-serif; font-size: 13px; width:100%;">
  <tbody>
    <tr style=" width:100%;">
        <td align="left">
            <img src="<?= FCPATH?>assets/img/wsllogo-login.png" alt="<?= FCPATH."assets/" ?> img/wsllogo-login.png" width="200" />
        </td>
        
        <td  align="left">
            <img src="<?= FCPATH?>assets/img/iv.4.png" alt="<?= FCPATH."assets/" ?> /img/iv.4.png" width="100" />
         </td>
     </tr>
     

    <tr align="left">
      <td>
        
        <p> Workstation SRL
            <br />Brussels Avenue de Roodebeeck 78<br />
            Schaerbeek, Brussels ,1030<br />
            workstationsols@gmail.com<br />Tel: 049 245 67465
       </p>
      </td>

      
      

       <td align="left">
          
          <p>Invoice no :    <?php echo $sales_invoice->invoice_number; ?></p><br />
          
          <p>Timeslip date :    <?php echo date("d-m-Y", time()); ?></p><br />
          <p>period date :   <?php echo date("d-m-Y",strtotime("+30 days") ); ?></p>
       </td>     
       
    </tr>
    
      
    
    
    
  </tbody>
</table>

<hr class="line-title"  style="color:#ff004e; ">

  <div class="row" style="font-family: Mulish, sans-serif;">
    <div class="col1">
         <p ><b>Bill To :</b> <br />
            <?php echo $sales_invoice->order_by; ?><br />
             <?php echo $sales_invoice->bill_to; ?>
          </p>
        
        
    </div>
    <div class="col2 floatright">
       
         <p><b><?php echo $sales_invoice->project_code; ?></b></p>  
        
    </div>
    <div class="col3 floatright">
        <p ><b>Project name :</b></p>
    </div>

  </div>
       

        <br />
        <table name="invoice_item" cellpadding="5" style="font-family: Mulish, sans-serif; font-size: 13px; width:100%; border-collapse: collapse; " >
          
          <thead>
            <tr style="background-color: #d4d4d4; ">
              <th align="center" width="10%"> Id </th>    
              <th align="center" width="30%"> Description </th>
              <th align="center"  width="20%"> Rate </th>
              <th align="center"  width="20%"> Hours</th>
              <th align="center"  width="20%"> Amount</th>
              
            </tr>
          </thead>
          <tbody >
            <?php foreach( $items as $eachItems) { ?>
              <tr >
                <td align="center"> <?= $eachItems->id?> </td>
                <td align="center"> <?= $eachItems->description?></td>
                <td align="center"> <?= $eachItems->rate?></td>
                <td align="center"> <?= $eachItems->hours?></td>
                <td align="center"> <?= $eachItems->amount?></td>
              </tr>
            <?php } ?>

            <tr>
              <td align="right"></td>
              <td align="right"></td>
              <td align="right"></td>
              <td align="center" style="border-bottom: 1px solid #f5f5f5;">Tax code:</td>
              <td align="center" style="border-bottom: 1px solid #f5f5f5;"><?php echo $sales_invoice->inv_tax_code; ?></td>
              
            </tr>
            <tr>
              <td align="right"></td>
              <td align="right"></td>
              <td align="right"></td>
              <td align="center" style="border-bottom: 1px solid #f5f5f5;">Total tax:</td>
              <td align="center" style="border-bottom: 1px solid #f5f5f5;"><?php echo $sales_invoice->total_tax; ?></td>
              
            </tr>
            <tr>
              <td align="right"></td>
              <td align="right"></td>
              <td align="right"></td>
              <td align="center" style="border-bottom: 1px solid #f5f5f5;">Total hours:</td>
              <td align="center" style="border-bottom: 1px solid #f5f5f5;"><?php echo $sales_invoice->total_hours; ?></td>
              
            </tr>
            <tr>
              <td align="right"></td>
              <td align="right"></td>
              <td align="right"></td>
              <td align="center" style="border-bottom: 1px solid #f5f5f5;">Subtotal:</td>
              <td align="center" style="border-bottom: 1px solid #f5f5f5;"><?php echo $sales_invoice->total_due; ?></td>
              
            </tr>
            <tr>
              <td align="right"></td>
              <td align="right"></td>
              <td align="right"></td>
              <td align="center" >Totals:</td>
              <td align="center" ><?php echo $sales_invoice->total_due_with_tax; ?></td>
              
            </tr>
            <tr>
              <td align="right">Note:</td>
              <td align="right"><?php echo $sales_invoice->notes; ?></td>
              <td align="right"></td>
              <td align="center" style="background-color: #d4d4d4; font-size: 14px;"><b><?php echo $sales_invoice->base_currency_code; ?></b></td>
              <td align="center" style="background-color: #d4d4d4; font-size: 14px;"><b><?php echo $sales_invoice->total_due_with_tax; ?></b></td>
              
            </tr>

           </tbody>

        </table>    <br />
        <div class="terms">
          <p><b>Thank you for your business.</b></p>
          <p><b>Terms of business:</b><br />
                I certify that this claim is in all respects true, correct, supportable by available documentation, and in compliance with all the terms and
                         conditions, laws and regulations governing its payments.</p><br />
          <p>Your sincerely<br />
             <img src="<?= FCPATH?>assets/img/mysignature.jpg" alt="<?= FCPATH."assets/" ?> /img/mysignature.jpg" width="130" /><br />
             Balinder Walia<br />
             Cloud Native Engineer</p>
        </div>

        <div class="row">
          <p><b>Paying online</b><br />
              Please make payment by electronic transfer to one of the following bank accounts (Wise Payments Ltd is the preferred bank account):</p>
          <div class="col8">
            <table class="detail-1" cellpadding="3">
              
              <tbody>
                <tr>
                  <td><b>Bank account</b></td>
                  <td><img src="<?= FCPATH?>assets/img/wise.jpeg" alt="<?= FCPATH."assets/" ?> img/wise.jpeg" width="20"  /></td>
                </tr>
                <tr>
                 <td >Bank name :</td>
                 <td >Wise Payments Ltd</td>
               </tr>
               <tr>
                <td >Account name :</td>
                <td >Workstation SRL</td>
               </tr>
               <tr>
                <td>Country code iso :</td>
                <td>BEL</td>
               </tr>
               <tr>
                <td>IBAN :</td>
                <td>BE16 9672 4370 3974</td>
               </tr>
               <tr>
                <td>BIC code / SWIFT Code :</td>
                <td>BICTRWIBEB1XXX</td>
               </tr>
               <tr>
                 <td>Please use reference</td>
                 <td>INV-<?php echo $sales_invoice->invoice_number; ?></td>
               </tr>
            
              </tbody>
            </table>
          </div>  
          <br />   
          <div class="col8">
             <table class="detail-2" cellpadding="3">
              
              <tr>
                <td>Bank account</td>
                <td><img src="<?= FCPATH?>assets/img/ing.png" alt="<?= FCPATH."assets/" ?> img/ing.png" width="20"  /></td>
              </tr>
              <tr>
                 <td >Bank name :</td>
                 <td >ING BELGIUM</td>
               </tr>
               <tr>
                <td >Account name :</td>
                <td >Workstation SRL</td>
               </tr>
               <tr>
                <td>Country code iso :</td>
                <td>BEL</td>
               </tr>
               <tr>
                <td>IBAN :</td>
                <td>BE90 3632 0287 4732</td>
               </tr>
               <tr>
                <td>BIC code / SWIFT Code :</td>
                <td>BBRUBEBB</td>
               </tr>
               <tr>
                 <td>Please use reference</td>
                 <td>INV-<?php echo $sales_invoice->invoice_number; ?></td>
               </tr>
            </table>
          </div>
            
          
        </div>
        <br />
        <p><b>To make payment via Internet Banking, you simply need to contact your bank with the above bank details. They can advise<br />
              you if you should use the SWIFT Code or BIC code depending on the payment originating country.</b></p><br />

        <p>Workstation SRL, is a registered Ltd company in Belgium, company number: 752. VAT number: BE0751.518.683. <br />
           Registered in Belgium. Registered office address: Avenue de Roodebeek 78, Box 59
           Diamant, Schaerbeek, Brussels
           Belgium, 1030.</p>      

        <p>Note: This Invoice was generated by  Workstation CRM and Invoicing system automatically.</p><br />
        <p>Date :<?php echo date("d-m-Y", time()); ?></p>
        <p>Time :<?php echo date("h:i:sa"); ?></p>

        
            
            
          
        
                           
                    

                    
                
            

        

















<!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
  </body>
</html>