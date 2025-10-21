<!-- footer div part -->
<div class="footer_part" style="position: relative;">
    <!-- Font Size Controls - Bottom Right -->
    <div class="font-size-controls-footer">
        <style>
            .font-size-controls-footer {
                position: fixed;
                bottom: 20px;
                right: 20px;
                z-index: 1000;
                display: flex;
                gap: 8px;
                align-items: center;
                background: white;
                padding: 10px 15px;
                border-radius: 50px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            }
            .font-size-btn {
                background: white;
                border: 2px solid #667eea;
                border-radius: 50%;
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.2s ease;
                font-weight: 700;
                color: #667eea;
            }
            .font-size-btn:hover {
                background: #667eea;
                color: white;
                transform: scale(1.1);
            }
            .font-size-btn:active {
                transform: scale(0.95);
            }
            .font-size-display {
                font-size: 0.875rem;
                color: #6b7280;
                font-weight: 600;
                min-width: 45px;
                text-align: center;
            }
        </style>
        <button class="font-size-btn" id="decreaseFontSize" title="Decrease font size">
            <i class="fa fa-minus"></i>
        </button>
        <span class="font-size-display" id="fontSizeDisplay">100%</span>
        <button class="font-size-btn" id="increaseFontSize" title="Increase font size">
            <i class="fa fa-plus"></i>
        </button>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="footer_iner text-center">
                    <?php

                    function auto_copyright($year = 'auto')
                    {
                        if($year == 'auto'){ $year = date('Y');}
                        if(intval($year) == date('Y')){ echo "<!--" . intval($year) . "-->";}
                        if(intval($year) < date('Y')){ echo intval($year) . ' - ' . date('Y');}
                        if(intval($year) > date('Y')){ echo "<!--" . date('Y') . "-->";}
                    }
 
                    $appReleaseNotesDocURL = getenv('APP_RELEASE_NOTES_DOC_URL') ?: "https://webaimpetus.com/";
                    $appEnvironment = getenv('APP_ENVIRONMENT') ?: "dev";
                    $targetCluster = getenv('APP_TARGET_CLUSTER') ?: "k3s0";
                    $hostName = getenv('HOSTNAME') ?: "hostname-env-var-not-set";

                    $workerra-ciCopyRight = "Cluster: " . $targetCluster . ".";
                    if ($appEnvironment == "prod" || $appEnvironment == "Prod") {
                        // in production hide environment details
                        $workerra-ciCopyRight .= " Environment: " . ucfirst($appEnvironment) . ".";
                        //$workerra-ciCopyRight .= " CodeIgniter Version: " . \CodeIgniter\CodeIgniter::CI_VERSION . ".";
                        $workerra-ciCopyRight .= " Hostname: " . $hostName . ".";

                    } else {
                        $workerra-ciCopyRight .= " Environment: " . ucfirst($appEnvironment) . ".";
                        $workerra-ciCopyRight .= " CodeIgniter Version: " . \CodeIgniter\CodeIgniter::CI_VERSION . ".";
                        $workerra-ciCopyRight .= " Hostname: " . $hostName . ".";
                    }
                    $workerra-ciCopyRight .= " Deployment Time: " . getenv('APP_DEPLOYED_AT') . ".";
                    ?>
                    <p class="typography small"><?php auto_copyright("2009"); ?>&nbsp;&copy;&nbsp;Workstation</p>
                    <p class="typography small">© All rights reserved.<br /></p>
                    <p class="typography small"><br /></p>
                    <p class="typography small">Powered&nbsp;by&nbsp;<a href="https://webaimpetus.com/"> <i class="ti-heart"></i>&nbsp;workerra-ci</a></p>
                    <p class="typography small">workerra-ci <a target="_blank" href="<?php echo $appReleaseNotesDocURL; ?>">v <?php echo getenv('APP_FULL_VERSION_NO') . " build " . getenv('APP_FULL_BUILD_NO'); ?></a></p>
                    <p class="typography small"><br /></p>
                    <p class="typography small"><?php echo $workerra-ciCopyRight; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- footer div part -->