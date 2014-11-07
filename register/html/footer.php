</div><!-- End #main-content -->
</div><!-- End #content -->

<div class="clear">&nbsp;</div>

<div id="footer">

    <table width="100%" border="0" cellspacing="5">
      <tr>
        <td><span style="font-size: 10px">	
          <p>Copyright &copy; 2005-<?=date('Y')?> SectionMaster.<br>
            <?
            if ($EVENT) {
                if ($EVENT['type']=='tradingpost') print "Trading Post"; else print "Event registration";
                print " services for {$EVENT['org_name']} are managed by SectionMaster,<br>a division of Clostridion Design & Support, LLC.
                <br>Some information on this page may be copyrighted by {$EVENT['org_name']}.";
            }
            ?>
            
           <br><a href="http://www.sectionmaster.org/about/privacy.php" target="_blank">Privacy Policy</a>
                | <a href="http://www.sectionmaster.org/about/refund.php?event_id=<?=$EVENT['id']?>" target="_blank">Refund Policy & Customer Service</a>
           </p>
          </span></td>
                  <td><div align="right"><a href="https://sealserver.trustkeeper.net/compliance/cert.php?code=w6oj3BWieJra4gUyxZfZ1ih5Q669qj&style=invert&size=105x54&language=en" target="hATW"><img src="https://sealserver.trustkeeper.net/compliance/seal.php?code=w6oj3BWieJra4gUyxZfZ1ih5Q669qj&style=invert&size=105x54&language=en" border="0" alt="Trusted Commerce" align="right"/></a>
				  
		<!-- (c) 2005, 2013. Authorize.Net is a registered trademark of CyberSource Corporation --> <div class="AuthorizeNetSeal" style="position:relative"> <script type="text/javascript" language="javascript">var ANS_customer_id="3740733f-eaf5-4c11-9bd5-13c056336024";</script> <script type="text/javascript" language="javascript" src="//verify.authorize.net/anetseal/seal.js" ></script> <a href="http://www.authorize.net/" id="AuthorizeNetText" target="_blank">Payment Processing</a> </div>
				  
				  </div></td>
      </tr>
    </table>


</div>

</div> <!-- end #wrap -->

<? include "html/processing.php"; ?>


<!-- c(~) -->
</body>
</html>