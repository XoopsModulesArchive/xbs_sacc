<?php declare(strict_types=1);

/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * Organisation Admin page
 *
 * Allow administrator to create or modify Organisations data
 *
 * @copyright (c) 2004, Ashley Kitson
 * @copyright     XOOPS Project https://xoops.org/
 * @license       GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author        Ashley Kitson http://akitson.bbcb.co.uk
 * @author        XOOPS Development Team
 * @package       SACC
 * @subpackage    Admin
 * @access        private
 * @version       1
 */

/**
 * Do all the declarations etc needed by an admin page
 */
require_once __DIR__ . '/admin_header.php';
//require_once __DIR__ . '/adminheader.php';

xoops_cp_header();

$modDir = $xoopsModule->getVar('dirname');
xoops_loadLanguage('modinfo', $modDir);

//Display the admin menu
//xoops_module_admin_menu(1,_AM_XBSSACC_ADMENU1);

/**
 * To use this as a template you need to write code to display
 * whatever it is you want displaying between here...
 */
global $_POST;
extract($_POST);
if (isset($submit)) { //edit the organisation's record
    adminEditOrg($org_id);
} elseif (isset($insert)) { //create a new organisation
    adminEditOrg();
} elseif (isset($save)) { //user has edited or created organisation so save it
    adminEditOrg($org_id, true);
} elseif (isset($cancel)) {
    redirect_header(SACC_URL . '/admin/adminorg.php', 1, _AM_XBSSACC_ORGED101);
} else { //Present a list of organisations to select to work with
    adminSelectOrg();
} //end if

/**
 * and here.
 */

//And put footer in
xoops_cp_footer();
