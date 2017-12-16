<?php
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author:     Ashley Kitson                                                 //
// Copyright:  (c) 2004, Ashley Kitson                                       //
// URL:        http://xoobs.net                                     //
// Project:    The XOOPS Project (http://www.xoops.org/)                     //
// Module:     Simple Accounts System (SACC)                                 //
// ------------------------------------------------------------------------- //

/**
* Input or edit a journal entry 
*
* Allow input of a new journal entry or edit an existing one
*
* @author Ashley Kitson http://xoobs.net
* @copyright 2005 Ashley Kitson, UK
* @package SACC
* @subpackage User_interface
* @access private
* @version 1 
*/

/**
* MUST include module header
*/
require("header.php");
/**
* Xoops header file
*/
include XOOPS_ROOT_PATH."/header.php";
/**
* SACC form objects and elements
*/
require_once SACC_PATH."/class/class.sacc.form.php";
/**
* CDM form objects and elements
*/
require_once CDM_PATH."/class/class.cdm.form.php";
/**
* CDM API functions
*/
require_once CDM_PATH."/include/functions.php";

//Check to see if user logged in
Global $xoopsUser;
if (empty($xoopsUser)) redirect_header(SACC_URL."/sacc_list_accounts.php",1,_MD_SACC_ERR_5);

/**
* Function: Display the  journal entry form	
*
* @version 1
*/
function dispForm() {
  global $xoopsOption;
  global $xoopsTpl;
  global $HTTP_GET_VARS;

  $xoopsOption['template_main'] = 'sacc_journal_entry.tpl';
    
  if (!empty($HTTP_POST_VARS['org_id'])) {
    //Save the organisation id for use by other screens
    $org_id = $HTTP_POST_VARS['org_id'];
    $_SESSION['sacc_org_id'] = $org_id;
  } else {
    //see if org_id is in session variable
    $org_id = (empty($_SESSION['sacc_org_id'])?null:$_SESSION['sacc_org_id']);
  }
  /* The real work of setting up the form follows here */
  if (!empty($org_id)) {
    //set up organisation
    $orgHandler =& xoops_getmodulehandler("SACCOrg",SACC_DIR);
    $org = $orgHandler->get($org_id);
    //Create form fields
    //Date defaulted to today
    $dt = new XoopsFormText('Transaction Date','jrn_dt',10,10,date('d/m/Y'));
    $prps = new XoopsFormText('Purpose','jrn_prps',20,20);
    $ac_dr_id = new SACCFormSelectAccount('Debit Account',"ac_dr_id",$org_id,0);
    $ac_cr_id = new SACCFormSelectAccount('Credit Account',"ac_cr_id",$org_id,0);
    $dr_ref = new XoopsFormText('Debit Reference','dr_ref',20,20);
    $cr_ref = new XoopsFormText('Credit Reference','cr_ref',20,20);
    $amount = new XoopsFormText('Amount','amount',11,11);
    $submit = new XoopsFormButton("","submit",_MD_SACC_SUBMIT,"submit");
    $cancel = new XoopsFormButton("","cancel",_MD_SACC_CANCEL,"submit");
    $reset = new XoopsFormButton("","reset",_MD_SACC_RESET,"reset");
    $journalForm = new XoopsThemeForm(sprintf('Journal Entry for %s',$org->getVar('org_name')),"journalform","sacc_journal.php");
    $journalForm->addElement($dt);
    $journalForm->addElement($prps);
    $journalForm->addElement($ac_dr_id);
    $journalForm->addElement($dr_ref);
    $journalForm->addElement($ac_cr_id);
    $journalForm->addElement($cr_ref);
    $journalForm->addElement($amount);
    $journalForm->addElement($submit);
    $journalForm->addElement($cancel);
    $journalForm->addElement($reset);
    $journalForm->assign($xoopsTpl);
  }
} //end function dispForm

/**
* Function: Submit the form data to database
*
* @version 1
*/
function submitForm() {
  global $HTTP_POST_VARS;
  extract($HTTP_POST_VARS);
  $jHandler =& xoops_getmodulehandler("SACCJournal",SACC_DIR);
  //format the date for insertion into database
  $dte = substr($jrn_dt,6,4)."-".substr($jrn_dt,3,2)."-".substr($jrn_dt,0,2);
  $jData = $jHandler->create($dte,$jrn_prps,$_SESSION['sacc_org_id']);
  $amount *= pow(10,SACC_CFG_DECPNT); //convert float to integer
  $jData->appendEntry($ac_dr_id,$dr_ref,$amount,0);
  $jData->appendEntry($ac_cr_id,$cr_ref,0,$amount);
  if ($jHandler->insert($jData)) {
    redirect_header(SACC_URL."/sacc_list_journal.php",10,_MD_SACC_JRNED2);
  } else {
    redirect_header(SACC_URL."/sacc_list_journal.php",10,$jHandler->getError());
  }
}//end function submitForm

/* Main Program Block */
//if submit and cancel buttons not pressed then display a form
if (empty($HTTP_POST_VARS['submit'])) { 
  if (empty($HTTP_POST_VARS['cancel'])) {//present new form for input
      dispForm();
      /**
      * Display page
      */
      include XOOPS_ROOT_PATH."/footer.php";
    } else {
      redirect_header(SACC_URL."/sacc_list_journal.php",1,_MD_SACC_JRNED1);
    }//end if empty cancel

} else { //User has submitted form
  submitForm();
}//end if

?>