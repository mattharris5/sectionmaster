<?
	$order = $db->getRow("SELECT * FROM orders WHERE order_id = '{$_SESSION['order_id']}'");
		if (DB::isError($order)) { dbError("Could not get order data", $order, ''); }
?>

<h2 style="display: inline; float: left;">Order Complete!</h2>

<form action="<? print $_GLOBALS['form_action']; ?>" method=post style="display: inline; float: right;">
	<input type=hidden name="session" value="destroy">
	<input type=hidden name="event_id" value="<?=$EVENT['id']?>">
	<input type=submit value="Start a New <?=$EVENT['casual_event_name']?> <?= $EVENT['type']=='event' ? "Registration" : "Order" ?>"
							 style="float: right; width: auto; margin-top: 10px;">
</form>

<br>Thank you for your order!  This page serves as your receipt for this transaction, so you may wish to print it out.  You will also be sent this information in an e-mail momentarily.

<fieldset><legend>Online Order Receipt: <?=getFullName($member)?></legend>

	<table class="receipt">

		<tr><td>
			<table>
			
				<tr><td><b>Order No.</b></td>
					<td><?=$EVENT['section_name']?>/<?=str_pad($EVENT['id'], 3, '0', STR_PAD_LEFT)?>-<?=str_pad($_SESSION['order_id'], 7, '0', STR_PAD_LEFT)?></td>
				</tr>
				
				<tr><td><b>Date/Time</b></td>
					<td><?=$order->order_timestamp?></td>
				</tr>
				
				<tr><td><b>Bill To:</b></td>
					<td><? print getFullName($member) . "
								<br>$member->address
								"; if ($member->address2 != '') print "<br>$member->address2";
						   print "	<br>$member->city, $member->state  $member->zip
								<br>$member->primary_phone
								<br>$member->email"; ?></td>
				</tr>
				
				<tr><td><b>Payment Info</b></td>
					<? if ($order->payment_method == 'cc'): ?>
						<td>Credit Card <?=$order->cc?>
							<br>Total Collected: $<?=number_format($order->total_collected, 2)?></td>
					<? elseif ($order->payment_method == 'cash'): ?>
						<td>Paid - Cash
							<br>Total Collected: $<?=number_format($order->total_collected, 2)?></td>
					<? elseif ($order->payment_method == 'check'): ?>
						<td>Paid - Check
							<br>Total Collected: $<?=number_format($order->total_collected, 2)?></td>
					<? elseif ($order->payment_method == 'other'): ?>
						<td>Paid - Other
							<br>Total Collected: $<?=number_format($order->total_collected, 2)?></td>
					<? elseif ($order->payment_method == 'free'): ?>
						<td>Paid - Free Order
							<br>Total Collected: $<?=number_format($order->total_collected, 2)?></td>
					<? elseif ($order->payment_method == 'custom1'): ?>
						<td><?=$EVENT['custom_payment_method1']?>
							<br><font color=red>Total Due: $<?=number_format($order->total_collected, 2)?></font></td>				
					<? else: ?>
						<td>Pay-at-Door
							<br><font color=red>Pay when you arrive at the event.</font></td>
					<? endif; ?>
				</tr>					
				
			</table>
		
			</td>
			
			<td>
			
				<table>
				
				<tr><td colspan=4><b>Ordered Items:</b></td></tr>

				<?
				$sql = "SELECT ordered_item_id, products.product_id, item, description,
							   product_options.product_option_id, quantity, ordered_items.price,
							   option_name, option_value, image_thumbnail, show_in_store, coupon
						FROM ordered_items
						LEFT JOIN products ON ordered_items.product_id = products.product_id
						LEFT JOIN product_options ON ordered_items.product_option_id = product_options.product_option_id
						WHERE order_id = '{$_SESSION['order_id']}'
						ORDER BY coupon ASC, show_in_store ASC";
				$ordered_items = $db->query($sql);
					if (DB::isError($ordered_items)) { dbError("Couldn't get ordered item product info", $ordered_items, $sql); }
					
				// show a row for each item
				while ($item = $ordered_items->fetchRow()) { $i++;
				
					// <tr>
					print "<tr>";
					
					// item & description
					print "<td id=\"description\"><font id=\"item\">$item->item</font>";
					if ($item->option_name != '')
						print "<li>$item->option_name: $item->option_value</li>";
					print "</font></td>";
							   
					// quantity
					print "<td id=\"quantity\">$item->quantity&nbsp;@</td>";
					print "<td align=right>" . number_format($item->price, 2) . "</td>";
					
					// item total
					$item_total = $item->quantity * $item->price;
					print "<td id=\"item_total\" align=right>$" . number_format($item_total, 2) . "</td>";
					
					// add to subtotal
					$subtotal = $subtotal + $item_total;
					
				}
				?>
	
				<tr id="subtotal">
					<td colspan=3 align=right>Subtotal</td>
					<td align=right>$<?=number_format($subtotal, 2)?></td></tr>
					
				<tr id="shipping">
					<td colspan=3 align=right>Shipping</td>
					<td align=right>$<?=number_format($order->shipping, 2)?></td></tr>
					
				<tr id="total" align=right><td colspan=3><b>Total&nbsp;<? print $order->paid!='1' ? "Due" : "Paid"; ?></b></td>
					<td align=right><b><?	
						$total_cost = $subtotal + $order->shipping;
						$total_cost = number_format((($total_cost < 0) ? 0 : $total_cost), 2); ?>
						$<?=$total_cost?></b></td></tr>
						
				</table>
				
				<br><center><input type=button value="Print this Page" onClick="window.print()"></center>
	
			</td>
		</tr>	
	</table>

</fieldset>

<? if ($EVENT['do_training'] != 0): 

$session_1 = $member->session_1;
$session_2 = $member->session_2;
$session_3 = $member->session_3;
$session_4 = $member->session_4;

if (($session_1!=1) or ($session_2!=2) or ($session_3!=3) or ($session_4!=4)):
?>
<fieldset><legend>Draft Training Schedule</legend>
	<p><strong>Note:</strong> This is only a DRAFT schedule.  Your training sessions are subject to change according to space availability and scheduling alterations, and will be finalized upon arrival at the event.  If you need to change or drop your selections, please do so when you check-in at the event.</p>
	
<?	// Print table of already-selected sessions  --- what a friggin mess, dude!
	if (($session_1!=1) or ($session_2!=2) or ($session_3!=3) or ($session_4!=4)) {
		print "<table class=\"classschedule\"><th colspan=3>$member->firstname's Training Schedule</th>";
	}	
	if ($session_1!=1) {
		$s1q=$db->query("SELECT college_prefix, college, course_number, session_name, session_length FROM training_sessions, colleges WHERE college=college_id AND session_id=$session_1"); $s1=$s1q->fetchRow();
		$tablecount++; print "\n<tr valign=top><td width=70>Session $tablecount</td><td width=60>$s1->college_prefix$s1->course_number</td><td>".stripslashes($s1->session_name);
		if (($s1->college!=$degree->college_major) and ($degree->college_major!=$unrestricted->college_id)) print " <i>(Elective)</i>";
		print $s1->session_length>1 ? "<br><i>This session will last $s1->session_length hours on your actual schedule.</i>" : "";	print "</td></tr>"; }
	if ($session_2!=2) {
		$s2q=$db->query("SELECT college_prefix, college, course_number, session_name, session_length FROM training_sessions, colleges WHERE college=college_id AND session_id=$session_2"); $s2=$s2q->fetchRow();
		$tablecount++; print "\n<tr valign=top><td width=70>Session $tablecount</td><td width=60>$s2->college_prefix$s2->course_number</td><td>".stripslashes($s2->session_name);
		print $s2->college!=$degree->college_major && $degree->college_major!=$unrestricted->college_id ? " <i>(Elective)</i>" : ""; 
		print $s2->session_length>1 ? "<br><i>This session will last $s2->session_length hours on your actual schedule.</i></td></tr>" : ""; print "</td></tr>"; }
	if ($session_3!=3) {
		$s3q=$db->query("SELECT college_prefix, college, course_number, session_name, session_length FROM training_sessions, colleges WHERE college=college_id AND session_id=$session_3"); $s3=$s3q->fetchRow();
		$tablecount++; print "\n<tr valign=top><td width=70>Session $tablecount</td><td width=60>$s3->college_prefix$s3->course_number</td><td>".stripslashes($s3->session_name);
		print $s3->college!=$degree->college_major && $degree->college_major!=$unrestricted->college_id ? " <i>(Elective)</i>" : "";
		print $s3->session_length>1 ? "<br><i>This session will last $s3->session_length hours on your actual schedule.</i>" : "";	print "</td></tr>"; }
	if ($session_4!=4) {
		$s4q=$db->query("SELECT college_prefix, college, course_number, session_name, session_length FROM training_sessions, colleges WHERE college=college_id AND session_id=$session_4"); $s4=$s4q->fetchRow();
		$tablecount++; print "\n<tr valign=top><td width=70>Session $tablecount</td><td width=60>$s4->college_prefix$s4->course_number</td><td>".stripslashes($s4->session_name);
		print $s4->college!=$degree->college_major && $degree->college_major!=$unrestricted->college_id ? " <i>(Elective)</i>" : ""; print "</td></tr>"; }
	if (($session_1!=1) or ($session_2!=2) or ($session_3!=3) or ($session_4!=4)) print "\n</table>"; ?>
</fieldset>

<? endif; endif;?>

<? if ($EVENT['type'] != 'tradingpost'): ?>
<fieldset><legend>BSA Tour Plan Requirement</legend>
	National and Regional policy is that tour plans are not required only when traveling by yourself or direct relatives.
	All other travel requires the use of an application filed with the appropriate council office. Tour plans must be used for travel to
	<?=$EVENT['casual_event_name']?>.  Remember, transporting non-family youth members is only permitted by adults over age 21.
	You can direct any questions concerning this policy to the Section Adviser, <?=$EVENT['section_adviser']?>.  Also see the
	<a href="http://www.scouting.org/scoutsource/HealthandSafety/GSS.aspx" target="_blank">Guide to Safe Scouting</a>.<br><br>
	The Region encourages each Section to promote the use of tour plans for all Order of the Arrow events.  However, it is not the
	Region's or Section's role to decide the exact implementation of this National Policy. That implementation is the responsibility
	of the individual Lodge and its Supreme Chief of the Fire.  Each Lodge is responsible for meeting with its Scout Executive and
	setting up their own internal guidelines for implementation of this National BSA policy.<br><br>
	<li><a href="http://www.scouting.org/filestore/pdf/680-014.pdf" target="_blank">Download Tour Plan Application</a></li>
	<li><a href="http://www.scouting.org/scoutsource/HealthandSafety/TourPlanFAQ.aspx" target="_blank">Read the Tour Plan FAQ</a></li>
</fieldset>

<fieldset><legend>Other Information</legend>

	Here's some information while you're getting ready for <?=$EVENT['casual_event_name']?>.  You might want to print this stuff out!
	
		<p><img src="register/images/default/clock.png" style="float: left;">	
		<div><?=stripslashes($EVENT['arrival_time_details'])?></div>

</fieldset>
<? endif; ?>


<? /*


			<TD CLASS="title">
			<!-- Title 1 -->
				Don't forget!
			</TD>
		</TR>
		<TR>
			<TD BGCOLOR="white">
			<!-- Story 1 -->
				
				Here's some information to remember while you're getting ready for Conclave.
				You might want to <A HREF="javascript:printit();" STYLE="font-size:10px;">print</A> out this page for reference.
				
				<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0>
				
				<?
				// Arrival time & details
					print "
					<TR><TD VALIGN=TOP><IMG SRC=\"images/conf/clock.png\"></TD>
						<TD><FONT CLASS=\"conf_title\">Check-in starts at 6 pm</FONT>
							<BR>You should arrive before the Opening Show at 9 pm on Friday, September 10.
							Arrive as early as 6 pm to start check-in.  When you get there, just tell them 
							your name and you'll be checked-in in seconds.
						</TD>
					</TR>";
						
				// If pay-at-door: Bring money 
					if ($order_info['cc_type'] == 'payatdoor'){
						print "
						<TR><TD VALIGN=TOP><IMG SRC=\"images/conf/money.png\"></TD>
							<TD><FONT CLASS=\"conf_title\">Bring $". sprintf('%.2f', $order_info['amount_paid']) ." to pay for your order</FONT>
								<BR>Since you chose the \"Pay-at-Door\" option, you will need to pay your registration
								fee when you check-in.
							</TD>
						</TR>";
					}
				
				// If ordered items: Pick up at Conclave
					if ($authorized = true) {
						$orderquery = "SELECT * FROM ordereditems
										LEFT JOIN products ON ordereditems.product=products.product_ID
										WHERE ordernum='".$order_info['ordernum_ID']."'";
						$orderresult = mysql_query($orderquery, $conn);
						
						if (mysql_num_rows($orderresult) > 0) {
						
							while ($products = mysql_fetch_array($orderresult)) {
								$desc = $products['product_code'];
								if ($desc != 'registration' && $desc != 'free_registration' && $desc != 'discount_registration') {
									$ordered_items_list .= "<LI>" . $products['quantity'] . " - " . $products['product_name'] . sprintf(' ($%.2f)', $products['product_price']) . "\n\t\t\t\t";
									$num_items++;
								}
							}
						
							if ($num_items > 0) {
								print "
								<TR><TD VALIGN=TOP><IMG SRC=\"images/conf/cart.png\"></TD>
									<TD><FONT CLASS=\"conf_title\">Pickup your items before 12 noon on Saturday</FONT>
										<BR>You ordered the following items from the Trading Post.  If you don't pick them up by noon on Saturday
											at Conclave, we may sell out of stock, so we cannot gaurantee that they'll be available past noon.
										<UL>
											$ordered_items_list
										</UL>
									</TD>
								</TR>";
							}
						}
					}
				
				// Bring a tent
					print "
					<TR><TD VALIGN=TOP><IMG SRC=\"images/conf/tent.png\"></TD>
						<TD><FONT CLASS=\"conf_title\">Bring a tent!</FONT>
							<BR>You will be camping on a nice, flat soccer field.  Be sure to bring a tent and sleeping bag.
								You might want to collaborate with a friend so you don't each have to bring your own tent.
						</TD>
					</TR>";
				
				// Wear your uniform
					print "
					<TR><TD VALIGN=TOP><IMG SRC=\"images/conf/uniform.png\"></TD>
						<TD><FONT CLASS=\"conf_title\">Wear your Uniform</FONT>
							<BR>You should wear your Class A or Field Uniform to Conclave.  It's tons of fun, but it's still
								a Scouting event.
						</TD>
					</TR>";
				
			?>
			</TABLE>
			</TD>
		</TR>
</TABLE>

<P><TABLE CELLPADDING=4 BORDER=0 BGCOLOR=#AE2424 WIDTH=620>
		<TR>
			<TD CLASS="title">
			<!-- Title 1 -->
			Directions to Conclave
			</TD>
		</TR>
		<TR>
			<TD BGCOLOR="white">
			<!-- Story 1 -->
					<TABLE CELLPADDING=10 CELLSPACING=0 BORDER=0>
					<TR><TD VALIGN=TOP><IMG SRC="images/conf/directions.png"></TD>
						<TD>
							<TABLE CELLPADDING=5 BORDER=0>
							<TR><TD COLSPAN=2>
									<FONT CLASS="conf_title">Directions to Conclave 2004</FONT>
								</TD>
								<TD ROWSPAN=2 VALIGN=TOP>
									<IMG SRC="images/mapquest.gif" ALIGN=RIGHT WIDTH=175>
								</TD>
								</TR>
							<TR><TD>
									<B>Start:</B><BR>
									<? print $member_info['address'] . "<BR>" . $member_info['city'] . ", " . $member_info['state'] . " " . $member_info['zip']; ?>
									</TD>
								<TD>
									<B>End:</B><BR>
									Conclave 2004<BR>Cheldelin Middle School
									</TD>
								</TR>
							</TABLE>
								
								
					<?
					print"</TD>
					</TR></TABLE>";
						
						if (!isset($address)) {	
							$address = $member_info['address'];
							$city    = $member_info['city'];
							$state   = $member_info['state'];
							$zip     = $member_info['zip'];
						}
						
						$mapquest_url = 'http://www.mapquest.com/directions/main.adp?do=prt&2a=987+NE+Conifer+Blvd&2c=Corvallis&2s=OR&2z=97330-4019&do=nw&1ex=1&2ex=1&src=maps&ct=NA&go=1&1a='.urlencode($address).'&1c='.urlencode($city).'&1s='.urlencode($state).'&1z='.urlencode($zip).'&submit=Get+Directions';
						if ($directions = implode('', file($mapquest_url))) {

						$posStart = strpos($directions, "<b>Distance:</b>");
						$posEnd = strpos($directions, "<tr><td><b>Notes:</b>");
						$posLen = strlen($directions) - $posStart - (strlen($directions)-$posEnd);
						$directions_body = substr($directions, $posStart, $posLen);
						
							if ($directions_body != '') {
								print "
								<!-- START MAPQUEST DRIVING DIRECTIONS -->
								<table cellpadding=0 cellspacing=0 border=0>
								  <tr>
									<td>
								<!-- AMS search term = or+national+usa -->
							
								  <table width=100% cellpadding=0 cellspacing=0 border=0>
									<tr>
									  <td>
										 <table width=100% cellpadding=0 cellspacing=0 border=0>
												   <tr>
												 <td colspan=2 valign=top>
												   <table width=100% cellpadding=0 cellspacing=0 border=0>
								
													 <tr>
													   <td valign=top class=size12 width=50 align=left>";
	
							
								
								// print the huge file
								print "\n<!-- MAPQUEST CODE IS AUTO-INSERTED HERE -->\n";
								print $directions_body;
								
								
								print "<FONT CLASS=\"footer\">These directions are informational only. No representation is made or warranty
										given as to their content, road conditions or route usability or
										expeditiousness. User assumes all risk of use. MapQuest and its suppliers
										assume no responsibility for any loss or delay resulting from such use. Copyright &copy; 2004 MapQuest.com, Inc.";
																		
								print "</table></td></tr></table></td></tr></table>
											  </td>
											</tr>
										  </table>
								 
										</td>
									  </tr>
								
									</table>";
						} else {
						
							print "<P><CENTER><FONT COLOR=RED><B>Sorry, your address couldn't be found by Mapquest!  Try again:</B><A HREF=\"$mapquest_url\" STYLE=\"color:white;\">XXX</A></FONT></CENTER></P>";
							print "<FORM ACTION=\"https://secure.webhostworks.net/secure/w1a.org/oareg/final.php\" METHOD=GET>
										<INPUT TYPE=hidden NAME=\"id\" VALUE=\"$id\">
										<INPUT TYPE=hidden NAME=\"pw\" VALUE=\"$pw\">
										<CENTER><TABLE CELLPADDING=0 CELLSPACING=1>
											<TR><TD>Address or Intersection</TD>
												<TD>City</TD>
												<TD>State</TD>
												<TD>Zip</TD>
											</TR>
											<TR><TD><INPUT TYPE=text NAME=\"address\" VALUE=\"$address\"></TD>
												<TD><INPUT TYPE=text NAME=\"city\" VALUE=\"$city\"></TD>
												<TD><INPUT TYPE=text SIZE=5 NAME=\"state\" VALUE=\"$state\"></TD>
												<TD><INPUT TYPE=text SIZE=10 NAME=\"zip\" VALUE=\"$zip\"></TD>
											</TR>
											<TR><TD COLSPAN=4><CENTER><INPUT TYPE=submit VALUE=\"Get Directions\">
																<INPUT TYPE=checkbox NAME=\"doaddressupdate\" VALUE=\"true\" checked>Update my Conclave record with this new address</TD>
											</TR>
										</CENTER></TABLE>
									</FORM>";
							
						}
					}
					
				?>			
			</TD>
		</TR>
</TABLE>
*/ ?>