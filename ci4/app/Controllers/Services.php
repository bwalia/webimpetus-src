<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Service_model;
use App\Models\Users_model;
use App\Models\Tenant_model;
use App\Models\Cat_model;
use App\Models\Secret_model;
use App\Models\Template_model;
use App\Models\Meta_model;
use App\Models\Amazon_s3_model;
use App\Models\Core\Common_model;
use App\Libraries\UUID;
use App\Models\Companies;
use App\Models\Email_model;
use App\Models\ServiceDomainsModel;
use Symfony\Component\Yaml\Yaml;


class Services extends Api
{
	public $serviceModel;
	public $user_model;
	public $secret_model;
	public $template_model;
	public $meta_model;
	public $Amazon_s3_model;
	public $businessUuid;
	public $whereCond;
	public $serviceDomainModel;
	public $emailModel;
	public $compniesModel;

	public function __construct()
	{
		parent::__construct();
		$this->session = \Config\Services::session();
		$this->serviceModel = new Service_model();
		$this->user_model = new Users_model();
		$this->tmodel = new Tenant_model();
		$this->cmodel = new Cat_model();
		$this->secret_model = new Secret_model();
		$this->template_model = new Template_model();
		$this->meta_model = new Meta_model();
		$this->Amazon_s3_model = new Amazon_s3_model();
		$this->db = \Config\Database::connect();
		$this->emailModel = new Email_model();
		$this->compniesModel = new Companies();
		helper(["global"]);

		$this->common_model = new Common_model();
		$this->common_model->getMenuCode("/services");
		$this->businessUuid = session('uuid_business');
		$this->whereCond['uuid_business_id'] = $this->businessUuid;
		$menucode = $this->getMenuCode("/services");
		$this->session->set("menucode", $menucode);
		$this->serviceDomainModel = new ServiceDomainsModel();
	}

	public function index()
	{
		$data['services'] = $this->serviceModel->getRowsWithService();
		$data['tableName'] = "services";
		$data['rawTblName'] = "service";
		$data['is_add_permission'] = 1;
		echo view('services/list', $data);
	}

	public function edit($id = 0)
	{
		$data['tableName'] = "services";
		$data['rawTblName'] = "service";
		$data['service'] = !empty($id) ? $this->serviceModel->getRowsWithService($id)->getRow() : [];
		$data['tenants'] = $this->tmodel->getRows();
		$data['category'] = $this->cmodel->getRows();
		$data['users'] = $this->user_model->getUser();
		$data['secret_services'] = $this->secret_model->getSecrets($id);
		$data['serviceDomains'] = $this->serviceDomainModel->getRowsByService($id);
		$data['all_domains'] = $this->common_model->getCommonData('domains', ['uuid_business_id' => $this->businessUuid]);
		$data['secret_values_templates'] = $this->secret_model->getTemplatesById($id);

		//print_r($data['all_domains']); die;
		echo view('services/edit', $data);
	}


	public function update()
	{
		// $post = $this->request->getPost();
		// print_r($post); die;
		$id = $this->request->getPost('id');
		$data = array(
			'name'  => $this->request->getPost('name'),
			'code' => $this->request->getPost('code'),
			'notes' => $this->request->getPost('notes'),
			'user_uuid' => $this->request->getPost('uuid'),
			//'nginx_config' => $this->request->getPost('nginx_config'),
			//'varnish_config' => $this->request->getPost('varnish_config'),
			'cid' => $this->request->getPost('cid'),
			'tid' => $this->request->getPost('tid'),
			'link' => $this->request->getPost('link'),
			'service_type' => $this->request->getPost('service_type'),
			'env_tags' => implode(",", $this->request->getPost('env_tags') ?? []),
			'uuid_business_id' => $this->businessUuid,
		);
		if (empty($id)) {
			$data['uuid'] = UUID::v5(UUID::v4(), 'services');
		} else {
			$data['uuid'] = $id;
		}

		$image_logo = $this->request->getPost('image_logo') ?? "";
		$brand_logo = $this->request->getPost('brand_logo') ?? "";
		if (!empty($image_logo) && strlen($image_logo) > 0) {

			$data['image_logo'] = $this->request->getPost('image_logo');
		}
		if (!empty($brand_logo) && strlen($brand_logo) > 0) {

			$data['image_brand'] = $this->request->getPost('brand_logo');
		}


		$id = $this->serviceModel->insertOrUpdate("services", $id, $data); //die;

		$this->secret_model->deleteServiceFromServiceID($data["uuid"]);
		// print_r($this->secret_model->getLastQuery()->getQuery());
		// echo "\n";
		$key_name = $this->request->getPost('key_name');
		$key_value = $this->request->getPost('key_value');
		$secret_tags = $this->request->getPost('secret_tags');
		$secret_uuids = $this->request->getPost('secret_uuid');

		if (isset($key_name) && isset($key_value)) {
			foreach ($key_name as $key => $value) {
				//$address_data['service_id'] = $id;
				$address_data['key_name'] = $key_name[$key];
				$address_data['secret_tags'] = $secret_tags[$key] ?? NULL;
				$address_data['status'] = 1;
				$address_data['uuid_business_id'] = $this->businessUuid;
				if (isset($secret_uuids[$key]) && $secret_uuids[$key] != '') {
					$address_data['uuid'] = $secret_uuids[$key];
				} else {
					$address_data['uuid'] = UUID::v5(UUID::v4(), 'secrets');
				}
				// print_r(strpos($key_value[$key], '********')); die;
				if (strpos($key_value[$key], '********') === false) {
					$address_data['key_value'] = $key_value[$key];
					$secret_id = $this->secret_model->saveOrUpdateData($id, $address_data);
				} elseif (strpos($key_value[$key], '********') >= 0) {
					unset($address_data['key_value']);
					$secret_id = $this->secret_model->saveOrUpdateData($id, $address_data);
				} else {
					$secret_id = $this->secret_model->getRowsByUUID($address_data['uuid'])->getRowArray();
					if ($secret_id && !empty($secret_id)) {
						$secret_id = $secret_id['id'];
					}
				}

				// print_r($this->secret_model->getLastQuery()->getQuery());
				// echo "\n";
				if ($secret_id > 0) {
					$dataRelated['secret_id'] = $secret_id;
					$dataRelated['service_id'] = $data["uuid"];
					$dataRelated['uuid_business_id'] = $this->businessUuid;
					$dataRelated['uuid'] = UUID::v5(UUID::v4(), 'secrets_services');
					$this->secret_model->saveSecretRelatedData($dataRelated);
					// print_r($this->secret_model->getLastQuery()->getQuery());
					// echo "\n";
				}
			}
		}


		$i = 0;
		$post = $this->request->getPost();
		$secretTemplateId = $post['secret_template'];
		$valuesTemplateId = $post['values_template'];
		$marketingTemplate = $post['email_marketing_template'];

		if ($secretTemplateId && $valuesTemplateId) {
			$templateData = [
				'uuid' => UUID::v5(UUID::v4(), 'templates__services'),
				'secret_template_id' => $secretTemplateId,
				'values_template_id' => $valuesTemplateId,
				'service_id' => $data['uuid'],
			];

			$this->common_model->insertOrUpdateTableData($templateData, "templates__services", "service_id", $id);
		}
		if ($marketingTemplate) {
			$templateData = [
				'uuid' => UUID::v5(UUID::v4(), 'templates__services'),
				'marketing_template_id' => $marketingTemplate,
				'service_id' => $data['uuid']
			];

			$this->common_model->insertOrUpdateTableData($templateData, "templates__services", "service_id", $id);
		}
		//print_r($post); die;
		if (isset($post["blocks_code"]) && !empty($post["blocks_code"]) && count($post["blocks_code"]) > 0) {
			foreach ($post["blocks_code"] as $code) {

				$blocks = [];
				$blocks["code"] = $code;
				//$blocks["webpages_id"] = '';
				$blocks["text"] = $post["blocks_text"][$i];
				$blocks["title"] = $post["blocks_title"][$i];
				$blocks["sort"] = $post["sort"][$i];
				$blocks["type"] = $post["type"][$i];
				$blocks["uuid_linked_table"] = $data['uuid'];
				$blocks["uuid_business_id"] = session('uuid_business');
				$blocks_id =  @$post["blocks_id"][$i];
				//print_r($blocks); die;
				if (empty($blocks["sort"])) {
					$blocks["sort"] = $blocks_id;
				}
				$blocks_id = $this->serviceModel->insertOrUpdate("blocks_list", $blocks_id, $blocks);
				if (empty($blocks["sort"])) {
					$this->serviceModel->insertOrUpdate("blocks_list", $blocks_id, ["sort" => $blocks_id]);
				}

				$i++;
			}
		} else {
			$this->common_model->deleteTableData("blocks_list", $id, "uuid_linked_table");
		}
		//print_r($post["domains"]); die;
		$this->serviceDomainModel->deleteDataByService($this->request->getPost('id'));
		if (isset($post["domains"]) && !empty($post["domains"]) && count($post["domains"]) > 0) {
			foreach ($post["domains"] as $domain) {
				$isDomainExists = $this->serviceDomainModel->checkRecordExists($domain, $id);
				if (empty($isDomainExists)) {
					$serviceDomainData = [
						'uuid' =>  UUID::v5(UUID::v4(), 'service__domains'),
						'service_uuid' => $data['uuid'],
						'domain_uuid' => $domain
					];
					$this->serviceDomainModel->saveData($serviceDomainData);
				}
			}
		} else {
		}


		return redirect()->to('/services');
	}

	public function deploy_service($uuid = 0)
	{
		if (!empty($uuid)) {
			$post = $this->request->getPost();
			if (isset($post['data']['serviceType']) && $post['data']['serviceType'] === "marketing") {
				$this->create_marketing_template($uuid);
				echo json_encode([
					"message" => "Email has been sent to selected companies.",
					"status" => 200
				]);
			} else {
				$selectedTags = array_filter($post['data']['selectedTags'], 'filterFalseValues');
				foreach ($selectedTags as $tk => $selectedTag) {
					$selectedTag = array_keys($selectedTag);

					if (empty($selectedTag[0])) {
						echo json_encode([
							"message" => "No environment selected",
							"status" => 403
						]);
						die;
					} else {
						$this->create_templates($uuid, $selectedTag[0]);
						$this->run_steps($uuid, $selectedTag[0]);
						$installScript = '/bin/bash /var/www/html/writable/helm/' . $selectedTag[0] . '-install-' . $uuid . '.sh';
						$output = shell_exec($installScript);
					}
				}
				// $this->push_service_env_vars($uuid);
				// $this->gen_service_yaml_file($uuid);
				// echo $output; die;
				echo json_encode([
					"message" => "Service deployment process started OK. Verify the deployment using kubectl get pods command",
					"status" => 200
				]);
			}
		} else {
			echo json_encode([
				"message" => "Uuid is empty!!",
				"status" => 403
			]);
		}
	}

	public function create_marketing_template($uuid) {
		$service = $this->common_model->getSingleRowWhere("templates__services", $uuid, "service_id");
		$emailTemplate = $this->common_model->getSingleRowWhere("templates", $service['marketing_template_id'], "uuid");
		$templateSecrets = $this->secret_model->getSecrets($uuid);

		$blocks = $this->common_model->getDataWhere("blocks_list", $uuid, "uuid_linked_table");
		$fromName = "Root Internet Team";
		$fromEmail = "tenthmatrix.mailer@gmail.com";
		$subject = "Moniter you Websites";
		foreach ($blocks as $bKey => $block) {
			if ($block['type'] === "database") {
				$rawQuery = $this->db->query($block['text'])->getResultArray();
				if ($rawQuery && !empty($rawQuery)) {
					$emailMessage = $emailTemplate['template_content'];
					$errors = [];
					foreach ($rawQuery as $cKey => $company) {
						foreach ($templateSecrets as $tKey => $templateSecret) {
							$emailMessage = str_replace('{' . $templateSecret['key_name'] . '}', $company[$templateSecret['key_value']], $emailMessage);
						}
						
						$is_send = $this->emailModel->phpmailer_send_mail($company['email'], $fromName, $fromEmail, $emailMessage, $subject);
						
						if ($is_send === true) {
							$this->compniesModel->set(['is_email_sent' => 1])->where('id', $company['id'])->update();
						} else {
							$errors[] = $is_send ? $is_send : "Email not sent to " . $company['email'];
						}
					}
					$errors = array_unique($errors);
					if (!empty($errors)) {
						echo implode('<br>', $errors);
						die;
					}
				}
				
			}
		}
		
	}

	public function delete_service($uuid = 0)
	{
		if (!empty($uuid)) {

			// $this->create_templates($uuid);
			$this->gen_service_env_file($uuid);
			$this->push_service_env_vars($uuid);
			$this->gen_service_yaml_file($uuid);

			//	exec('/bin/bash /var/www/html/writable/webimpetus_deploy_service.sh', $output, $return);
			$output = shell_exec('/bin/sh /var/www/html/writable/webimpetus_delete_service.sh');
			// This just needs to run the delete script using helm uninstall cmd instead [BUG: This is not working as expected. Need to fix it.]
			//	echo $output;
			echo "Service deletion process started OK. Note: This process does not delete the tenant database.";
		} else {
			echo "Uuid is empty!!";
		}
	}


	public function create_templates($uuid, $userSelectedENV)
	{
		$service = $this->common_model->getSingleRowWhere("templates__services", $uuid, "service_id");
		$secretTemplate = $this->common_model->getSingleRowWhere("templates", $service['secret_template_id'], "uuid");
		$secretYaml = $secretTemplate["template_content"];
		$secretYamlArray = explode("---", $secretYaml);

		$targetEnvRow = $this->common_model->getSecretByServiceUuid("TARGET_ENV", $uuid);
		if (empty($targetEnvRow)) {
			//echo "TARGET_ENV secret not found or is empty"; die;
		} else {
			if (isset($targetEnvRow['key_value']) && !empty($targetEnvRow['key_value'])) {
				$targetEnv = $targetEnvRow['key_value'];
				//	echo  "TARGET_ENV var :" . $targetEnv . ". User selected env: " . $userSelectedENV; die;
			} else {
				//	echo "TARGET_ENV secret found and is not empty"; die;
			}
		}
		foreach ($secretYamlArray as $templateKey => $secretYamlTemplate) {
			$webSecrets = $this->common_model->getDataWhere("secrets_services", $uuid, "service_id");
			foreach ($webSecrets as $key => $webSecret) {
				$secrets = $this->common_model->getSingleRowWhere("secrets", $webSecret['secret_id'], "id");
				$isOverrided = $this->common_model->getSecretByServiceUuid($secrets['key_name'], $uuid, $userSelectedENV);
				if (!empty($isOverrided)) {
					if ($userSelectedENV == $isOverrided['secret_tags']) {
						$secretYamlTemplate = str_replace($isOverrided['key_name'], $isOverrided['key_value'], $secretYamlTemplate);
					} else {
						echo json_encode([
							"message" => "370: " . $secrets['key_name'] . " is not found in $userSelectedENV environment or empty",
							"status" => 403
						]);
						die;
					}
				} else {
					if ($userSelectedENV == $secrets["secret_tags"] || !$secrets["secret_tags"] || !isset($secrets["secret_tags"])) { //
						//	echo $secrets['key_name'] . " 1 : " . $secrets['key_value'] . "<br>";die;
						if ($secrets['key_name'] == "TARGET_ENV") {
							$secretYamlTemplate = str_replace($secrets['key_name'], $userSelectedENV, $secretYamlTemplate);
						} else {
							$secretYamlTemplate = str_replace($secrets['key_name'], $secrets['key_value'], $secretYamlTemplate);
						}
					} else {
						$isNullOverrided = $this->common_model->getSecretByServiceUuid($secrets['key_name'], $uuid, NULL);
						if (!empty($isNullOverrided)) {
							$secretYamlTemplate = str_replace($isNullOverrided['key_name'], $isNullOverrided['key_value'], $secretYamlTemplate);
						} else {
							echo json_encode([
								"message" => "388: " . $secrets['key_name'] . " is not found in $userSelectedENV environment or empty",
								"status" => 403
							]);
							die;
						}
					}
				}
			}
			// Create Secret Yaml File
			$secretFile = fopen(WRITEPATH . "secret/" . $userSelectedENV . "-secret-" . $templateKey . "-" . $uuid . ".yaml", "w") or die("Unable to open file!");
			fwrite($secretFile, $secretYamlTemplate);
			fclose($secretFile);
		}
		// Create kubeseal script to create secrets

		$kubeConfigRow = $this->common_model->getSecretByServiceUuid("KUBECONFIG", $uuid, $userSelectedENV);
		if (empty($kubeConfigRow)) {
			$kubeConfigRow = $this->common_model->getSecretByServiceUuid("KUBECONFIG", $uuid, NULL);
			if (empty($kubeConfigRow)) {
				echo json_encode([
					"message" => "KUBECONFIG secret not found or is empty",
					"status" => 403
				]);
				die;
			}
		}
		$kubeConfig = base64_decode($kubeConfigRow['key_value']);

		$k3sFile = fopen(WRITEPATH . "secret/k3s.yaml", "w") or die("Unable to open file!");
		// echo "KUBECONFIG secret found : " . $kubeConfig;
		fwrite($k3sFile, $kubeConfig);
		fclose($k3sFile);

		$secretCommand = "#!/bin/bash\n";
		$secretCommand .= "set -x\n";
		$secretCommand .= "export KUBECONFIG=" . WRITEPATH . "secret/k3s.yaml\n";
		foreach ($secretYamlArray as $templateKey2 => $secretYamlTemplate2) {
			$secretCommand .= "kubeseal --format=yaml < " . WRITEPATH . "secret/" . $userSelectedENV . "-secret-" . $templateKey2 . "-" . $uuid . ".yaml" . " > " . WRITEPATH . "secret/" . $userSelectedENV . "-sealed-secret-" . $templateKey2 . "-" . $uuid . ".yaml\n";
		}
		$secretFileScript = fopen(WRITEPATH . "secret/" . $userSelectedENV . "-kubeseal-secret.sh", "w") or die("Unable to open file!");
		fwrite($secretFileScript, $secretCommand);
		fclose($secretFileScript);

		shell_exec('/bin/bash /var/www/html/writable/secret/' . $userSelectedENV . '-kubeseal-secret.sh');

		$secretsArray = [];
		foreach ($secretYamlArray as $templateKey3 => $secretYamlTemplate3) {
			$sealedSecretContent = file_get_contents(WRITEPATH . "secret/" . $userSelectedENV . "-sealed-secret-" . $templateKey3 . "-" . $uuid . ".yaml");
			if (empty($sealedSecretContent)) {
				echo json_encode([
					"message" => "Kubeseal command failed. Please check kubernetes cluster connection is working and Kubeseal is setup.",
					"status" => 403
				]);
				die;
			}
			$sealedSecretContent = Yaml::parse($sealedSecretContent);
			// env_file must be present in the secrets file for this work until we fully create dynamic secrets management system
			if (isset($sealedSecretContent["spec"]["encryptedData"]["env_file"])) {
				$envSecret = $sealedSecretContent["spec"]["encryptedData"]["env_file"];
				$secretsArray['env_file'] = $envSecret;
			} else {
				echo json_encode([
					"message" => "Env file not found in sealed secret. Kubeseal command failed\n",
					"status" => 403
				]);
			}

			if (isset($sealedSecretContent["spec"]["encryptedData"]["hostname"])) {
				$secret_hostname = $sealedSecretContent["spec"]["encryptedData"]["hostname"];
				$secretsArray['hostname'] = $secret_hostname;
			}

			if (isset($sealedSecretContent["spec"]["encryptedData"]["password"])) {
				$secret_dbPassword = $sealedSecretContent["spec"]["encryptedData"]["password"];
				$secretsArray['password'] = $secret_dbPassword;
			}

			if (isset($sealedSecretContent["spec"]["encryptedData"]["rootPassword"])) {
				$secret_dbRootPassword = $sealedSecretContent["spec"]["encryptedData"]["rootPassword"];
				$secretsArray['rootPassword'] = $secret_dbRootPassword;
			}

			if (isset($sealedSecretContent["spec"]["encryptedData"]["username"])) {
				$secret_dbUsername = $sealedSecretContent["spec"]["encryptedData"]["username"];
				$secretsArray['username'] = $secret_dbUsername;
			}

			if (isset($sealedSecretContent["spec"]["encryptedData"]["port"])) {
				$secret_dbRootPort = $sealedSecretContent["spec"]["encryptedData"]["port"];
				$secretsArray['port'] = $secret_dbRootPort;
			}
		}
		// Create Values YAML
		$valuesTemplate = $this->common_model->getSingleRowWhere("templates", $service['values_template_id'], "uuid");
		$valuesYaml = $valuesTemplate["template_content"];
		$webSecrets = $this->common_model->getDataWhere("secrets_services", $uuid, "service_id");
		foreach ($webSecrets as $key => $webSecret) {
			$secrets = $this->common_model->getSingleRowWhere("secrets", $webSecret['secret_id'], "id");
			$isOverrided = $this->common_model->getSecretByServiceUuid($secrets['key_name'], $uuid, $userSelectedENV);
			if (!empty($isOverrided)) {
				if ($userSelectedENV == $isOverrided['secret_tags']) {
					$valuesYaml = str_replace($isOverrided['key_name'], $isOverrided['key_value'], $valuesYaml);
				} else {
					echo json_encode([
						"message" => "488: " . $secrets['key_name'] . " is not found in $userSelectedENV environment or empty",
						"status" => 403
					]);
					die;
				}
			} else {
				if ($userSelectedENV == $secrets["secret_tags"] || !$secrets["secret_tags"] || !isset($secrets["secret_tags"])) {
					if ($secrets['key_name'] == "TARGET_ENV") {
						$valuesYaml = str_replace($secrets['key_name'], $userSelectedENV, $valuesYaml);
					} else {
						$valuesYaml = str_replace($secrets['key_name'], $secrets['key_value'], $valuesYaml);
					}
				} else {
					$isNullOverrided = $this->common_model->getSecretByServiceUuid($secrets['key_name'], $uuid, NULL);
					if (!empty($isNullOverrided)) {
						$valuesYaml = str_replace($isNullOverrided['key_name'], $isNullOverrided['key_value'], $valuesYaml);
					} else {
						echo json_encode([
							"message" => "505: " . $secrets['key_name'] . " is not found in $userSelectedENV environment or empty",
							"status" => 403
						]);
						die;
					}
				}
			}
		}

		$modifiedValuesString = Yaml::parse($valuesYaml);
		$serviceDomains = $this->serviceDomainModel->getRowsByService($uuid);
		$hostsArray = [];
		foreach ($serviceDomains as $key => $serviceDomain) {
			$domainData = $this->common_model->getSingleRowWhere("domains", $serviceDomain['domain_uuid'], "uuid");
			if (!empty($domainData) && $domainData) {
				$hostsArray[] = [
					'host' => $domainData['name'],
					'paths' => [[
						'path' => $domainData['domain_path'],
						'pathType' => $domainData['domain_path_type'],
						'serviceName' => $domainData['domain_service_name'],
						'servicePort' => $domainData['domain_service_port'],
					]]
				];
			}
		}
		if (isset($modifiedValuesString['ingress']['hosts']) && !empty($hostsArray)) {
			array_push($modifiedValuesString['ingress']['hosts'], $hostsArray);
		}

		if (isset($modifiedValuesString["secure_env_file"])) {
			$modifiedValuesString["secure_env_file"] = $secretsArray['env_file'];
		} elseif (isset($modifiedValuesString["safeSealedSecret"])) {
			$modifiedValuesString["safeSealedSecret"] = $secretsArray['env_file'];
		}

		/*	$modifiedValuesString["secure_env_file"] = $envSecret; */

		isset($secretsArray['hostname']) ? $modifiedValuesString["db"]["hostname"] = $secretsArray['hostname'] : "";
		isset($secretsArray['password']) ? $modifiedValuesString["db"]["password"] = $secretsArray['password'] : "";
		isset($secretsArray['rootPassword']) ? $modifiedValuesString["db"]["rootPassword"] = $secretsArray['rootPassword'] : "";
		isset($secretsArray['username']) ? $modifiedValuesString["db"]["username"] = $secretsArray['username'] : "";
		isset($secretsArray['port']) ? $modifiedValuesString["db"]["port"] = $secretsArray['port'] : "";

		$modifiedValuesString = YAML::dump($modifiedValuesString);
		$valuesFile = fopen(WRITEPATH . "values/" . $userSelectedENV . "-values-" . $uuid . ".yaml", "w") or die("Unable to open file!");
		fwrite($valuesFile, $modifiedValuesString);
		fclose($valuesFile);

		// helm upgrade -i wsl-int ./devops/webimpetus-chart -f devops/webimpetus-chart/values-int-k3s2.yaml --set-string targetImage="***/webimpetus" --set-string targetImageTag="int" --namespace int --create-namespace
	}

	function run_steps($uuid, $userSelectedENV)
	{
		$getSteps = $this->common_model->getSingleRowWhere("blocks_list", $uuid, "uuid_linked_table");
		$steps = $getSteps["text"];
		$steps = base64_decode($steps);
		$secretServices = $this->common_model->getDataWhere("secrets_services", $uuid, "service_id");
		foreach ($secretServices as $key => $secretService) {
			$secrets = $this->common_model->getSingleRowWhere("secrets", $secretService['secret_id'], "id");

			if ($userSelectedENV == $secrets["secret_tags"] || !$secrets["secret_tags"] || !isset($secrets["secret_tags"])) {
				if ($secrets['key_name'] == "TARGET_ENV") {
					$steps = str_replace("$" . $secrets['key_name'], $userSelectedENV, $steps);
				} else {
					$steps = str_replace("$" . $secrets['key_name'], $secrets['key_value'], $steps);
				}
			} else {
				if ($secrets['key_name'] == "TARGET_ENV") {
					$steps = str_replace("$" . $secrets['key_name'], $userSelectedENV, $steps);
				} else {
					$steps = str_replace("$" . $secrets['key_name'], $secrets['key_value'], $steps);
				}
			}
		}
		$steps = str_replace("-f values", "-f " . WRITEPATH . "values/" . $userSelectedENV . "-values", $steps);
		$helmFile = fopen(WRITEPATH . "helm/" . $userSelectedENV . "-install-" . $uuid . ".sh", "w") or die("Unable to open file!");
		fwrite($helmFile, $steps);
		fclose($helmFile);
	}

	function recursiveReplace(&$array, $search, $replace)
	{
		foreach ($array as $key => &$value) {
			if (is_array($value)) {
				$this->recursiveReplace($value, $search, $replace);
			} elseif (is_string($value)) {
				$array[$key] = preg_replace($search, $replace, $array[$key]);
			}
		}

		return $array;
	}


	public function push_service_env_vars($uuid)
	{
		// Get the contents of the JSON file for service and add as env variables to pass to the deployment
		$svcJsonFileContents = file_get_contents(WRITEPATH . "webimpetus_deployments/service-" . $uuid . ".json");
		// Convert to array
		$svcJsonFileObj = json_decode($svcJsonFileContents);
		putenv("SERVICE_ID=" . $uuid);
		putenv("SERVICE_NAME=" . $svcJsonFileObj->name);
		// loop through all global secrets required for kubernetes deployment 
		$secrets = $this->secret_model->getRows();
		if (!empty($secrets)) {
			foreach ($secrets as $key => $val) {
				if ($val['key_name'] == 'KUBECONFIG') {
					$myfile = fopen(WRITEPATH . "kube_config_auth", "w") or die("Unable to open file!");
					fwrite($myfile, $val['key_value']);
					fclose($myfile);
				}

				if ($val['key_name'] == 'webimpetus_DOCKER_IMAGE' || $val['key_name'] == 'webimpetus_DOCKER_IMAGE_TAG' || $val['key_name'] == 'KUBENETES_CLUSTER_NAME' || $val['key_name'] == 'AWS_ACCESS_KEY_ID' || $val['key_name'] == 'AWS_SECRET_ACCESS_KEY' || $val['key_name'] == 'AWS_DEFAULT_REGION') {
					putenv($val['key_name'] . "=" . $val['key_value']);
				}
			}
		}

		// loop through all secrets of this service 
		$secrets = $this->secret_model->getSecrets($uuid);
		if (!empty($secrets)) {
			foreach ($secrets as $key => $val) {
				putenv($val['key_name'] . "=" . $val['key_value']);
			}
		}
	}


	public function gen_service_env_file($uuid)
	{

		$service_data = file_get_contents(WRITEPATH . 'tizohub.values.template');
		$secrets = $this->secret_model->getSecrets($uuid);
		if (!empty($secrets)) {
			foreach ($secrets as $key => $val) {
				$pattern = "/{{" . $val['key_name'] . "}}/i";
				$service_data = preg_replace($pattern, $val['key_value'], $service_data);
			}
		}

		// loop through all global secrets required for kubernetes deployment 
		$secrets = $this->secret_model->getRows();
		if (!empty($secrets)) {
			foreach ($secrets as $key => $val) {
				if ($val['key_name'] == 'webimpetus_DOCKER_IMAGE' || $val['key_name'] == 'webimpetus_DOCKER_IMAGE_TAG' || $val['key_name'] == 'KUBENETES_CLUSTER_NAME' || $val['key_name'] == 'AWS_ACCESS_KEY_ID' || $val['key_name'] == 'AWS_SECRET_ACCESS_KEY' || $val['key_name'] == 'AWS_DEFAULT_REGION') {
					$pattern = "/{{" . $val['key_name'] . "}}/i";
					$service_data = preg_replace($pattern, $val['key_value'], $service_data);
				}
			}
		}

		$myfile = fopen(WRITEPATH . "webimpetus_deployments/values-" . $uuid . ".yaml", "w") or die("Unable to open file!");
		fwrite($myfile, $service_data);
		fclose($myfile);

		//create php seed
		// $myfile = fopen(WRITEPATH . "webimpetus_deployments/service-".$uuid.".php", "w") or die("Unable to open file!");
		// fwrite($myfile, $service_data);
		// fclose($myfile);

	}


	public function gen_service_yaml_file($uuid)
	{
		$service_data = file_get_contents(WRITEPATH . 'tizohub.yaml.template');

		//then go through service secrets vars and may override any global var values
		$secrets = $this->secret_model->getSecrets($uuid);
		if (!empty($secrets)) {
			foreach ($secrets as $key => $val) {
				$pattern = "/{{" . $val['key_name'] . "}}/i";
				$service_data = preg_replace($pattern, $val['key_value'], $service_data);
			}
		}
		// loop through all global secrets required for kubernetes deployment 
		$secrets = $this->secret_model->getRows();
		if (!empty($secrets)) {
			foreach ($secrets as $key => $val) {
				if ($val['key_name'] == 'webimpetus_DOCKER_IMAGE' || $val['key_name'] == 'webimpetus_DOCKER_IMAGE_TAG' || $val['key_name'] == 'KUBENETES_CLUSTER_NAME' || $val['key_name'] == 'AWS_ACCESS_KEY_ID' || $val['key_name'] == 'AWS_SECRET_ACCESS_KEY' || $val['key_name'] == 'AWS_DEFAULT_REGION') {
					$pattern = "/{{" . $val['key_name'] . "}}/i";
					$service_data = preg_replace($pattern, $val['key_value'], $service_data);
				}
			}
		}

		$myfile = fopen(WRITEPATH . "webimpetus_deployments/service-" . $uuid . ".yaml", "w") or die("Unable to open file!");
		fwrite($myfile, $service_data);
		fclose($myfile);
	}


	public function delete($id)
	{
		// echo $id; die;
		if (!empty($id)) {
			$response = $this->serviceModel->deleteDataByUUID($id);
			if ($response) {
				session()->setFlashdata('message', 'Data deleted Successfully!');
				session()->setFlashdata('alert-class', 'alert-success');
			} else {
				session()->setFlashdata('message', 'Something wrong delete failed!');
				session()->setFlashdata('alert-class', 'alert-danger');
			}
		}

		return redirect()->to('/services');
	}


	public function getMenuCode($value)
	{
		$result = $this->db->table("menu")->getWhere([
			"link" => $value
		])->getRowArray();

		return @$result['id'];
	}


	public function uploadMediaFiles()
	{

		$response = $this->Amazon_s3_model->doUpload("file", "service-logo");

		if ($response["status"]) {

			$id = 0;
			$file_path = $response['filePath'];
			$status = 1;
			$file_views = view("services/uploadedFileView", array("file_path" => $file_path, "id" => $id));
			$msg = "success";
		} else {
			$status = 0;
			$file_views = '';
			$msg = "error";
		}

		echo json_encode(array("status" => $status, "file_path" => $file_views, "msg" => $msg));
	}

	public function deleteRow()
	{

		$id = $this->request->getPost("id");
		$serviceType = $this->request->getPost("type");
		$serviceId = $this->request->getPost("sId");
		switch ($serviceType) {
			case 'domains':
				$nameTable = 'service__domains';
				$fieldName = 'service_uuid';
				$selector = 'uuid';
				break;
			case 'secret_services':
				$nameTable = 'secrets_services';
				$fieldName = 'service_id';
				$selector = 'secret_id';
				break;
			case 'service_step':
				$nameTable = 'blocks_list';
				$fieldName = 'uuid_linked_table';
				$selector = 'id';
				break;
			default:
				$nameTable = "secrets";
				$fieldName = 'id';
				$selector = 'id';
				break;
		}

		$data[$fieldName] = null;

		$res = $this->common_model->unlinkData($nameTable, $id, $selector, $data);
		echo $this->db->getlastQuery();
		echo json_encode($res);
	}

	public function uploadMediaFiles2()
	{

		$folder = $this->request->getPost("mainTable");

		$response = $this->Amazon_s3_model->doUpload("file", $folder);

		if ($response["status"]) {

			$file_path = $response['filePath'];
			$status = 1;
			$file_views = '<input type="hidden" value="' . $file_path . '" name="brand_logo">
		<img class="img-rounded" src="' . $file_path . '" width="100px">
		<a href="" id="delete_image_logo2" class="btn btn-danger"><i class="fa fa-trash"></i></a>';
			$msg = "success";
		} else {
			$status = 0;
			$file_views = '';
			$msg = "error";
		}

		echo json_encode(array("status" => $status, "file_path" => $file_views, "msg" => $msg));
	}

	public function status()
	{
		if (!empty($id = $this->request->getPost('id'))) {
			$data = array(
				'status' => $this->request->getPost('status')
			);
			$this->common_model->updateTableData($id, $data, "services");
		}
		echo '1';
	}
}
