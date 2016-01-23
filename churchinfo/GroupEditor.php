<?php
/*******************************************************************************
 *
 *  filename    : GroupEditor.php
 *  last change : 2003-04-15
 *  website     : http://www.churchcrm.io
 *  copyright   : Copyright 2001, 2002, 2003 Deane Barker, Chris Gebhardt
 *                Copyright 2004-2012 Michael Wilt
 *
 *  ChurchCRM is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 ******************************************************************************/

//Include the function library
require "Include/Config.php";
require "Include/Functions.php";
require "service/GroupService.php";

// Security: User must have Manage Groups permission
if (!$_SESSION['bManageGroups'])
{
    Redirect("Menu.php");
    exit;
}

//Set the page title
$sPageTitle = gettext("Group Editor");
$groupService = new GroupService();
//Get the GroupID from the querystring.  Redirect to Menu if no groupID is present, since this is an edit-only form.
if (array_key_exists ("GroupID", $_GET))
    $iGroupID = FilterInput($_GET["GroupID"],'int');
else
{
    Redirect("GroupList.php");
}

$thisGroup = $groupService->getGroups($iGroupID);   //get this group from the group service.
$rsGroupTypes = $groupService->getGroupTypes();     // Get Group Types for the drop-down
$rsGroupRoleSeed = $groupService->getGroupRoleTemplateGroups();     //Group Group Role List 
require "Include/Header.php";

?>
<link rel="stylesheet" type="text/css" href="<?= $sURLPath; ?>/vendor/almasaeed2010/adminlte/plugins/datatables/dataTables.bootstrap.css">
<script src="<?= $sURLPath; ?>/vendor/almasaeed2010/adminlte/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= $sURLPath; ?>/vendor/almasaeed2010/adminlte/plugins/datatables/dataTables.bootstrap.js"></script>

<link rel="stylesheet" type="text/css" href="<?= $sURLPath; ?>/vendor/almasaeed2010/adminlte/plugins/datatables/extensions/TableTools/css/dataTables.tableTools.css">
<script type="text/javascript" language="javascript" src="<?= $sURLPath; ?>/vendor/almasaeed2010/adminlte/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js"></script>

<!-- GROUP SPECIFIC PROPERTIES MODAL-->
     <div class="modal fade" id="groupSpecificPropertiesModal" tabindex="-1" role="dialog" aria-labelledby="deleteGroup" aria-hidden="true">
            <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="gsproperties-label"></h4>
                        </div>
                        <div class="modal-body">
                        <span style="color: red">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button name="setgroupSpecificProperties" id="setgroupSpecificProperties" type="button" class="btn btn-danger">asdf</button>
                        </div>
                    </div>
            </div>
        </div>
<!-- END GROUP SPECIFIC PROPERTIES MODAL-->

<div class="box">
    <div class="box-header">
        <h3 class="box-title">Group Settings</h3>
    </div>
    <div class="box-body">
        <form name="groupEditForm" id="groupEditForm">
            <div class="form-group">
                <div class="row">
                <div class="col-xs-4">
                    <label for="Name"><?php echo gettext("Name:"); ?></label>
                    <input class="form-control" type="text" Name="Name" value="<?php echo htmlentities(stripslashes($thisGroup['groupName']),ENT_NOQUOTES, "UTF-8"); ?>">
                </div>
                </div>
                <div class="row">
                <div class="col-xs-4">
                    <label for="Description"><?php echo gettext("Description:"); ?></label>
                    <textarea  class="form-control" name="Description" cols="40" rows="5"><?php echo htmlentities(stripslashes($thisGroup['groupDescription']),ENT_NOQUOTES, "UTF-8"); ?></textarea></td>
                </div>
                </div>
                <div class="row">
                <div class="col-xs-3">
                        <label for="GroupType"><?php echo gettext("Type of Group:"); ?></label>
                        <select class="form-control input-small" name="GroupType">
                            <option value="0"><?php echo gettext("Unassigned"); ?></option>
                            <option value="0">-----------------------</option>
                            <?php
                            foreach ($rsGroupTypes as $groupType)
                            {
                                echo "<option value=\"" . $groupType['lst_OptionID'] . "\"";
                                if ($thisGroup['grp_Type'] == $groupType['lst_OptionID'])
                                    echo " selected";
                                echo ">" . $groupType['lst_OptionName']."</option>";
                            }
                            ?>
                        </select>
                </div>
                </div>
                <div class="row">
                <div class="col-xs-3">
                    <?php 
                    // Show Role Clone fields only when adding new group
                    if (strlen($iGroupID) < 1) { ?>
                        <b><?php echo gettext("Group Member Roles:"); ?></b>
                        
                        <?php echo gettext("Clone roles:"); ?>
                        <input type="checkbox" name="cloneGroupRole" id="cloneGroupRole" value="1">
                        </div>
                        <div class="col-xs-3" id="selectGroupIDDiv">
                        <?php echo gettext("from group:"); ?>
                        <select class="form-control input-small" name="seedGroupID" id="seedGroupID" >
                        <option value="0"><?php gettext("Select a group"); ?></option>
                        
                        <?php
                           foreach($rsGroupRoleSeed as $groupRoleTemplate)
                            {
                                echo "<option value=\"" . $groupRoleTemplate['grp_ID'] . "\">" . $groupRoleTemplate['grp_Name'] . "</option>";
                            }
                            echo "</select>";
                        ?>

                <?php } ?>
                </div>
                </div>
                <br>
                <div class="row">
                <div class="col-xs-6">
                    <label for="UseGroupProps"><?php echo gettext("Group Specific Properties: "); ?></label>
               
                    <?php
                        if ($thisGroup['grp_hasSpecialProps'])
                        {
                            echo "Enabled" ;
                            echo '<div class="btn-group"><button type="button" id="disableGroupProps" class="btn btn-danger">Disable Group Specific Properties</button>&nbsp;';
                            echo '<button type="button" class="btn btn-success groupSpecificProperties">Edit Group Specific Properties</button></div>';// href=\"GroupPropsFormEditor.php?GroupID=$iGroupID\">" . gettext("Edit Group-Specific Properties Form") . "</a>";
                        }
                        else
                            echo "Disabled <br>" ;
                            echo '<button type="button" id="enableGroupProps" class="btn btn-danger groupSpecificProperties">Enable Group Specific Properties</button>&nbsp;'; 
                    ?>
                </div>
                </div>
                <br>
                <div class="row">    
                <div class="col-xs-3">
                    <input type="submit" id="saveGroup" class="btn btn-primary" <?php echo 'value="' . gettext("Save") . '"'; ?> Name="GroupSubmit">
                </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo gettext("Group Roles:"); ?></h3>
    </div>
    <div class="box-body">
        <div class="alert alert-info alert-dismissable">
            <i class="fa fa-info"></i>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <strong></strong>Group name changes are saved as soon as the box loses focus
        </div>

        <?php
        if (strlen($iGroupID) > 0)
        {
        ?>
            <table class="table" id="groupRoleTable">
            </table>
            <label for="newRole">New Role: </label><input type="text" class="form-control" id="newRole" name="newRole">
            <br>
            <button type="button" id="addNewRole" class="btn btn-primary">Add New Role</button>

        <?php
        }
        else
        {
            ?><b class="MediumLargeText"><?php echo gettext("Initial Group Creation:  Group roles can be edited after the first save."); ?></b><br><br><?php
        }
        ?>
    </div>
</div>
<script>
    //setup some document-global variables for later on in the javascript
    var defaultRoleID= <?php echo ($thisGroup['grp_DefaultRole']?  $thisGroup['grp_DefaultRole'] : 1) ?>;
    var dataT = 0;
    var groupRoleData = <?php echo json_encode($groupService->getGroupRoles($iGroupID)); ?>;
    var roleCount = groupRoleData.length; 
    var groupID=<?php echo $iGroupID?>;
</script>
<script src="<?= $sURLPath; ?>/js/GroupEditor.js"></script>
<?php
    require "Include/Footer.php";
?>