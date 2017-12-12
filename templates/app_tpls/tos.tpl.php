<script src = "js/jquery-ui.min.js" type = "text/javascript" ></script >

<script type = "text/javascript" >
    shouldsubmit = false;
    $(document).ready(function () {
        dialog = $("#dialog-confirm").dialog({
            resizable: false,
            autoOpen: false,
            height: 500,
            width: 900,
            modal: true,
            buttons: [
                {
                    "text": "Accept & Login",
                    "class": "btn btn_disabled acc_login",
                    click: function () {
                        shouldsubmit = true;
                        $("#login_form").submit();
                        $(this).dialog("close");
                    }
                }
                ,
                {
                    "text": "Change credentials",
                    "class": "btn btn_cancel",
                    click: function () {
                        $(this).dialog("close");
                    }
                }
            ]
        });
        jQuery('div.ui-dialog-buttonpane').append('<div class="acc_tos_div"><input type="checkbox" id="acc_top_chb"> <label style="display:inline" for="acc_top_chb">I have read and accepted these Terms</label></div>');
        jQuery('.acc_login').attr('disabled', 'true');
        jQuery('#acc_top_chb').bind('change', function () {
            if (jQuery(this).is(':checked')) {
                jQuery('.acc_login').removeAttr('disabled').removeClass('btn_disabled');
            } else {
                jQuery('.acc_login').attr('disabled', 'true').addClass('btn_disabled');
            }
        });

        form = $("#login_form").on("submit", function (event) {
            if (!shouldsubmit) {
                event.preventDefault();
                dialog.dialog("open");
            }
        });
    });
</script >

<style >
    h2 {
        line-height: 40px;
        font-size: 31.5px;
        margin: 10px 0px;
    }
</style >


<div id = "dialog-confirm" title = "You must accept the updated Terms of Use before you can login." style = "display: none" >

    <section id = "mainline" >
        <div class = "wrraper" ><h2 >Terms of Use</h2 ></div >
    </section >
<div class="wrraper">
<h3><strong>TOP TRADELINES&nbsp;TERMS AND CONDITIONS</strong></h3>
<p><em>last updated July 29, 2015</em></p>
<p>TopTradelines.com (“TTL” or the “Service”) promotes and sells tradelines for business use by individuals and entities, and is offered through the URL www.TopTradelines.com (“Website”). TTL is owned and operated by 99th Floor, LLC d/b/a TopTradelines, a Florida limited liability company (“TTL” “we,” or “us”). TTL has officers, employees, and contractors (“our Team”). As a user of the Service or a representative of an individual or entity that’s a user of the Service, you’re a “User” according to this agreement (or “you”).</p>
<p>Please read these Terms carefully. This is a legal agreement. By using TopTradelines.com or by checking the box and pushing the “Submit” buttons, you are electronically signing this Agreement and agreeing to be legally bound by its terms and conditions. If you do not agree to any of the terms below, TTL is unwilling to provide the Service to you, and you should not click on the “Accept” button, but instead you should leave the website or mobile application to discontinue the registration process.</p>
<h3><strong>A. TERMS OF USE</strong>- applicable to ALL Users</h3>
<p><strong>1. Eligibility</strong><br>
In order to use TTL, you must:</p>
<p style="padding-left: 30px;">1. be at least eighteen (18) years old and able to enter into contracts;<br>
2. complete the registration process;<br>
3. agree to the Terms;<br>
4. provide true, complete, and up to date contact information;<br>
5. only use the Service for business purposes; not for personal, family, nor household purposes; and<br>
6. not use the Website or Services for any purpose that is unlawful or prohibited by this Agreement.<br>
By using TTL, you represent and warrant that you meet all the requirements listed above, and that you won’t use TTL in a way that violates any laws or regulations. TTL may refuse service, close accounts of any users, and change eligibility requirements at any time.</p>
<p><strong>2. Term</strong><br>
The Term begins when you use this Website or when you click both “Accept” buttons and continues as long as you use the Service. Clicking the button, checking the box and entering your username means that you’ve officially “signed” the Terms. If you sign up for TTL on behalf of a company or other entity, you represent and warrant that you have the authority to accept these Terms on their behalf.</p>
<p><strong>3. Closing Your Account</strong><br>
You or TTL may terminate this Agreement at any time and for any reason by giving Notice to the other party. We may suspend our Service to you at any time, with or without cause. If we terminate your account without cause, we’ll refund the balance due to you. We won’t refund or reimburse you if there’s cause, like a violation of these Terms. Once terminated, we may permanently delete your account and all the data associated with it.</p>
<p><strong>4. Changes</strong><br>
We may change any of the Terms by posting revised Terms of Use on our Website or by sending an email to the last email address you gave us. Unless you terminate your account within ten (10) days, the new Terms will be effective immediately and apply to any continued or new use of TTL. We may change the Website, the Service, or any features of the Service at any time.</p>
<p><strong>5. Account and Password</strong><br>
You’re responsible for keeping your account name and password confidential. You’re also responsible for any account that you have access to, whether or not you authorized the use. You’ll immediately notify us of any unauthorized use of your accounts. We’re not responsible for any losses due to stolen or hacked passwords. We don’t have access to your current password, and for security reasons, we may only reset your password.</p>
<p><strong>6. Payment</strong><br>
As long as you’re a User or have an outstanding balance with us, you’ll provide us with valid payment information and authorize us or our third party designee to process your payment of the outstanding balance using that payment information. You’ll replace the payment information if it is not valid. Anyone paying by credit card, electronic check or other payment method represents and warrants that he or she is authorized to use that payment method, and that any and all charges may be billed using that payment method and won’t be rejected. If, for some reason, we’re unable to process your payment, we’ll try to contact you by email and suspend your account until your payment can be processed.</p>
<p><strong>7. Refunds</strong><br>
TTL does not issue refunds. TTL shall issue replacement tradeline(s), or a credit which may be used to purchase any products or service that 99th Floor LLC provides. Our Refund Policy (hyperlink) shall be treated as part of these Terms.</p>
<p><strong>8. Changes</strong><br>
We may change our fees at any time by posting a new pricing structure to our Website or sending you a notification by email.</p>
<h3><strong>RIGHTS</strong></h3>
<p><strong>9. Proprietary Rights Owned by Us</strong><br>
You shall respect our proprietary rights in the Website and the software used to provide TTL (proprietary rights include patents, trademarks, service marks, and copyrights).</p>
<p><strong>10. Proprietary Rights Owned by You</strong><br>
You represent and warrant that you either own or have permission to use all of the material that you upload to this Website or communicate to us. You retain ownership of the materials you upload to the website or otherwise submit to us. You grant TTL a perpetual, irrevocable, unlimited, worldwide, fully paid/sub licensable license to use, copy, perform, display, distribute, and make derivative works from content you post.</p>
<p><strong>11. Privacy Policy</strong><br>
We may use and disclose your information according to our Privacy Policy (YOUR PRIVACY POLICY hyperlink). Our Privacy Policy will be treated as part of these Terms.</p>
<h3><strong>RULES AND PROHIBITED CONDUCT</strong></h3>
<p><strong>12. Prohibited Actions</strong></p>
<p>You shall not:</p>
<p style="padding-left: 30px;">• Share your password.<br>
• Decipher, decompile, disassemble, or reverse engineer any of the software on our Website, or in any way used or downloaded from the Website.<br>
• use or provide software or services that interact or interoperate with Us, including but not limited to downloading, uploading, posting, flagging, emailing, search, or mobile use. Robots, spiders, scripts, scrapers, crawlers, etc. are prohibited. You agree not to collect users’ personal and/or contact information (“PI”).</p>
<p><strong>13. Mutual Non-Disparagement</strong><br>
You agree and warrant and that you shall not disparage or comment negatively, directly or indirectly, about TTL, or its Team. We agree and warrant and that we shall not disparage or comment negatively, directly or indirectly about you; except we may reports debts owed by you to us to Credit Reporting Agencies. Disparagement shall be defined as any remarks, comments or statements that impugn the character, honesty, integrity, morality, business acumen or abilities in connection with any aspect of our dealings with each other. You and TTL agree and acknowledge that this non-disparagement provision is a material term of this Agreement; the absence of which would have resulted in TTL refusing to enter into this Agreement.</p>
<p><strong>14. Non-Use and Non-Disclosure</strong><br>
You agree that all communications with us shall be considered private and confidential information and you shall not under any circumstance disclose, publish, broadcast, make known in any way shape or form, or use the content of such communications, regardless if the communication is made verbally or in written form, without our express written authorization.</p>
<p><strong>15. Compliance with Laws</strong><br>
You represent and warrant that your use of TTL shall comply with all applicable laws and regulations. You agree to indemnify and hold us harmless from any losses, including attorney fees that result from your breach of any part of your warranties and representations.</p>
<p><strong>16. Payment Dispute Rules</strong><br>
You warrant that no credit card payment, e-check or other payment made to TTL by you, or a third party for your benefit, shall be disputed, or a chargeback filed with the credit card issuer, until after you have completed sequentially the three step procedure below;</p>
<p style="padding-left: 30px;"><strong>First:</strong> Attempt first to settle the dispute by online mediation administered by the American Arbitration Association under its Commercial Mediation Procedures. This can be done here: <a title="https://apps.adr.org/webfile/" href="https://apps.adr.org/webfile/">https://apps.adr.org/webfile/</a><br>
<strong>Second:</strong> Submit a claim to be settled by binding arbitration administered by the American Arbitration Association in accordance with its Commercial Arbitration Rules and judgment on the award rendered by a single arbitrator may be entered in any court having jurisdiction thereof. This can be done here: <a title="https://www.adr.org/webfile/faces/home" href="https://www.adr.org/webfile/faces/home">https://www.adr.org/webfile/faces/home</a><br>
<strong>Third:</strong> File a claim against our Surety Bond.</p>
<p>Each time you wish to dispute a payment, these rules must be followed. Each appeal by you, or a third party of a disputed payment decision you or the third party lost counts as a separate disputed payment for purposes of this section 16 and section 21.</p>
<h3><strong>LIABILITY</strong></h3>
<p><strong>17. Limitation of Liability</strong><br>
To the maximum extent permitted by law, you assume full responsibility for any loss that results from your use of the Website and the Services, including any downloads from the Website. We and our Team won’t be liable for any indirect, punitive, special, or consequential damages under any circumstances, even if they’re based on negligence or we’ve been advised of the possibility of those damages. Our total liability for all claims made about the Service shall be limited to $100.00</p>
<p><strong>18. No Warranties</strong><br>
To the maximum extent permitted by law, we provide the material on the Website and the Service as is. That means we don’t provide warranties of any kind, either express or implied, including but not limited to warranties of merchantability and fitness for a particular purpose.</p>
<p><strong>19. Indemnity</strong><br>
You agree to indemnify and hold us and our Team harmless from any losses (including attorney fees) that result from any claims you make that isn’t allowed under these Terms due to a “Limitation of Liability” or other provision. You also agree to indemnify and hold us harmless from any losses (including attorney fees) that result from third-party claims that you or someone using your password did something that, if true, would violate any of these Terms.</p>
<p><strong>20. Attorney Fees</strong><br>
If we file an action against you claiming you breached these Terms and we prevail, we’re entitled to recover reasonable attorney fees and any damages or other relief we may be awarded.</p>
<p><strong>21. Liquidated Damages</strong><br>
In some instances, a breach of these Terms could cause damages, but proving the actual damages would be impossible. These instances shall result in the corresponding liquidated damages, which are a reasonable pre-estimate of the damages:</p>
<p style="padding-left: 30px;">1. Each time You violate the Payment Dispute Rules, the liquidated damages will be three times the amount of each of your disputed payment(s) to us, but not less than $1000.<br>
2. Each time You violate the Non-Use and Non- Disclosure terms, then the liquidated damages will be $5000, for each violation.<br>
3. Each time You violate the Non-Disparagement terms, the liquidated damages will be $25,000, for each violation.<br>
4. If you don’t pay an amount due within thirty (30) days after we send you a late payment notice, then the liquidated damages will be three times the total amount you were billed but failed to pay.<br>
5. If you attempt to pay your balance due, by an altered or fictitious payment instrument, the liquidated damages will be three times the amount of the balance due.</p>
<p><strong>22. Collections</strong><br>
If you fail or refuse to pay fees due to us when they are due, your account may be turned over for collection and possible litigation, and you hereby agree to pay all reasonable attorneys fees, court costs, filing fees, and collection costs which may be assessed by us, our attorneys, or any collection agency retained to pursue the matter, along with interest at the highest rate allowed by law. WE REPORT COLLECTION ACCOUNTS TO ALL 3 CREDIT BUREAUS.</p>
<p><strong>23. Subpoena Fees</strong><br>
If we have to provide information in response to a subpoena related to your account, then we may charge you for our costs. These fees may include attorney and employee time spent retrieving the records, preparing documents, and participating in a deposition.</p>
<p><strong>24. Disclaimers</strong><br>
We and our Team aren’t responsible for the behavior of any brokers, suppliers, or other Users.</p>
<p><strong>25. Force Majeure</strong><br>
We won’t be held liable for any delays or failure in performance of any part of the Service, from any cause beyond our control. This includes, but is not limited to, acts of God, changes to law or regulations, embargoes, war, terrorist acts, riots, fires, earthquakes, nuclear accidents, zombie apocalypse, floods, strikes, power blackouts, volcanic action, unusually severe weather conditions, acts of hackers or third-party service providers or suppliers.</p>
<p><strong>26. Equitable Relief</strong><br>
If you violate these Terms then we may seek injunctive relief (meaning we may request a court order to stop you) or other equitable relief in any state or federal court in the State of Florida, and you consent to exclusive jurisdiction and venue in such courts.</p>
<p><strong>27. Arbitration and Choice of Law</strong><br>
The State of Florida’s laws, except for conflict of laws rules, will apply to any dispute related to these Terms or the Service. Any dispute related to the Terms, the Privacy Policy, or the Service itself will be decided by as follows: You or We may choose to resolve disputes involving $5000 or less exclusively in Miami Florida small claims court. If a dispute involving more than $5000 arises under this agreement, you and we agree to submit the dispute to binding arbitration at the following location: Miami, FL., under the rules and auspices of the American Arbitration Association Expedited Commercial Rules for Arbitration, solely by WRITTEN SUBMISSION. Judgment upon the award rendered by the arbitration may be entered in any court with jurisdiction to do so. You and TTL expressly authorize the arbitrator to award reasonable attorneys fees and costs to the prevailing party as determined by the arbitrator.</p>
<p>ARBITRATION MUST BE ON AN INDIVIDUAL BASIS. THIS MEANS NEITHER YOU NOR WE MAY JOIN OR CONSOLIDATE CLAIMS IN ARBITRATION BY OR AGAINST OTHER , OR LITIGATE IN COURT OR ARBITRATE ANY CLAIMS AS A REPRESENTATIVE OR MEMBER OF A CLASS OR IN A PRIVATE ATTORNEY GENERAL CAPACITY.</p>
<p><strong>28. Assignments</strong><br>
You may not assign any of your rights under this agreement to anyone else. We may assign our rights to any other individual or entity at our discretion.</p>
<p><strong>29. Survivability</strong><br>
<span lang="EN-US"><span style="color: #000000; font-family: Calibri;">Any provision of this Agreement which imposes an obligation shall survive the termination or expiration of this Agreement.<br>
</span></span></p>
<p><strong>30. Severability</strong><br>
If it turns out that a section of this Agreement isn’t enforceable, then that section will be removed or edited as little as necessary, and the rest of the Terms will still be valid.</p>
<p><strong>31. Interpretation</strong><br>
The headers and sidebar text are provided only to make this agreement easier to read and understand. The fact that we wrote these Terms won’t affect the way this Agreement is interpreted.</p>
<p><strong>32. Amendments and Waiver</strong><br>
Amendments or changes to these Terms won’t be effective until we post revised Terms on the Website. That aside, additional terms may apply to certain features of the Service (the “Additional Terms”). The Additional Terms will be considered incorporated into these Terms when you activate the feature. Where there’s a conflict between these Terms and the Additional Terms, the Additional Terms will control. If we don’t immediately take action on a violation of these Terms, we’re not giving up any rights under the Terms, and we may still take action at some point.</p>
<p><strong>33. Further Actions</strong><br>
You will use best efforts to provide all documents and take any actions necessary to meet your obligations under these Terms.</p>
<p><strong>34. Contact Information and Notice</strong><br>
If you have any questions or concerns regarding our Terms of Use or other services of this website, you may contact us by email at Support@TopTradelines.com or by fax at 305-459-3909. Any notice to you will be effective when we send it to the last email or physical address you gave us or posted on our Website. Any notice to us will be effective when delivered to us along with a copy to our legal counsel: Attn. Legal Department, 99th Floor LLC, 1000 Ponce de Leon Blvd, Suite 103, Coral Gables, FL 33134 or any addresses as we may later post on the Website.</p>
<p><strong>35. AUTHORIZATION TO CONTACT YOU; RECORDING CALLS:</strong><br>
You agree to receive calls, including autodialed and/or pre-recorded message calls, from Us at any of the telephone numbers (including mobile telephone numbers) that we have collected for you as authorized and described in our User Privacy Notice, including telephone numbers you have provided us, or that we have obtained from third parties or collected by our own efforts. If the telephone number that we have collected is a mobile telephone number, you consent to receive SMS or other text messages at that number. Standard telephone minute and text charges may apply if we contact you at a mobile number or device. You agree we may contact you in the manner described above at the telephone numbers we have in our records for these purposes:</p>
<p style="padding-left: 30px;">•To contact you for reasons relating to your account or your use of our Services (such as to collect a debt, resolve a dispute, or to otherwise enforce our User Agreement) or as authorized by applicable law.<br>
•To contact you for marketing, promotional, or other reasons that you have either previously consented to or that you may be asked to consent to in the future. If you do not wish to receive such communications, you can opt-out by sending us a communication that states You are opting out.</p>
<p>We may share your telephone numbers with our service providers (such as billing or collections companies) who we have contracted with to assist us in pursuing our rights or performing our obligations under the User Agreement, our policies, or any other agreement we may have with you. These service providers may also contact you using autodialed or prerecorded messages calls and/or SMS or other text messages, only as authorized by us to carry out the purposes we have identified above, and not for their own purposes.</p>
<p>We will not share your telephone number with non-affiliated third parties for their purposes without your explicit consent, but may share your telephone numbers with members of our corporate family and/or our affiliates, for their use, as authorized. Members of the 99th Floor LLC corporate family and/or our affiliates will only contact you using autodialed or prerecorded message calls and/or SMS or other text messages, if you have requested their services.<br>
We may, without further notice or warning and in its discretion, monitor or record telephone conversations you or anyone acting on your behalf has with Us or our agents for quality control and training purposes or for its own protection. You acknowledge and understand that, while your communications with us may be overheard, monitored, or recorded without further notice or warning, not all telephone lines or calls may be recorded by Us, and we do not guarantee that recordings of any particular telephone calls will be retained or retrievable.</p>
<p><strong>36. Entire Agreement</strong><br>
These Terms, our Privacy Policy, and any Additional Terms you’ve agreed to are incorporated herein and make up the entire agreement and supersede all prior agreements, representations, and understandings.</p>
<p><strong>B. Tradeline Customer Terms.</strong><br>
These terms and the User terms above apply to all Tradeline customers who purchase tradelines.</p>
<p style="padding-left: 30px;"><strong>1. Credit Monitoring Account.</strong> You are required as part of the Tradeline purchase process to open up a Credit Monitoring Account to enable TopTradelines to pull Tri-Merge credit reports as necessary. You shall provide TopTradelines with your log-in and password for the Credit Monitoring account. You acknowledge your Credit Monitoring account with be charged $34.95 for each Tri-Merge credit report that TopTradelines pulls, and that each Tradeline purchased may need a separate Tri-Merge credit report to be pulled before it will show up on your Credit Record<br>
<strong>2. Tri-Merge Credit Report Pull Authorization</strong>. You expressly authorize TopTradelines to use your log-in and password to sign into your Credit Monitoring Account and pull as many Tri-Merge credit reports as necessary to enable your Tradelines to post to your credit report. If your Credit Monitoring Account refuses to process a TopTradelines tri-Merge credit report pull request due to a payment related problem, TopTradelines will promptly notify you. You will have 72 hours to correct the problem from the time we send you an email to notify you. If you do not correct the problem with 72 hours of being notified, you expressly authorize TopTradelines or its agents to perform a Hard Inquiry Tri-Merge credit report pull, and charge your account $35 for said pull. You acknowledge that a Hard Inquiry tri-Merge credit report pulls will show up on your credit record, and may negatively affect your credit score.<br>
<strong>3. Liquidated Damages</strong>. You warrant that you or anyone acting on your behalf shall not contest or dispute any Hard Inquiry Tri-Merge credit report pulls that appear on your credit record that were performed by TopTradelines or its agents in accordance with Section B 2 above. You understand and warrant that by doing so, you shall cause large and difficult to measure damages to us. You hereby agree to pay us $500 in Liquidated Damages for each Hard Credit Inquiry disputed, per Bureau.</p>
<p><strong>C. Supplier Terms</strong><br>
If you are a Supplier, the Users Terms and these Supplier Terms apply to you.</p>
<p><strong>1. Appointment of Supplier.</strong> Company appoints Supplier as a non-exclusive independent contractor to supply business products to the Company subject to terms, conditions, and covenants set forth in this agreement (the “Services”).<br>
Supplier accepts such appointment and agrees to comply with the terms and to perform all conditions in this agreement.</p>
<p><strong>2. Supplier Tradeline Requirements</strong>. Supplier shall:</p>
<p style="padding-left: 30px;">a. submit for inclusion in TopTradelines inventory, only those tradelines that have a Perfect Payment History (No Late Payments) and which Supplier warrants that it will maintain a Balance no higher than 10% of the Tradelines Account Credit Limit.<br>
b. supply the most recent credit card statement for each new tradeline submitted by Supplier to TopTradelines for approval of inclusion in TopTradelines inventory.<br>
c. supply TopTradelines with Monthly Credit Card Account Statements for all your Tradelines that are in TopTradelines inventory, no later than 7 Calendar Days after the Statement Date of each Credit Card Account Statement. You may obscure all but the last 4 digits of your Credit Card Account #. All other information must be visible and legible.<br>
d. respond to each Supplier Order we send you and providing Screenshot Proof that each Customer was successfully added to the Credit Card Account as an authorized user WITHIN 2 DAYS MAXIMUM of receiving the Order.</p>
<p><strong>3. Representations and Warranties.</strong> Supplier warrants that it shall:</p>
<p style="padding-left: 30px;">a. Fulfill each tradeline order placed by TopTradelines by the specified Add-By Date;<br>
b. Maintain a 10% or lower Balance on each Tradeline included in TopTradelines inventory;<br>
c. Maintain a Perfect Payment History on each Tradeline included in TopTradelines inventory;<br>
d. Not contact our Tradeline customers and/or Brokers without our express written authorization;<br>
e. Not Falsify Screenshot Proofs of Customers being added to your Credit Card Account;<br>
f. Not remove a Customers tradeline prior the specified Remove Date;<br>
g. Not Refuse to remove a Customer tradeline within a maximum of 7 Calendar Days after being instructed to do so; and<br>
h. Never share its private credit card account information with TopTradelines, TopTradelines customers, or third parties purporting to be acting on TopTradelines behalf.</p>
<p><strong>4. Liquidated Damages.</strong></p>
<p style="padding-left: 30px;">a. Supplier acknowledges that a breach of Sections C-2c or C-3 shall cause damages to TopTradelines, but proving the actual damages would be impossible. Supplier agrees to pay the following liquidated damages to TopTradelines, which Supplier agrees are a reasonable estimate of the damages that would result from Suppliers breach of Sections C-2c or C-3:<br>
b. Supplier agrees to pay Liquidated damages to TopTradelines in an amount of $250 for each violation of Section C-2c.<br>
c. Supplier agrees to pay Liquidated damages to TopTradelines in an amount equal to the commission Supplier would have earned in filling the Supplier order, if supplier had not breached section C-3 above. Further a breach of any combination of C-3b and/or C-3c, and C-3g shall result in an additional $2500 in liquidated damages to TopTradelines by Supplier.</p>
<p><strong>5. Suppliers Account.</strong></p>
<p style="padding-left: 30px;"><strong>a. Commissions</strong>. Supplier’s commissions are earned when we confirm that a tradeline that you sold posts to the customers 3 Credit Reports. We may advance commissions to established Suppliers who are in full compliance with this Section C. These advance payments are contingent upon the tradeline posting to all 3 of the customer’s credit reports. If the tradeline fails to post to the customers 3 credit reports for any or no reason, that advance must be repaid to TopTradelines, and your Supplier account will be debited accordingly. New tradelines added to the TopTradelines inventory and tradelines with Fail to Post Percentages greater than 10% are subject to having Supplier Commissions held/delayed until we can confirm the tradeline is posting correctly to the 3 Credit Bureaus and/or the Fail to Post Percentage goes down to 10% or less. This decision which shall be made at our sole discretion.</p>
<p style="padding-left: 30px;"><strong>b. Payments</strong>. Payments are made via Direct Deposit each week for your current Supplier Account Balance. Your Supplier Account Balance is calculated as the sum of all Commissions you earned minus the entire advance Payments made and any other debits you have accumulated, including but not limited liquidated damage fees or commissions advanced for tradelines that failed to post to the customers 3 credit reports. If you believe there is any inaccuracy whatsoever in your Supplier Compensation, you must submit a Transaction Dispute Support Ticket no later than 7 Calendar Days after the Status Date of the Transaction in question. If you do not submit such Dispute within this Time Frame, then the validity of the Transaction in question will be considered Final and you will no longer be able to dispute it for any reason whatsoever.</p>
<p style="padding-left: 30px;"><strong>c. Suppliers Account Suspension and Termination</strong>. In the event your Supplier Account is Suspended or Terminated, Orders in “Added to Card” or “Complete” Status may need to be changed to “Failed to Post” or “Cancelled by Customer”, which will reset the Commission on such Orders to $0 and lower your Supplier Account Balance, possibly creating a Negative Balance (meaning we paid you more money than you actually earned). In the event of a negative Supplier Account Balance, you will be given a maximum of 14 Calendar Days to pay us back the amount we overpaid you, and such payment shall be made via ACH, Wire Transfer, Bank Deposit or, if an exception is granted, via Credit Card. In the event you fail to pay us back the Negative Balance within 14 Calendar Days, your Supplier Account will be sent to Collections as explained in the “Collections” section of our Terms of Use.</p>
<p><strong>6. Independent Contractor Status</strong></p>
<p style="padding-left: 30px;">a. Status. Supplier is an independent contractor of Company. Nothing contained in this Agreement shall be construed to create the relationship of employer and employee, principal and agent, partnership or joint venture, or any other fiduciary relationship.<br>
b. No Authority. Supplier shall have no authority to act as agent for, or on behalf of, Company, or to represent Company, or bind Company in any manner. Broker shall not use Company’s name, logo, copyright, trademark, trade name or any near resemblance of such name or trademark which in the opinion of Company, would infringe upon or dilute the logos, names and/or trademarks or copyrights of Company, or which in the opinion of Company bears such near resemblance to any logos, names and/or trademarks or copyrights of Company might deceive customers or create confusion, or any other identifying mark on any stationery, documents, or advertising without prior written consent of Company.<br>
c. No Benefits. Supplier shall not be entitled to worker’s compensation, retirement, insurance or other benefits afforded to employees of Company.<br>
d. Taxes. Supplier is responsible for payment of all taxes on commissions earned as a TopTradelines Independent contractor.</p>
<p><strong>D. Brokers Terms</strong><br>
If you are a broker the Users terms and the Brokers terms apply to you.</p>
<p style="padding-left: 30px;">1. Appointment of Broker. Company appoints Broker as a non-exclusive independent contractor to market the business products for the Company subject to terms, conditions, and covenants set forth in this agreement (the “Services”).<br>
Broker accepts such appointment and agrees to comply with the terms and to perform all conditions in this agreement.<br>
2. Solicitation of Customers.<br>
a. Broker shall solicit potential business customers for the purchase of the products that the broker purchased from the Company.<br>
b. Broker shall require customer to sign up for Credit Monitoring before selling customer any tradeline products.<br>
c. Broker is not authorized to give any quotations; or make any bid, formal or informal; or to execute or initial any written contract or agreement or any written commitment; or make any oral representations or commitments which would be binding upon Company without the express written permission of Company, including but not limited to any expressed or implied product warranties.<br>
d. It is Brokers exclusive responsibility to deal with his customers. Broker may not copy his customer on any communications with Company.</p>
<p><strong>3. Independent Contractor Status</strong></p>
<p style="padding-left: 30px;">a. Status. Broker is an independent contractor of Company. Nothing contained in this Agreement shall be construed to create the relationship of employer and employee, principal and agent, partnership or joint venture, or any other fiduciary relationship.<br>
b. No Authority. Broker shall have no authority to act as agent for, or on behalf of, Company, or to represent Company, or bind Company in any manner. Broker shall not use Company’s name, logo, copyright, trademark, trade name or any near resemblance of such name or trademark which in the opinion of Company, would infringe upon or dilute the logos, names and/or trademarks or copyrights of Company, or which in the opinion of Company bears such near resemblance to any logos, names and/or trademarks or copyrights of Company might deceive customers or create confusion, or any other identifying mark on any stationery, documents, or advertising without prior written consent of Company.<br>
c. No Benefits. Broker shall not be entitled to worker’s compensation, retirement, insurance or other benefits afforded to employees of Company.<br>
d. Taxes. Broker is responsible for payment of all taxes on income earned as a TopTradelines Independent contractor.</p>
<p><strong>4. Representations and Warranties.</strong> Broker warrants that:</p>
<p style="padding-left: 30px;">a. Product Suitability. Broker fully understands that TopTradelines products shall only be sold to customers whose use is for business purposes, and not to customers use is for personal, family, or household purposes.<br>
b. Authority. Broker has the full right to allow it to provide the Company with the assignments and rights provided for herein.<br>
c. Performance. The Services shall be performed in a professional and workmanlike manner and that none of such Services or any part of this Agreement is or will be inconsistent with any obligation Broker may have to others. Broker shall refrain from engaging in conduct or activities that might be detrimental to or reflect adversely on the reputation of TopTradelines, Broker or TopTradelines products, and shall not engage in any discourteous, deceptive, misleading or unethical practices or activities.<br>
d. Compliance with the Law. Broker shall abide by all federal, state, and local laws, ordinances and regulations.</p>
<h3><strong>SEPARATE ACKNOWLEDGEMENT</strong> Page</h3>
<p>I have read and agree to this commercial service agreement containing arbitration, payment dispute rules, non-disparagement, and personal guarantee terms.</p>
<p><strong>Commercial Service Agreement</strong><br>
The User represents and warrants that it is eighteen (18) years old and able to enter into contracts; and is using TopTradelines services for solely business purposes and in full compliance with all applicable laws and regulations.<br>
Arbitration and Choice of Law</p>
<p>The State of Florida’s laws, except for conflict of laws rules, will apply to any dispute related to these Terms or the Service. Any dispute related to the Terms, the Privacy Policy, or the Service itself will be decided by as follows: You or We may choose to resolve disputes involving $5000 or less exclusively in Miami Florida small claims court. If a dispute involving more than $5000 arises under this agreement, you and we agree to submit the dispute to binding arbitration at the following location: Miami, FL., under the rules and auspices of the American Arbitration Association Expedited Commercial Rules for Arbitration. Judgment upon the award rendered by the arbitration may be entered in any court with jurisdiction to do so. You and TTL expressly authorize the arbitrator to award reasonable attorneys fees and costs to the prevailing party as determined by the arbitrator.</p>
<p>ARBITRATION MUST BE ON AN INDIVIDUAL BASIS. THIS MEANS NEITHER YOU NOR WE MAY JOIN OR CONSOLIDATE CLAIMS IN ARBITRATION BY OR AGAINST OTHER , OR LITIGATE IN COURT OR ARBITRATE ANY CLAIMS AS A REPRESENTATIVE OR MEMBER OF A CLASS OR IN A PRIVATE ATTORNEY GENERAL CAPACITY.</p>
<p><strong>Payment Dispute Rules</strong><br>
You warrant that no credit card payment, e-check or other payment made to TTL by you, or a third party for your benefit, shall be disputed, or a chargeback filed with the credit card issuer, until after you have completed sequentially the three step procedure below;</p>
<p style="padding-left: 30px;"><strong>First:</strong> Attempt first to settle the dispute by online mediation administered by the American Arbitration Association under its Commercial Mediation Procedures. This can be done here: <a title="https://apps.adr.org/webfile/" href="https://apps.adr.org/webfile/">https://apps.adr.org/webfile/</a><br>
<strong>Second:</strong> Submit a claim to be settled by binding arbitration administered by the American Arbitration Association in accordance with its Commercial Arbitration Rules and judgment on the award rendered by a single arbitrator may be entered in any court having jurisdiction thereof. This can be done here: <a title="https://www.adr.org/webfile/faces/home" href="https://www.adr.org/webfile/faces/home">https://www.adr.org/webfile/faces/home</a><br>
<strong>Third:</strong> File a claim against our Surety Bond.</p>
<p>Each time you wish to dispute a payment, these rules must be followed. Each appeal by you, or a third party of a disputed payment decision you or the third party lost counts as a separate disputed payment for purposes of this section 16 and section 21.</p>
<p><strong>Mutual Non-Disparagement</strong><br>
You agree and warrant and that you shall not disparage or comment negatively, directly or indirectly, about TTL, or its Team. We agree and warrant and that we shall not disparage or comment negatively, directly or indirectly about you; except we may reports debts owed by you to us to Credit Reporting Agencies.. Disparagement shall be defined as any comments or statements that impugn the character, honesty, integrity, morality, business acumen or abilities in connection with any aspect of our dealings with each other. You and TTL agree and acknowledge that this non-disparagement provision is a material term of this Agreement; the absence of which would have resulted in TTL refusing to enter into this Agreement. If You or We violate the Non-Disparagement terms, the liquidated damages will be $25,000, for each violation. You and We agree that $25,000 represents a reasonable estimate of the damages either party would suffer due to disparagement by the other.</p>
<p><strong>Personal Guarantee</strong><br>
You agree you are personally liable to TopTradelines for all debts incurred by you whether personally or on behalf of a third party , or incurred on your behalf. You represent that if you are using someone else’s credit card to make a purchase from TopTradelines, you are acting as that person or entities agent. As their agent you agree to accept these terms on their behalf and legally bind them to this agreement. If you are acting as the agent for a business entity, you agree as that business entities agent, that the principals of that business entity will be personally liable for all debts incurred by that business.</p>
<p><strong>Entire Agreement.</strong> These Terms, our Privacy Policy, Terms of Use and any Additional Terms you’ve agreed to are incorporated herein and make up the entire agreement and supersede all prior agreements, representations, and understandings.</p>
    </div>
</div >
