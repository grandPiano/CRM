<?php
/*******************************************************************************
 *
 *  filename    : Include/Header-functions.php
 *  website     : http://www.churchcrm.io
 *  description : page header used for most pages
 *
 *  Copyright 2001-2004 Phillip Hullquist, Deane Barker, Chris Gebhardt, Michael Wilt
 *  Update 2017 Philippe Logel
 *
 *
 ******************************************************************************/

require_once 'Functions.php';

use ChurchCRM\Service\SystemService;
use ChurchCRM\dto\SystemURLs;
use ChurchCRM\Service\NotificationService;
use ChurchCRM\dto\SystemConfig;
use ChurchCRM\MenuConfigQuery;

function Header_system_notifications()
{
    if (NotificationService::hasActiveNotifications()) {
        ?>
        <script>
            <?php foreach (NotificationService::getNotifications() as $notification) { ?>
            $.notify({
                icon: 'fa fa-bell',
                message: '<?= $notification->title?>',
                url: '<?= $notification->link ?>'
            },{
                delay: 10000,
                type: 'danger',
                placement: {
                    from: 'bottom',
                    align: 'left'
                }
            });
            <?php } ?>
        </script>
        <?php
    }
}

function Header_modals()
{
    ?>
    <!-- Issue Report Modal -->
    <div id="IssueReportModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <form name="issueReport">
                    <input type="hidden" name="pageName" value="<?= $_SERVER['SCRIPT_NAME'] ?>"/>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><?= gettext('Issue Report!') ?></h4>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-xl-3">
                                    <label
                                            for="issueTitle"><?= gettext('Enter a Title for your bug / feature report') ?>
                                        : </label>
                                </div>
                                <div class="col-xl-3">
                                    <input type="text" name="issueTitle">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-3">
                                    <label
                                            for="issueDescription"><?= gettext('What were you doing when you noticed the bug / feature opportunity?') ?></label>
                                </div>
                                <div class="col-xl-3">
                                    <textarea rows="10" cols="50" name="issueDescription"></textarea>
                                </div>
                            </div>
                        </div>
                        <ul>
                            <li><?= gettext('When you click "submit," an error report will be posted to the ChurchCRM GitHub Issue tracker.') ?></li>
                            <li><?= gettext('Please do not include any confidential information.') ?></li>
                            <li><?= gettext('Some general information about your system will be submitted along with the request such as Server version and browser headers.') ?></li>
                            <li><?= gettext('No personally identifiable information will be submitted unless you purposefully include it.') ?></li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="submitIssue"><?= gettext('Submit') ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Issue Report Modal -->

    <?php
}

function Header_body_scripts()
{
    global $localeInfo;
    $systemService = new SystemService(); ?>
    <script nonce="<?= SystemURLs::getCSPNonce() ?>">
        window.CRM = {
            root: "<?= SystemURLs::getRootPath() ?>",
            fullURL:"<?= SystemURLs::getURL() ?>",
            lang: "<?= $localeInfo->getLanguageCode() ?>",
            locale: "<?= $localeInfo->getLocale() ?>",
            shortLocale: "<?= $localeInfo->getShortLocale() ?>",
            maxUploadSize: "<?= $systemService->getMaxUploadFileSize(true) ?>",
            maxUploadSizeBytes: "<?= $systemService->getMaxUploadFileSize(false) ?>",
            datePickerformat:"<?= SystemConfig::getValue('sDatePickerPlaceHolder') ?>",
            iDasbhoardServiceIntervalTime:"<?= SystemConfig::getValue('iDasbhoardServiceIntervalTime') ?>",
            plugin: {
                dataTable : {
                   "language": {
                        "url": "<?= SystemURLs::getRootPath() ?>/locale/datatables/<?= $localeInfo->getDataTables() ?>.json"
                    },
                    responsive: true,
                    "dom": 'T<"clear">lfrtip',
                    "tableTools": {
                        "sSwfPath": "<?= SystemURLs::getRootPath() ?>/skin/adminlte/plugins/datatables/extensions/TableTools/swf/copy_csv_xls.swf"
                    }
                }
            },
            PageName:"<?= $_SERVER['PHP_SELF']?>"
        };
    </script>
    <script src="<?= SystemURLs::getRootPath() ?>/skin/js/CRMJSOM.js"></script>
    <?php
}
?>
