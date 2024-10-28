<?php 
    header('Content-Type: text/html');
    require_once(APPPATH . 'Views/common/edit-title.php');
    $blocks_list = getResultArray("blocks_list", ["uuid_linked_table" => @$service->uuid]);
    $domains = getResultArray("domains", ["sid" => @$service->uuid]);
    $templates = getResultArray("templates", ['module_name' => 'services']);
    $uri = service('uri');
    $uriSegment = $uri->getSegment(3);
?>
<style>
    .hidden {
        display: none;
    }
</style>
<div class="white_card_body">
    <div class="card-body">
        <form id="addservice" method="post" action="/services/update" enctype="multipart/form-data">
            <div class="row">
                <div class="col-xs-12 col-md-12">
                    <nav>
                        <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Service Detail</a>
                            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Service Secrets</a>
                            <a class="nav-item nav-link" id="nav-steps-tab" data-toggle="tab" href="#nav-steps" role="tab" aria-controls="nav-steps" aria-selected="false">Service Workflows</a>
                            <a class="nav-item nav-link" id="nav-domains-tab" data-toggle="tab" href="#nav-domains" role="tab" aria-controls="nav-domains" aria-selected="false">Domains</a>
                        </div>
                    </nav>
                    <div class="tab-content py-3 px-3 px-sm-0 col-md-12" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                            <div class="form-row">
                                <div class="form-group required col-md-6">
                                    <label for="inputEmail4">Name</label>
                                    <input autocomplete="off" autocomplete="off" type="text" class="form-control required" id="name" name="name" placeholder="" value="<?= @$service->name ?>">

                                    <input type="hidden" class="form-control" name="id" placeholder="" value="<?= @$service->uuid ?>" />
                                </div>
                                <div class="form-group required col-md-6">
                                    <label for="inputState">Choose User</label>
                                    <select id="uuid" name="uuid" class="form-control required">
                                        <option value="" selected="">--Select--</option>
                                        <?php foreach ($users as $row) : ?>
                                            <option value="<?= $row['uuid']; ?>" <?= ($row['uuid'] == @$service->user_uuid) ? 'selected' : '' ?>>
                                                <?= $row['name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>


                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputState">Choose Category</label>
                                    <select id="cid" name="cid" class="form-control">
                                        <option value="" selected="">--Select--</option>
                                        <?php foreach ($category as $row) : ?>
                                            <option value="<?= $row['id']; ?>" <?= ($row['id'] == @$service->cid) ? 'selected' : '' ?>>
                                                <?= $row['name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="inputState">Choose Tenant</label>
                                    <select id="tid" name="tid" class="form-control">
                                        <option value="" selected="">--Select--</option>
                                        <?php foreach ($tenants as $row) : ?>
                                            <option value="<?= $row['id']; ?>" <?= ($row['id'] == @$service->tid) ? 'selected' : '' ?>>
                                                <?= $row['name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>


                            <div class="form-row">
                                <div class="form-group required col-md-6">
                                    <label for="inputPassword4">Code</label>
                                    <input autocomplete="off" autocomplete="off" type="text" class="form-control required" id="code" name="code" placeholder="" value="<?= @$service->code ?>">
                                </div>
                                <div class="form-group required col-md-6">
                                    <label for="inputPassword4">Description</label>
                                    <textarea class="form-control required" id="notes" name="notes" rows="16" placeholder=""><?= @$service->notes ?></textarea>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputPassword4">Clone URL</label>
                                    <input type="text" class="form-control" id="clone-url" name="clone-url" placeholder="" value="<?= @$service->link ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="inputEmail4">Environment Tags</label>

                                    <select id="my-select2" data-select2-tags="true" name="env_tags[]" multiple="multiple" class="form-control select2">
                                        <?php
                                        if (!empty($service->env_tags)) {
                                            $arr = explode(',', $service->env_tags);
                                            foreach ($arr as $row) : ?>
                                                <option value="<?= $row; ?>" selected="selected">
                                                    <?= $row; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6 required">
                                    <label for="service_type">Service Type</label>
                                    <select class="custom-select required" id="service_type" name="service_type">
                                        <option selected>Please Select the Service Type</option>
                                        <option value="workflows" <?=@$service->service_type == 'workflows' ? 'selected' : ''?> >Run Workflows</option>
                                        <option value="marketing" <?=@$service->service_type == 'marketing' ? 'selected' : ''?>>Marketing</option>
                                    </select>
                                </div>
                            </div>
                            <?php $secret_values_templates['secret_template_id'] = isset($secret_values_templates['secret_template_id']) ? @$secret_values_templates['secret_template_id'] : []; ?>
                            <div class="service-template-wrapper <?php echo @$service->service_type == 'workflows' ? 'show' : 'hidden'; ?>" id="service-template-wrapper">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputPassword4">Secret Template</label>
                                        <select id="secret_template" name="secret_template[]" class="form-control select2 multiple" multiple>
                                            <!-- <option value="" selected="">--Select--</option> -->
                                            <?php foreach ($templates as $template) : ?>
                                                <option value="<?= $template['uuid']; ?>" <?= (in_array($template['uuid'], @$secret_values_templates['secret_template_id'])) ? 'selected' : '' ?>>
                                                    <?= $template['code']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputPassword4">Values Template</label>
                                        <select id="values_template" name="values_template" class="form-control">
                                            <option value="" selected="">--Select--</option>
                                            <?php foreach ($templates as $template) : ?>
                                                <option value="<?= $template['uuid']; ?>" <?= ($template['uuid'] == @$secret_values_templates['values_template_id']) ? 'selected' : '' ?>>
                                                    <?= $template['code']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6" id="secretKeysName"></div>
                                    <div class="form-group col-md-6" id="valuesKeysName"></div>
                                </div>
                            </div>
                            <div class="marketing-wrapper <?php echo @$service->service_type == 'marketing' ? 'show' : 'hidden'; ?>" id="marketing-wrapper">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputPassword4">Email Template</label>
                                        <select id="email_marketing_template" name="email_marketing_template" class="form-control">
                                            <option value="" selected="">--Select--</option>
                                            <?php foreach ($templates as $template) : ?>
                                                <option value="<?= $template['uuid']; ?>" <?= ($template['uuid'] == @$secret_values_templates['marketing_template_id']) ? 'selected' : '' ?>>
                                                    <?= $template['code']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputAddress">Logo Upload</label>

                                    <span class="all-media-image-files">
                                        <?php if (!empty(@$service->image_logo)) { ?>
                                            <img class="img-rounded" src="<?= @$service->image_logo; ?>" width="100px">
                                            <a href="/services/rmimg/image_logo/<?= @$service->uuid ?>" onclick="return confirm('Are you sure?')" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                                        <?php } ?>
                                    </span>
                                    <div class="uplogInrDiv" id="drop_file_doc_zone">
                                        <input type="file" name="file" class="form-control fileUpload  form-control-lg" id="file">
                                        <div class="uploadBlkInr">
                                            <div class="uplogImg">
                                                <img src="/assets/img/fileupload.png" />
                                            </div>
                                            <div class="uploadFileCnt">
                                                <p>
                                                    <a href="#">Upload a file </a> file chosen or drag
                                                    and drop
                                                </p>
                                                <p>
                                                    <span>Video, PNG, JPG, GIF up to 10MB</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                                <div class="form-group col-md-6">
                                    <label for="inputAddress">Brand Upload</label>
                                    <span class="all-media-image-files2 media-files">
                                        <?php if (!empty(@$service->image_brand)) { ?>
                                            <img src="<?= @$service->image_brand; ?>" width="100px">
                                            <a href="/services/rmimg/image_brand/<?= @$service->uuid ?>" onclick="return confirm('Are you sure?')" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                                        <?php } ?>
                                    </span>
                                    <div class="uplogInrDiv " id="drop_file_doc_zone2">

                                        <input type="file" name="file2" class="fileUpload2" id="file2">
                                        <div class="uploadBlkInr">
                                            <div class="uplogImg">
                                                <img src="/assets/img/fileupload.png" />
                                            </div>
                                            <div class="uploadFileCnt">
                                                <p>
                                                    <a href="#">Upload a file </a> file chosen or drag
                                                    and drop
                                                </p>
                                                <p>
                                                    <span>Video, PNG, JPG, GIF up to 10MB</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                            <div class="form-row">
                                <?php /*
                     for($jak_i=0; $jak_i<count($defaultSecret); $jak_i++){
                         $new_id = $jak_i + 1;
                         
                         if(empty($default_secrets_services[$jak_i]['secrets_default_value']))
                             $default_secrets_services[$jak_i]['secrets_default_value'] = '';
                 ?>
                 <div class="form-row col-md-12">
                     <div class="form-group col-md-6">
                         <label for="inputEmail4">Secret Key</label>
                         <input autocomplete="off" autocomplete="off" type="text" class="form-control" id="default_key_name_<?php echo $new_id; ?>" name="default_key_name[]" readonly placeholder="" value="<?=$defaultSecret[$jak_i]['secrets_default_key'] ?>">
                     </div>
                     <div class="form-group col-md-6">
                         <label for="inputEmail4">Secret Value</label>
                         <input autocomplete="off" type="text" class="form-control" id="default_key_value_<?php echo $new_id; ?>" name="default_key_value[]" placeholder="" value="<?=$default_secrets_services[$jak_i]['secrets_default_value'] ?>">
                     </div>
                 </div>
                 <?php
                     }
                 */ ?>
                            </div>

                            <?php
                            if (count($secret_services) > 0) {
                            ?>
                                <div class="form-row addresscontainer" id="addresscontainer">
                                    <?php
                                    for ($jak_i = 0; $jak_i < count($secret_services); $jak_i++) {
                                        $new_id = $jak_i + 1;
                                    ?>
                                        <div class="form-row col-md-12 secret-row-container" id="office_address_<?php echo $new_id; ?>">
                                            <div class="form-group col-md-3">
                                                <label for="inputEmail4">Secret Key</label>
                                                <input autocomplete="off" type="text" class="form-control" id="key_name_<?php echo $new_id; ?>" name="key_name[]" placeholder="" value="<?= $secret_services[$jak_i]['key_name'] ?>">
                                            </div>
                                            <div class="form-group col-md-5">
                                                <label for="inputEmail4">Secret Value</label>
                                                <input autocomplete="off" type="text" class="form-control" id="key_value_<?php echo $new_id; ?>" name="key_value[]" placeholder="" value="<?= (!empty($_SESSION['role']) && $_SESSION['role'] == 1) ? $secret_services[$jak_i]['key_value'] : '********' ?>">
                                            </div>
                                            <div class="form-group col-md-2 d-flex flex-column">
                                                <label for="my-select2_<?php echo $new_id; ?>">Environment</label>
                                                
                                                <select id="my-select2_<?php echo $new_id; ?>" data-select2-tags="true" name="secret_tags[]" class="form-control select2">
                                                    <option value="" >--Select--</option>
                                                    <?php
                                                    if (isset($secret_services[$jak_i]['secret_tags'])) {
                                                        ?>
                                                            <option value="<?= $secret_services[$jak_i]['secret_tags']; ?>" selected="selected">
                                                                <?= $secret_services[$jak_i]['secret_tags']; ?>
                                                            </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <input type="hidden" class="form-control" name="secret_uuid[]" placeholder="" value="<?= @$secret_services[$jak_i]['uuid'] ?>" />
                                            <?php
                                            if ($jak_i == 0) {
                                            ?>
                                                <div class="form-group col-md-1 change d-flex">
                                                    <button class="btn btn-primary bootstrap-touchspin-up add " type="button" style="max-height: 35px;margin-top: 28px;margin-left: 10px;">+</button>
                                                    <button class="btn btn-info bootstrap-touchspin-up deleteaddress" data-type="secret_services" data-id="<?= $secret_services[$jak_i]['id'] ?>" id="deleteRow" type="button" style="max-height: 35px;margin-top: 28px;margin-left: 10px;">-</button>
                                                </div>
                                            <?php
                                            } else {
                                            ?>
                                                <div class="form-group col-md-1 change">
                                                    <button class="btn btn-info bootstrap-touchspin-up deleteaddress" data-type="secret_services" data-id="<?= $secret_services[$jak_i]['id'] ?>" id="deleteRow" type="button" style="max-height: 35px;margin-top: 28px;margin-left: 10px;">-</button>
                                                </div>
                                            <?php
                                            }
                                            ?>
                                            <div class="form-group col-md-1 change">
                                                <button 
                                                    class="btn btn-info bootstrap-touchspin-up clone-row" 
                                                    data-type="secret_services" 
                                                    data-uuid="<?= $secret_services[$jak_i]['uuid'] ?>" 
                                                    data-id="office_address_<?php echo $new_id; ?>"
                                                    data-key="<?php echo $new_id; ?>"
                                                    id="cloneRow" 
                                                    type="button" 
                                                    style="max-height: 35px;margin-top: 28px;margin-left: 10px;"
                                                >
                                                    <i class="fas fa-clone"></i>
                                                </button>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </div>

                                <input type="hidden" value="<?php echo count($secret_services); ?>" id="total_secret_services" name="total_secret_services">

                            <?php
                            } else {
                            ?>
                                <div class="form-row" id="office_address_1">
                                    <div class="form-group col-md-3">
                                        <label for="inputEmail4">Secret Key</label>
                                        <input autocomplete="off" type="text" class="form-control" id="key_name_1" name="key_name[]" placeholder="" value="">
                                    </div>
                                    <div class="form-group col-md-5">
                                        <label for="inputEmail4">Secret Value</label>
                                        <input autocomplete="off" type="text" class="form-control" id="key_value_1" name="key_value[]" placeholder="" value="">
                                    </div>
                                    <div class="form-group col-md-2 d-flex flex-column">
                                        <label for="my-select2_0">Environment</label>
                                        
                                        <select id="my-select2_0" data-select2-tags="true" name="secret_tags[]" class="form-control select2">
                                            <option value="" >--Select--</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-1 change">
                                        <button class="btn btn-primary bootstrap-touchspin-up add" type="button" style="max-height: 35px;margin-top: 28px;margin-left: 10px;">+</button>
                                    </div>
                                    <div class="form-group col-md-1 change">
                                        <button 
                                            class="btn btn-info bootstrap-touchspin-up clone-row" 
                                            data-type="secret_services" 
                                            data-uuid="" 
                                            data-id="office_address_1"
                                            data-key="1"
                                            id="cloneRow" 
                                            type="button" 
                                            style="max-height: 35px;margin-top: 28px;margin-left: 10px;"
                                        >
                                            <i class="fas fa-clone"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="form-row addresscontainer">

                                </div>
                                <input type="hidden" value="1" id="total_secret_services" name="total_secret_services">
                            <?php
                            }
                            ?>
                        </div>

                        <div class="tab-pane fade" id="nav-steps" role="tabpanel" aria-labelledby="nav-steps-tab">
                            <?php
                            if (count($blocks_list) > 0) {
                            ?>
                                <div class="form-row blocks_html">
                                    <?php
                                    for ($jak_i = 0; $jak_i < count($blocks_list); $jak_i++) {
                                        $new_id = $jak_i + 1;
                                    ?>
                                        <div class="form-row col-md-12 each-row each-block" style="margin-bottom:30px;" id="blocks_html_<?php echo $new_id; ?>">
                                            <div class="form-group col-md-6">
                                                <label for="inputEmail4">Code</label>
                                                <input autocomplete="off" type="text" class="form-control blocks_code" id="blocks_code<?php echo $new_id; ?>" name="blocks_code[]" placeholder="" value="<?= $blocks_list[$jak_i]['code'] ?>"><br>

                                                <label for="inputEmail4">Title</label>
                                                <input autocomplete="off" type="text" class="form-control" id="blocks_title<?php echo $new_id; ?>" name="blocks_title[]" placeholder="" value="<?= $blocks_list[$jak_i]['title'] ?>"><br>

                                                <label for="inputEmail4">Sort</label>
                                                <input autocomplete="off" type="number" class="form-control" name="sort[]" placeholder="" value="<?= $blocks_list[$jak_i]['sort'] ?>">

                                                <label for="inputEmail4">Type</label>
                                                <select name="type[]" id="text_type" class="form-control text_type">
                                                    <option value="database" <?php if ($blocks_list[$jak_i]['type'] == 'database')
                                                                                    echo "selected"; ?>>Database</option>
                                                    <option value="nginx" <?php if ($blocks_list[$jak_i]['type'] == 'nginx')
                                                                                echo "selected"; ?>>Nginx</option>
                                                    <option value="dns" <?php if ($blocks_list[$jak_i]['type'] == 'dns')
                                                                            echo "selected"; ?>>DNS</option>
                                                    <option value="varnish" <?php if ($blocks_list[$jak_i]['type'] == 'varnish')
                                                                                echo "selected"; ?>>Varnish</option>
                                                    <option value="secrets" <?php if ($blocks_list[$jak_i]['type'] == 'secrets')
                                                                                echo "selected"; ?>>Secrets</option>
                                                    <option value="bash" <?php if ($blocks_list[$jak_i]['type'] == 'bash')
                                                                                echo "selected"; ?>>Bash</option>
                                                </select>
                                            </div>

                                            <input type="hidden" class="hidden_type_value" value="<?= $blocks_list[$jak_i]['type'] ?>">
                                            <input type="hidden" class="hidden_blocks_text_value" value="<?= $blocks_list[$jak_i]['text'] ?>">

                                            <div class="form-group col-md-5 textarea-block">
                                                <label class="textarea_label" for="inputEmail4">
                                                    <?php echo @$type[$blocks_list[$jak_i]['type']]; ?>
                                                </label>

                                                <textarea class="form-control blocks_text <?php if ($blocks_list[$jak_i]['type'] == 'WYSIWYG') {
                                                                                                echo "myClassName";
                                                                                            } else {
                                                                                                echo "textarea-height";
                                                                                            } ?>" id="blocks_text<?php echo $new_id; ?>" name="blocks_text[]" rows="12"><?= $blocks_list[$jak_i]['text'] ?></textarea>
                                            </div>
                                            <input type="hidden" value="<?= $blocks_list[$jak_i]['id'] ?>" id="blocks_id" name="blocks_id[]">

                                            <div class="form-group col-md-1 change">
                                                <button class="btn btn-info bootstrap-touchspin-up deleteaddress" id="deleteRow" type="button" data-type="service_step" data-id="<?= $blocks_list[$jak_i]['id'] ?>" style="max-height: 35px;margin-top: 38px;margin-left: 10px;margin-bottom:10px;">-</button>
                                                <br>
                                                <a href="#" class="tooltip-class" style="margin-left: 23px;" data-toggle="tooltip" title="<?= @$data_type_format[$blocks_list[$jak_i]['type']]; ?>"><i class="fa fa-info-circle"></i></a>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </div>

                                <input type="hidden" value="<?php echo count($blocks_list); ?>" id="total_blocks" name="total_blocks" />

                            <?php
                            } else {
                            ?>
                                <div class="form-row each-block" style="margin-bottom:30px;" id="blocks_html_1">
                                    <div class="form-group col-md-6">
                                        <label for="inputEmail4">Code</label>
                                        <input autocomplete="off" type="text" class="form-control blocks_code" id="first_name_1" name="blocks_code[]" placeholder="" value="">

                                        <label for="inputEmail4">Title</label>
                                        <input autocomplete="off" type="text" class="form-control" id="surname" name="blocks_title[]" placeholder="" value="">
                                        <label for="inputEmail4">Sort</label>
                                        <input autocomplete="off" type="number" class="form-control" name="sort[]" placeholder="" value="">

                                        <label for="inputEmail4">Type</label>
                                        <select name="type[]" id="text_type" class="form-control text_type">
                                            <option value="database">Database</option>
                                            <option value="nginx">Nginx</option>
                                            <option value="dns">DNS</option>
                                            <option value="varnish">Varnish</option>
                                            <option value="secrets">Secrets</option>
                                            <option value="bash">Bash</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-5 textarea-block">
                                        <label class="textarea_label" for="inputEmail4">Text</label>
                                        <textarea class="form-control textarea-height blocks_text" id="ck-content" name="blocks_text[]" rows="12"></textarea>
                                    </div>
                                </div>
                                <input type="hidden" value="0" id="contact_id" name="contact_id">
                                <div class="form-row blocks_html">
                                </div>
                                <input type="hidden" value="1" id="total_blocks" name="total_blocks">
                            <?php
                            }
                            ?>

                            <div class="form-group">
                                <button class="btn btn-primary add_step" type="button" style="float:right;margin-right: 120px;">Add Steps</button><br><br>
                            </div>

                        </div>

                        <div class="tab-pane fade" id="nav-domains" role="tabpanel" aria-labelledby="nav-domains-tab">

                            <?php
                            if (count($serviceDomains) > 0) {
                            ?>
                                <div class="form-row domains_container">
                                    <?php
                                    for ($jak_i = 0; $jak_i < count($serviceDomains); $jak_i++) {
                                        $new_id = $jak_i + 1;
                                    ?>
                                        <div class="form-row col-md-12" id="domains_<?php echo $new_id; ?>">
                                            <div class="form-group col-md-6">

                                                <label for="inputEmail4">Select Domain</label>



                                                <select id="domains" name="domains[]" class="form-control">
                                                    <option value="" selected="">--Select--</option>
                                                    <?php foreach ($all_domains as $row) : ?>
                                                        <option value="<?= $row['uuid']; ?>" <?= ($row['uuid'] == @$serviceDomains[$jak_i]['domain_uuid']) ? 'selected' : '' ?>>
                                                            <?= $row['name']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>

                                            </div>

                                            <?php
                                            if ($jak_i == 0) {
                                            ?>
                                                <div class="form-group col-md-1 change d-flex">
                                                    <button class="btn btn-primary bootstrap-touchspin-up add_domain " type="button" style="max-height: 35px;margin-top: 28px;margin-left: 10px;">+</button>
                                                    <button class="btn btn-info bootstrap-touchspin-up deleteaddress" data-type="domains" data-id="<?= $serviceDomains[$jak_i]['uuid'] ?>" id="deleteRow" type="button" style="max-height: 35px;margin-top: 28px;margin-left: 10px;">-</button>
                                                </div>
                                            <?php
                                            } else {
                                            ?>
                                                <div class="form-group col-md-1 change">
                                                    <button class="btn btn-info bootstrap-touchspin-up deleteaddress" data-type="domains" data-id="<?= $serviceDomains[$jak_i]['uuid'] ?>" id="deleteRow" type="button" style="max-height: 35px;margin-top: 28px;margin-left: 10px;">-</button>
                                                </div>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </div>

                                <input type="hidden" value="<?php echo count($serviceDomains); ?>" id="total_domains" name="total_domains">

                            <?php
                            } else {
                            ?>
                                <div class="form-row" id="domains_1">
                                    <div class="form-group col-md-6">
                                        <label for="inputEmail4">Select Domain</label>

                                        <select id="domains" name="domains[]" class="form-control">
                                            <option value="" selected="">--Select--</option>
                                            <?php foreach ($all_domains as $row) : ?>
                                                <option value="<?= $row['uuid']; ?>">
                                                    <?= $row['name']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>


                                    </div>

                                    <div class="form-group col-md-1 change">
                                        <button class="btn btn-primary bootstrap-touchspin-up add_domain" type="button" style="max-height: 35px;margin-top: 28px;margin-left: 10px;">+</button>
                                    </div>
                                </div>
                                <div class="form-row domains_container">

                                </div>
                                <input type="hidden" value="1" id="total_domains" name="total_domains">
                                <input type="hidden" value="<?php echo $uriSegment; ?>" id="serviceId" name="serviceId">
                            <?php
                            }
                            ?>
                        </div>


                    </div>
                </div>
            </div>

            <div class="form-row justify-content-end">
                <div class="form-group mr-3">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                <div class="form-group mr-3">
                    <button type="button" id="DeployService" class="btn btn-primary page_title_right">
                        Deploy
                    </button>
                </div>
                <div class="form-group mr-3">
                    <button type="button" id="DeleteService" class="btn btn-primary page_title_right">
                        Delete
                    </button>
                </div>
            </div>
        </form>
        <?php /*<div class="btndata">
 <button type="button" id="RunCmd" class="btn btn-primary page_title_right">Run CMD</button>
</div> */ ?>
    </div>
</div>

<div 
    class="modal fade" 
    id="helmConfirmationModal" 
    tabindex="-1" role="dialog" 
    aria-labelledby="helmConfirmationModalTitle" 
    aria-hidden="true"
    data-backdrop="false"
>
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="helmModalTitle">Deploy to?</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Please select the environment where you want to deploy</p>
        <form id="deploy-modal-form">
            <?php
            if (!empty($service->env_tags)) {
                $arr = explode(',', $service->env_tags);
                foreach ($arr as $key => $envTag) : ?>
                    <div class="form-check">
                        <input type="checkbox" name="<?= $envTag ?>" class="form-check-input select-env-tag" id="select-env_<?= $key ?>">
                        <label for="select-env_<?= $key ?>" class="form-check-label"><?= $envTag ?></label>
                    </div>
                <?php endforeach; ?>
            <?php } ?>
        </form>
        <span class="deply-message" id="deplyMessage"></span>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
        <button type="button" class="btn btn-primary" id="helm-deploy">Yes</button>
      </div>
    </div>
  </div>
</div>

<?php require_once(APPPATH . 'Views/common/footer.php'); ?>


<script>
    // Add the following code if you want the name of the file appear on select
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/additional-methods.min.js"></script>
<script>
    if ($("#addservice").length > 0) {
        $("#addservice").validate({
            rules: {
                name: {
                    required: true,
                },
                notes: {
                    required: true,
                },
                code: {
                    required: true,
                },
                uuid: {
                    required: true,
                },
                /* nginx_config: {
                   required: true,
                 }, 
                 varnish_config: {
                   required: true,
                 },   */
            },
            messages: {
                name: {
                    required: "Please enter name",
                },
                notes: {
                    required: "Please enter notes",
                },
                code: {
                    required: "Please enter code",
                },
                uuid: {
                    required: "Please select user",
                },

            },
        })
    }
</script>

<script type="text/javascript">
    var id = "<?= @$service->uuid ?>";

    $(document).ready(function() {
        $('#service_type').change(function() {
            if ($(this).val() === 'workflows') {
                $('#service-template-wrapper').removeClass('hidden');
                $('#marketing-wrapper').addClass('hidden');
            } else if ($(this).val() === 'marketing') {
                $('#service-template-wrapper').addClass('hidden');
                $('#marketing-wrapper').removeClass('hidden');
            }
        });
    });

    $(document).on('drop', '#drop_file_doc_zone', function(e) {
        // $("#ajax_load").show();
        if (e.originalEvent.dataTransfer) {
            if (e.originalEvent.dataTransfer.files.length) {
                e.preventDefault();
                e.stopPropagation();
                var i = 0;
                while (i < e.originalEvent.dataTransfer.files.length) {
                    newUploadDocFiles(e.originalEvent.dataTransfer.files[i], id);
                    i++;
                }
            }
        }
    });

    $(document).on("change", ".fileUpload", function() {

        for (var count = 0; count < $(this)[0].files.length; count++) {

            newUploadDocFiles($(this)[0].files[count], id);
        }

    });



    function newUploadDocFiles(fileobj, id) {

        $("#ajax_load").hide();

        var form = new FormData();

        form.append("file", fileobj);
        form.append("mainTable", class_name);
        form.append("id", id);

        $.ajax({
            url: '/services/uploadMediaFiles',
            type: 'post',
            dataType: 'json',
            maxNumberOfFiles: 1,
            autoUpload: false,
            success: function(result) {

                $("#ajax_load").hide();
                if (result.status == '1') {
                    $(".all-media-image-files").html(result.file_path);
                } else {
                    toastr.error(result.msg);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $("#ajax_load").hide();
                console.log(textStatus, errorThrown);
            },
            data: form,
            cache: false,
            contentType: false,
            processData: false


        });



    }

    $(document).on('drop', '#drop_file_doc_zone2', function(e) {
        // $("#ajax_load").show();
        if (e.originalEvent.dataTransfer) {
            if (e.originalEvent.dataTransfer.files.length) {
                e.preventDefault();
                e.stopPropagation();
                var i = 0;
                while (i < e.originalEvent.dataTransfer.files.length) {
                    newUploadDocFiles2(e.originalEvent.dataTransfer.files[i], id);
                    i++;
                }
            }
        }
    });

    $(document).on("change", ".fileUpload2", function() {

        for (var count = 0; count < $(this)[0].files.length; count++) {

            newUploadDocFiles2($(this)[0].files[count], id);
        }

    });



    function newUploadDocFiles2(fileobj, id) {

        $("#ajax_load").hide();

        var form = new FormData();

        form.append("file", fileobj);
        form.append("mainTable", class_name);
        form.append("id", id);

        $.ajax({
            url: '/services/uploadMediaFiles2',
            type: 'post',
            dataType: 'json',
            maxNumberOfFiles: 1,
            autoUpload: false,
            success: function(result) {

                $("#ajax_load").hide();
                if (result.status == '1') {
                    $(".all-media-image-files2").html(result.file_path);
                } else {
                    toastr.error(result.msg);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $("#ajax_load").hide();
                console.log(textStatus, errorThrown);
            },
            data: form,
            cache: false,
            contentType: false,
            processData: false


        });



    }

    $(document).ready(function() {
        $("#helm-deploy").click(function() {
            selectedTags = [];
            var x = $(".select-env-tag"); 
            const serviceType = $("#service_type").val();
            $.each(x, function(i, field) {
                selectedTags.push({[field.name]: $(field).is(':checked')});
            }); 
            console.log({selectedTags});
            var Status = $(this).val();
            $.ajax({
                url: "/services/deploy_service/<?= @$service->uuid ?>",
                type: "post",
                data: {
                    'data': { Status,  selectedTags, serviceType}
                },
                success: function(response) {
                    const res = typeof(response) == "string" ? JSON.parse(response) : response;
                    if (res.status != 200) {
                        const msg = `<p class="text-danger">${res.message}</p>`
                        $("#deplyMessage").html(msg);
                    } else {
                        const msg = `<p class="text-success">${res.message}</p>`
                        $("#deplyMessage").html(msg);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log({textStatus, errorThrown});
                }
            });
        }); 
    })

    $('#DeployService').on('click', function() {
        const serviceType = $("#service_type").val();
        if (serviceType === "workflows") {
            $('#helmConfirmationModal').modal('toggle');
        } else if (serviceType === "marketing") {
            $("#helm-deploy").click();
        }
        return false;
        var Status = $(this).val();
        $.ajax({
            url: "/services/deploy_service/<?= @$service->uuid ?>",
            type: "post",
            data: {
                'data': Status
            },
            success: function(response) {
                alert(response);


                // You will get response from your PHP page (what you echo or print)
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    });

    $('#DeleteService').on('click', function() {
        var Status = $(this).val();
        $.ajax({
            url: "/services/delete_service/<?= @$service->uuid ?>",
            type: "post",
            data: {
                'data': Status
            },
            success: function(response) {
                alert(response);


                // You will get response from your PHP page (what you echo or print)
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    });


    $(document).ready(function() {

        var max_fields_limit = 10; //set limit for maximum input fields
        var x = $('#total_secret_services').val(); //initialize counter for text box
        $('.add').click(function(e) { //click event on add more fields button having class add_more_button
            // e.preventDefault();
            if (x < max_fields_limit) { //check conditions
                x++; //counter increment

                $('.addresscontainer').append(`
                    <div class="form-row col-md-12" id="office_address_${x}">
                        <div class="form-group col-md-3">
                            <label for="inputSecretKey">Secret Key</label>
                            <input autocomplete="off" type="text" class="form-control" id="key_name_${x}" name="key_name[]" placeholder="" value="">
                        </div>
                        <div class="form-group col-md-5">
                            <label for="inputSecretValue">Secret Value</label>
                            <input autocomplete="off" type="text" class="form-control" id="key_value_${x}" name="key_value[]" placeholder="" value="">
                        </div>
                        <div class="form-group col-md-2 d-flex flex-column">
                            <label for="my-select2_${x}">Environment</label>
                            <select id="my-select2_${x}" data-select2-tags="true" name="secret_tags[]" class="form-control select2">
                                <option value="" >--Select--</option>
                            </select>
                        </div>
                        <div class="form-group col-md-1 change">
                            <button class="btn btn-info bootstrap-touchspin-up deleteaddress" data-type="secret_services" id="deleteRow" type="button" style="max-height: 35px;margin-top: 28px;margin-left: 10px;">-</button>
                        </div>
                        <div class="form-group col-md-1 change">
                            <button 
                                class="btn btn-info bootstrap-touchspin-up clone-row" 
                                data-type="secret_services" 
                                data-uuid="" 
                                data-id="office_address_${x}"
                                data-key="${x}"
                                id="cloneRow" 
                                type="button" 
                                style="max-height: 35px;margin-top: 28px;margin-left: 10px;"
                            >
                                <i class="fas fa-clone"></i>
                            </button>
                        </div>
                    </div>`
                );

            }
            $('.select2').select2({
                placeholder: "Select an Option",
                allowClear: true,
                tags: true,
            });
            
            $(".select2-container--default .select2-selection--single .select2-selection__clear").css("padding-right", "20px");
            $(".select2-container--default .select2-selection--single .select2-selection__clear").css("padding-top", "7px");

            $('.deleteaddress').on("click", function(e) { //user click on remove text links
                e.preventDefault();
                $(this).parent().parent().remove();
                x--;
            })
        });

        $(".select2-container--default .select2-selection--single .select2-selection__clear").css("padding-right", "20px");
        $(".select2-container--default .select2-selection--single .select2-selection__clear").css("padding-top", "7px");
    });

    $('.deleteaddress').on("click", function(e) { //user click on remove text links

        var current = $(this);
        var serviceId = current.attr("data-id");
        var serviceType = current.attr("data-type");
        var pageId = $("#serviceId").val();
        $.ajax({
            url: baseUrl + "/services/deleteRow",
            data: {
                id: serviceId,
                type: serviceType,
                sId: pageId
            },
            method: 'post',
            success: function(res) {
                console.log(res)
                current.parent().parent().remove();

            }
        })

    })

    $(document).on("click", "#cloneRow", function () {
        let row = $(this).attr("data-id");
        var originalField = document.querySelector(`#${row}`);
        var clonedField = originalField.cloneNode(true);
        var parentNode = document.getElementById("addresscontainer");
        var nodeCount = parentNode.childElementCount;

        clonedField.id = `office_address_${nodeCount + 1}`
        clonedField.children[0].children[1].id = `key_name_${nodeCount + 1}`;
        clonedField.children[1].children[1].id = `key_value_${nodeCount + 1}`;
        clonedField.children[1].children[1].value = ``;
        clonedField.children[1].children[1].defaultValue = ``;
        clonedField.removeChild(clonedField.children[2]);
        var envField = `
            <label for="my-select2_${nodeCount + 1}">Environment</label>
            <select id="my-select2_${nodeCount + 1}" data-select2-tags="true" name="secret_tags[]" class="form-control select2">
                <option value="" >--Select--</option>
            </select>
        `;
        var envNode = document.createElement("div");
        envNode.classList.add("form-group", "col-md-2", "d-flex", "flex-column");
        envNode.innerHTML = envField;
        clonedField.children[2].defaultValue = "";
        clonedField.children[2].value = "";
        var indexToInsertAt = 2;
        var existingElement = clonedField.children[indexToInsertAt];
        clonedField.insertBefore(envNode, existingElement);
        console.log({clonedField});
        document.getElementById('addresscontainer').appendChild(clonedField);
        $('.select2').select2({
            placeholder: "Select an Option",
            allowClear: true,
            tags: true,
        });
    })

    // Add the following code if you want the name of the file appear on select
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });

    $("#file2").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings("#file2").addClass("selected").html(fileName);
    });

    $("#delete_image_logo").on("click", function(e) {
        e.preventDefault();
        $(".all-media-image-files").html("");
    })
    $("#delete_image_logo2").on("click", function(e) {
        e.preventDefault();
        $(".all-media-image-files").html("");
    })

    $(document).ready(function() {

        $('[data-toggle="tooltip"]').tooltip();

        var max_fields_limit = 20; //set limit for maximum input fields
        total_blocks = parseInt($('#total_blocks').val()); //initialize counter for text box

        $('.add_step').click(function(e) { //click event on add more fields button having class add_more_button

            $('.blocks_html').append('<div class="form-row col-md-12 each-block" style="margin-bottom:30px;" id="blocks_html_' + total_blocks + '"><div class="form-group col-md-6">' +
                '<label for="inputSecretKey">Code</label>' +
                '<input type="text" class="form-control blocks_code" id="blocks_code' + total_blocks + '" name="blocks_code[]" placeholder="" value=""><br>' +

                '<label for="inputSecretValue">Title</label>' +
                '<input type="text" class="form-control" id="blocks_title' + total_blocks + '" name="blocks_title[]" placeholder="" value=""><br>' +
                '<label for="inputEmail4">Sort</label>' +
                '<input autocomplete="off" type="number" class="form-control"  name="sort[]" placeholder="" value="">' +

                '<label for="inputEmail4">Type</label>' +
                '<select name="type[]" id="text_type" class="form-control text_type">' +
                '<option value="database">Database</option>' +
                '<option value="nginx">Nginx</option>' +
                '<option value="dns" >DNS</option>' +
                '<option value="varnish" >Varnish</option>' +
                '<option value="secrets" >Secrets	</option>' +
                '<option value="bash">Bash</option>' +
                '</select>' +

                '</div>' +
                '<div class="form-group col-md-5 textarea-block">' +
                '<label class="textarea_label" for="inputSecretValue">Text</label>' +
                '<textarea class="form-control textarea-height blocks_text" id="blocks_text' + total_blocks + '" name="blocks_text[]" placeholder="" value="" ></textarea> ' +
                '</div> <input type="hidden" value="0" id="blocks_id" name="blocks_id[]">' +
                '<div class="form-group col-md-1 change">' +
                '<button class="btn btn-info bootstrap-touchspin-up deleteaddress" data-type="service_step" id="deleteRow" type="button" style="max-height: 35px;margin-top: 28px;margin-left: 10px;">-</button>' +
                '</div></div>'
            );

            total_blocks++;

            CKEDITOR.replaceAll('myClassName');

            $('.deleteaddress').on("click", function(e) { //user click on remove text links

                $(this).parent().parent().remove();
                total_blocks--;

            })
        });
    });



    $(document).ready(function() {

        var max_fields_limit = 10; //set limit for maximum input fields
        var x = $('#total_domains').val(); //initialize counter for text box
        $('.add_domain').click(function(e) { //click event on add more fields button having class add_more_button
            // e.preventDefault();
            if (x < max_fields_limit) { //check conditions
                x++; //counter increment

                $('.domains_container').append('<div class="form-row col-md-12" id="domains_' + x + '"><div class="form-group col-md-6">' +
                    '<label for="inputSecretKey">Select Domain</label>' +
                    '<select id="domains" name="domains[]" class="form-control">                                      <option value="" selected="">--Select--</option> <?php foreach ($all_domains as $row) : ?>     <option value="<?= $row['uuid']; ?>"><?= $row['name']; ?></option> <?php endforeach; ?> </select></div>' +
                    '<div class="form-group col-md-1 change">' +
                    '<button class="btn btn-info bootstrap-touchspin-up deleteaddress" data-type="domains" id="deleteRow" type="button" style="max-height: 35px;margin-top: 28px;margin-left: 10px;">-</button>' +
                    '</div></div>'
                );


            }

            $('.deleteaddress').on("click", function(e) { //user click on remove text links
                e.preventDefault();
                $(this).parent().parent().remove();
                x--;
            })
        });
        var templates = <?php echo json_encode($templates); ?>;
        var secretKeys = <?php echo !empty($tempKeys) ? json_encode($tempKeys) : "[]" ?>;
        console.log({secretKeys});
        function getCodeByUuid(uuid) {
            for (var i = 0; i < templates.length; i++) {
                if (templates[i].uuid === uuid) {
                    return templates[i].code;
                }
            }
            return null;
        }

        function findSecretKey(secretTempId) {
            var secretKey = null;
            $.each(secretKeys, function(index, item) {
                if (item.secret_temp_id === secretTempId) {
                    secretKey = item.secret_key;
                    return false;
                }
            });
            return secretKey;
        }
        function findValSecretKey(secretTempId, valueTempId) {
            var secretKey = null;
            $.each(secretKeys, function(index, item) {
                if (item.secret_temp_id === secretTempId && item.values_temp_id === valueTempId) {
                    secretKey = item.values_key;
                    return false;
                }
            });
            return secretKey;
        }

        function addSecKeyInput (secKeys) {
            $.each(secKeys, function (idx, selectedSec) {
                var secTempName = getCodeByUuid(selectedSec);
                var secKey = findSecretKey(selectedSec);
                var keyHtml = `
                    <div class="col-md-12">
                        <label for="secKeysName${idx}">Take keys From: ${secTempName}</label>
                        <select id="secKeysName${idx}" data-select2-tags="true" name="secKeysName[${selectedSec}][]" class="form-control select2">
                        <option value="${secKey ? secKey : ''}" selected >${secKey ? secKey : ''}</option>
                        </select>
                    </div>
                `;
                $("#secretKeysName").append(keyHtml);
                selectRefresh();
            });
        }
        function addValuesKeyInput (secKeys, tempId) {
            $.each(secKeys, function (idx, selectedSec) {
                var secTempName = getCodeByUuid(selectedSec);
                var valKey = findValSecretKey(selectedSec, tempId)
                var keyHtml = `
                    <div class="col-md-12">
                        <label for="valKeysName${idx}">Add Keys For: ${secTempName}</label>
                        <select id="valKeysName${idx}" data-select2-tags="true" name="valKeysName[${tempId}/${selectedSec}][]" class="form-control select2">
                        <option value="${valKey ? valKey : ''}" selected >${valKey ? valKey : ''}</option>
                        </select>
                    </div>
                `;
                $("#valuesKeysName").append(keyHtml);
                selectRefresh();
            });
        }

        var previousSelectedValues = <?php echo json_encode($secret_values_templates['secret_template_id']); ?>;
        var prevValTemps = "<?php echo $secret_values_templates['values_template_id'] ?? "false"; ?>";
        addSecKeyInput(previousSelectedValues);
        addValuesKeyInput(previousSelectedValues, prevValTemps);
        $('#secret_template').on('change', function() {
            var selectedSecretValues = $(this).val();
            var selectedValues = $("#values_template").val();
            $("#secretKeysName").html("");
            addSecKeyInput(selectedSecretValues);
            if (selectedValues) {
                $("#valuesKeysName").html("");
                addValuesKeyInput(selectedSecretValues, selectedValues);
            }
        });
        $('#values_template').on('change', function() {
            var selectedValues = $(this).val();
            var selectedSecretValues = $("#secret_template").val();
            $("#valuesKeysName").html("");
            addValuesKeyInput(selectedSecretValues, selectedValues);
        });
    });
</script>