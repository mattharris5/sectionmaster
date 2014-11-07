SectionMaster 3.2.01 Readme
===========================

© 2001-2004
Rob J. Rosamond
Co-Founder, Network Specialist
Clostridion Design & Support, LLC
rob@cdssalem.com

---------------------------

CONTENTS

I.	Legal & Distribution

II.	Preface

III.	Introduction

IV.	General Information

V.	Version History

---------------------------

LEGAL & DISTRIBUTION

Distribution of this software in its original form is encouraged, royalty-free.  Changes and suggestions are welcomed; please share your modifications with the author so that future versions may include your ideas and concepts.  SectionMaster and all its original modules and features are intellectual property of its author.  This "About" form with copyright information and all references to it must remain intact.

This software is distributed as-is and contains no warranty, express or implied.  The author is not responsible for data loss or software misuse, or any other situation resulting in litigation.

---------------------------

PREFACE

Seeing the need for a centralized contact and event management database for a Conclave of over 400 participants in 2001, SectionMaster was born.  The database now has the ability to handle the entire registration process, training signup and management, and auction management.

Though these are three separate functions in the database, SectionMaster makes it convenient by tying personal contact information together.  In a snap, a personalized training schedule and nametag can be printed.  In a wink, a personalized auction invoice can be printed.  In a flash, a registration confirmation sheet can be generated.

The system was designed to be implemented on a network of several computers and a few networked printers.  An ideal conclave network should consist of six computers, at least two laser printers, and a server.  DO NOT use a workstation as a server; that can prove disastrous when and if it crashes, making all the other computers go down as well.  By having a separate machine just to store the SectionMaster data, little or no network downtime should ever occur.

Use of SectionMaster is provided purely as a service to OA brothers across the nation.  You may add and modify forms and reports; all I ask is that you simply retain the copyright and contact information of the original author at all times.

---------------------------

INTRODUCTION

You will notice that this Readme file isn't very detailed at all.  Unfortunately, I can't invest the time to produce a massive support document for SectionMaster.  As a result, you probably won't find the answer to every question here.  In fact, I'm positive you won't, and I apologize for that.

The goal of this readme is simple: to tell the user the very vital information required for a near-expert to make this system work for himself.  I'm sure that you understand that because this is a volunteer-produced program, support would be impossible to include.  I wish it didn't have to be that way, but at the same time, I also want everyone to be able to have access to a quality program for their own use.

---------------------------

GENERAL INFORMATION

The following notes are few and scattered, and are in no particular order.  I recommend that you just read all of them before you even attempt to begin using the program.

--> Scroll down for a VERSION HISTORY as well as a list of features new to the current edition <--

1) This was written in Microsoft Access 2000.  It may run on Access 97, provided that in 2000 you go to the Tools menu, point to Database Utilities, and then select Convert Database... To prior Access version.  You can also upgrade this database to Access XP, but beware that it will not format nicely back to 2000.  This is a known issue that all database developers are screaming to Microsoft that they have to upgrade all their machines to have Access XP for their system to work.  There is a way around this, however, and it is called Database Splitting.  This is where the actual data of a database is stored in a separate file and Access doesn't care what version it is from.  The "front-end design" of the rest of the database (forms, reports, macros, & modules) is stored in a format that will work with the version of Access that is specific to that particular machine.  For more information on this, read the help files.

2) SectionMaster DOES utilize a split-database system.  The primary reason for this is to decrease network traffic when SectionMaster is implemented in a network environment.  If you were to make the database "program" file (where all forms, macros, and reports are stored) merged with the data and then you tried to share that one file (which you CAN do) over a network, not only would the server have to transmit the data, but also all the "program" data (forms, macros, reports).  Raw data is much smaller than program data.  Note that when you install SectionMaster, the install program will put both the program file and the data file in C:\Program Files\SectionMaster\.  In a networked environment, this would not work, as you would probably have a mapped network drive from which you pull the data file.  SectionMaster is currently configured to take the data from the C:\ drive, however.  You must change this Manage Data Source area in the Utilities menu.  Check every table, then check "Always prompt for new location" and then click OK.  It will prompt you for the real location of the Data.mdb file, and you can point that to anywhere pretty much (network drive, another folder on your hard drive, etc.).

3) There are many many many menus, with oh-so many options for you to choose from.  I tried to divide the database up into three different areas.  Take Registration for one.  Within here you have the standard data entry, a bunch of reports, etc.  To navigate through the records, use the Page Up and Page Down keys, or the scroll wheel on your mouse (if you have one).  Note that we've kept track of who came to conclave the previous year, as well.  You'll need to configure the various different chapters and lodges in your section.  SectionMaster has the ability to calculate percentages of active membership that are present at conclave according to chapter.  In our section, there were incentives for chapter chiefs to get people to Conclave, so I added this tool and report.  Check out all the various reports and statistics that the program can generate, too.

4) The program manages the entire training system.  There is already an entire catalog of training sessions built in with descriptions (in most cases) and everything.  All you have to do is go in and activate the training classes you want, type in the name of the trainer, and assign class locations (which must be configured just as you did with the chapters).  Locations have configurable capacities.  During registration AT Conclave, simply monitor the enrollment report to see when class sizes are reaching capacity.  Then go in and check the "Class Full" field to mark the class as being listed "FULL" in the drop-down menus on the Check-in form.  There are several different reports that are useful for trainers and for training committee staff.  At any point, it is easy to go in and print a training schedule for any person who requests one.

5) The auction system is quite efficient.  It was designed so that when an item had been won either from a silent or oral auction, it is brought over to the data entry/cash-out table.  Someone then enters a pre-determined ID number for the item, hits enter, enters the bidder ID number (which is the same as the Conclave ID Number that prints on the ID badges, invoices, and everything else), hits enter, enters the amount it was won for, hits enter twice, and the item is now saved.  Then you can go in and search for a person and check the items as they pay for them.  At any time during the auction you can print customized invoices for items that have not yet been paid for, and you can also get live statistics as to how much money the auction is raising, how much money has been collected, and how much has yet to be collected.  There are several reports for this section, as well.

6) Since the program file for SectionMaster is not shared over the network, each user has the capability to go in and edit reports while other users are doing data entry or using the system.  When you have a copy of the program file that is good, be sure to keep a backup.  That way if the program file gets messed up, you can simply copy the known good program file over the network to that computer.

---------------------------

VERSION HISTORY

SectionMaster 3.2.01 (November 2004) --

1)  Fixed problem with "Print" option in File menu.  Previously, reports would be immediately sent to the printer.  Now a print dialog box appears as is custom in most applications.  The menu configuration in the previous release (3.2) was the result of a problem addressed in Microsoft KnowledgeBase Article 304391 ("ACC2000: Print Command on Custom Menu Causes Object to Be Printed Immediately Without Showing Print Dialog Box"): http://support.microsoft.com/default.aspx?scid=kb;en-us;304391

SectionMaster 3.2 (October 2004) --

1)  Improved navigation: Eliminated hard-to-browse switchboard system and created a drop-down menu interface.  This allows for easy switching between tasks and instant access to needed tools and reports.  The Quick Launch gives easy access to the most commonly-needed reports and features.  The Fast Printing menu provides access to the common batch print jobs that need to be done on-the-fly.

2)  Automated processes: Several tasks could previously only be done by doing manual data entry or by using the back-end of the database to administer it.
	a) The current year is determined by the "Year" field in the Conclave & Section Information form.  No more editing of queries to determine the participants of the current year.
	b) Advance Degree Status - A feature available in the Pre-Conclave Tasks portion of the Utilities menu, the updates will promote participants to the next degree.
	c) Pre-Conclave Update - Another feature available in the Pre-Conclave Tasks portion of the Utilities menu, this update sets default or null values for fields that normally contain conclave-specific data that needs to be cleared between events.

3)  Manage Data Source: This item in the Utilities menu provides fast access to the Linked Table Manager to change the location of your data file.  This is especially useful if you need to move your data file from the default C:\Program Files\SectionMaster location, such as a network path.

4) Registration Improvements
	a) Added some error-checking to the Check-in form to prevent incomplete data from being captured which causes some reports to err.
	b) Added Check-out form to allow for easy early departures, as well as easy marking of records once the rest of the participants have left the event.
	c) Added dynamic brotherhood eligibility checking and first-time attendee checking, with indications on various reports.
	d) Added various attendance statistics reports.  One shows the attendance by day as well as by full 24-hour periods, which is useful for insurance fee purposes.  The other summarizes the numbers of participants that paid various fees for the event.
	e) Imported lodge information for all Western region lodges, as well as the ability to indicate which lodges are local to your section so they appear at the top of the list on various forms and reports.
	f) Registration Confirmation form alerts registration staff to imcomplete information with yellow highlights in blank fields.

5) Training Improvements
	a) Added designation on session location signs to indicate whether the sessions are OPEN or FULL, depending on their designations in the "Class Full?" field.
	b) Integrated entire 2-sided design of training degree so that they can be dynamically printed at any given time.
	c) Created a degree summary to show statistics of how many of each type were awarded in each college.
	d) Created a tool to advance the status of degrees for each participant.  See sub-section 2b of this version history section.

6) Auction Improvements
	a) Automatically figures out what format to print invoices based on the number of items won.
	b) Live updates to the total amount due based on the items marked as having already been paid-for.

SectionMaster 3.0 (September 2003) --

1)  "Conclave Information" Utility: This utility allows you to easily change your section name, conclave date, location, logo, and other information.  You can also specify the starting and ending date/time.  This allows you to pull reports based on those who have actually checked in, and during a specified time range.  In previous versions, this information was hard-coded into each report.  This allows one change to affect every report in the entire program.  The information is stored in a table in the data.mdb companion file.

2)  "Staff Classification" Utility: This utility allows you to edit the various staff classifications.  These are stored in a table in data.mdb, and can be selected in the drop-down box on the registration form.

3)  New and improved check-in form: The registration check-in form has been completely re-designed.  It is now much more aesthetically pleasing to do data entry, and divides the registration process into six steps or tabs.  The tabs can be easily navigated through using ALT + the tab number.  Hint: Any letter that is underlined is an ALT+ hotkey.

4)  Better searching capabilities: You can still use the old-fashioned CTRL+F to find a record based on the field which you have the cursor in at any given time.  However, a new red search bar has been added to the most commonly-used forms to make searching easier.  You can even search using the drop-down box, which makes it easy to see if there are duplicate records for any one individual.

5)  Easier Express Check-in Process: Simply type ID numbers on the number pad and press enter.  The records are updated with the computer's current date and time.  In 2.0 you had to do CTRL+F, type the ID number, hit enter, close the search box, update the record, and search again, which was a very tedious process.  The new process makes express check-in truly "express!"

6)  Easier Printing of Large Reports: For the registration confirmation sheets and the training schedules, you are now prompted to select the staff classification for which you'd like to print the group of records.  This is useful if you are printing on different colored paper for each of these groups.

7)  Better Control of Payment Information: On the registration confirmation sheets and the training schedules there is now a visual "for staff use only" box that will print PAID in large bold print if one of the following is true:
	a) The Receipt/Notes field is equal to the Free Auth Code in the Conclave Info Utility
	b) The Receipt/Notes field is equal to "Mail-in: Paid"
	c) The Amount Paid field is greater than or equal to the Discount Price in the Conclave Info Utility

8)  Improved Statistical Reports: Attendance reports now are easier to read and include useful subtotals, broken down by Youth/Adult, Chapter, or Lodge.

SectionMaster 2.0 (October 2002) -- First publicly released version.

THANK YOU FOR YOUR SUPPORT TO SECTIONMASTER!