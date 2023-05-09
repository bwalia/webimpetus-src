<!-- footer div part -->
<div class="DOCKER_REGISTRYpart">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="DOCKER_REGISTRYiner text-center">
                    <?php

                    function auto_copyright($year = 'auto')
                    {
                        if($year == 'auto'){ $year = date('Y');}
                        if(intval($year) == date('Y')){ echo "<!--" . intval($year) . "-->";}
                        if(intval($year) < date('Y')){ echo intval($year) . ' - ' . date('Y');}
                        if(intval($year) > date('Y')){ echo "<!--" . date('Y') . "-->";}
                    }
 
                    $appReleaseNotesDocURL = getenv('APP_RELEASE_NOTES_DOC_URL') ?: "https://webimpetus.cloud/";
                    $appEnvironment = getenv('APP_ENVIRONMENT') ?: "dev";
                    $targetCluster = getenv('APP_TARGET_CLUSTER') ?: "k3s0";
                    $hostName = getenv('HOSTNAME') ?: "hostname-env-var-not-set";

                    $webImpetusCopyRight = "Cluster: " . $targetCluster . ".";
                    if ($appEnvironment == "prod" || $appEnvironment == "Prod") {
                        // in production hide environment details
                        $webImpetusCopyRight .= " Environment: " . ucfirst($appEnvironment) . ".";
                        //$webImpetusCopyRight .= " CodeIgniter Version: " . \CodeIgniter\CodeIgniter::CI_VERSION . ".";
                        $webImpetusCopyRight .= " Hostname: " . $hostName . ".";

                    } else {
                        $webImpetusCopyRight .= " Environment: " . ucfirst($appEnvironment) . ".";
                        $webImpetusCopyRight .= " CodeIgniter Version: " . \CodeIgniter\CodeIgniter::CI_VERSION . ".";
                        $webImpetusCopyRight .= " Hostname: " . $hostName . ".";
                    }
                    $webImpetusCopyRight .= " Deployment Time: " . getenv('APP_DEPLOYED_AT') . ".";
                    ?>
                    <p class="typography small"><?php auto_copyright("2009"); ?>&nbsp;&copy;&nbsp;Workstation</p>
                    <p class="typography small">© All rights reserved.<br /></p>
                    <p class="typography small"><br /></p>
                    <p class="typography small">Powered&nbsp;by&nbsp;<a href="https://webimpetus.cloud/"> <i class="ti-heart"></i>&nbsp;Webimpetus</a></p>
                    <p class="typography small">WebImpetus <a target="_blank" href="<?php echo $appReleaseNotesDocURL; ?>">v <?php echo getenv('APP_FULL_VERSION_NO') . " build " . getenv('APP_FULL_BUILD_NO'); ?></a></p>
                    <p class="typography small"><br /></p>
                    <p class="typography small"><?php echo $webImpetusCopyRight; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- footer div part -->