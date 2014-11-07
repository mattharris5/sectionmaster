<div id="toc_container">

<strong><a href="index.php">SM-ONLINE ADMIN MENU</a></strong>
<font color="#aaaaaa">User: <strong><?=$user->info->firstname?> <?=$user->info->lastname?> (<?=$user->info->section?>)</strong></font>

<menu class="toc">

	<li id="l1"><a href="javascript:toggleMenu('section')">					SECTION	<?= strtoupper($user->info->section) ?>						</a>
		
			<? if ($user->peekPermissions("members")): ?>
  		<li id="l2"><a href="section/members.php">		Members						</a> <? endif; ?>

			<? if ($user->peekPermissions("switch_id")): ?>
			<li id="l2"><a href="other/switchid.php">		Switch ID #s			</a> <? endif; ?>


  		<? if ($user->peekPermissions("users")): ?>		
  		<li id="l2"><a href="section/users.php">			Admin Users					</a> <? endif; ?>
  		
  		<? if ($user->peekPermissions("mass_email")): ?>
  		<li id="l2"><a href="section/massmail2.php">		Mass E-mailer				</a> <? endif; ?>
		
  		<? if ($user->peekPermissions("superuser")): ?>
  		<li id="l2"><a href="section/massmail.php">		Mass E-mailer(old)				</a> <? endif; ?>
  		
  		<? if ($user->peekPermissions("section_info")): ?>
  		<li id="l2"><a href="other/chapters.php">			Chapters				</a> <? endif; ?>
	
	<? if ($user->info->current_event == 0): ?>
		<li id="l1"><a href="event/select.php">			EVENT						</a>
			<li id="l2"><a href="event/select.php">		Select an event				</a>
	<? else: 
		$event = $user->db->getRow("SELECT * FROM events WHERE id='{$user->info->current_event}'"); ?>

	<li id="l1"><a href="event/index.php">						<?= $event->formal_event_name ?>						</a>
				<a href="event/select.php" style="margin:0; padding-left: 15px; font-size: 75%;">(Select another event)</a>

		<? if ($user->getPermissions("event_prefs") > 2 ): ?>
		<li id="l2"><a href="event/info.php">			Event Prefs					</a> <? endif; ?>

	<? if ($event->type == 'event'): ?>

		<? if ($user->peekPermissions("registrations")): ?>
		<li id="l2"><a href="event/registrations.php">	Registrations				</a> <? endif; ?>
		
		<? if ($user->peekPermissions("members")): ?>
		<li id="l2"><a href="event/export.php">	Export Data				</a> <? endif; ?>

		<? if ($user->peekPermissions("members")): ?>
		<li id="l2"><a href="event/housing/locations.php">	Housing				</a> <? endif; ?>

		<? if ($user->peekPermissions("training") && $event->do_training==1): ?>
		<li id="l2"><a href="event/training.php">		Training			</a>
			<? endif; ?>
			
		<? if ($user->peekPermissions("report_eval")): ?>
		<li id="l2"><a href="other/eval_questions.php">		Evaluation ?'s			</a>
			<? endif; ?>

		<li id="l2"><a href="event/reports.php">			Reports						</a>

		<? if ($user->peekPermissions("report_summary")): ?>
		  <li id="l3"><a href="event/report_mailer.php">				E-mailer					</a> <? endif; ?>
			
		<? /*if ($user->peekPermissions("report_summary")): ?>
		  <li id="l3"><a href="event/stats.php">				Old E-mailer					</a> <? endif;*/ ?>		

		<? if ($user->peekPermissions("report_summary")): ?>
		  <li id="l3"><a href="event/reports.php?report=summary">				Summary					</a> <? endif; ?>

		<? if ($user->peekPermissions("report_lodge")): ?>
		  <li id="l3"><a href="event/reports.php?report=lodge">				Lodge				</a> <? endif; ?>

		<? if ($user->peekPermissions("report_chapter")): ?>
		  <li id="l3"><a href="event/reports.php?report=chapter">				Chapter				</a> <? endif; ?>

		<? if ($user->peekPermissions("report_lodge")): ?>
		  <li id="l3"><a href="event/reports.php?report=out_of_section">				Out-of-Section				</a> <? endif; ?>


		<? if ($user->peekPermissions("report_tradingpost") && $event->do_tradingpost==1): ?>
		  <li id="l3"><a href="event/reports.php?report=tradingpost">				Trading Post			</a> <? endif; ?>

		<? if ($user->peekPermissions("report_training") && $event->do_training==1): ?>
		  <li id="l3"><a href="event/reports.php?report=training">				Training				</a> <? endif; ?>
			
		<? if ($user->peekPermissions("report_diet")): ?>
		  <li id="l3"><a href="event/reports.php?report=diet">				Dietary Needs			</a> <? endif; ?>

		<? if ($user->peekPermissions("report_medical")): ?>
		  <li id="l3"><a href="event/reports.php?report=medical">				Emer. Contacts			</a> <? endif; ?>

		<? if ($user->peekPermissions("report_eval")): ?>
		  <li id="l3"><a href="event/reports.php?report=eval">				Evaluation			</a> <? endif; ?>

		<? if ($user->peekPermissions("vicarious_login")): ?>
		<li id="l2"><a href="event/vlogin.php">			Vicarious Login				</a> <? endif; ?>

		<? if ($user->peekPermissions("paper_reg")): ?>
		<li id="l2"><a href="event/paper.php">			Input Paper Reg.			</a> <? endif; ?>

	<? endif; ?>
	<? endif; ?>
	
	<li id="l1"><a href="tradingpost/index.php">				TRADING POST					</a>

		<? if ($user->peekPermissions("products")): ?>
		<li id="l2"><a href="tradingpost/products.php">		Products/Inventory			</a> <? endif; ?>

		<? if ($user->peekPermissions("coupons")): ?>
		<li id="l2"><a href="tradingpost/coupons.php">		Coupons						</a> <? endif; ?>

	
	<!--li id="l1"><a href="other/index.php">						OTHER							</a>
		<li id="l2"><a href="other/profile.php">			Edit My Profile				</a>

		<? if ($user->peekPermissions("request_refund")): ?>
		<li id="l2"><a href="other/refunds.php">			Request Refund				</a> <? endif; ?>
			-->	

	<li id="l1"><a href="other/changepass.php">		Change Password			</a>
	<li id="l1"><a href="index.php?logout=logout">						LOGOUT							</a>

		
</menu>

</div>