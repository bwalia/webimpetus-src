$workerra-ci_info_array = $this->loadworkerra-ciInfo();
		
		if (array_key_exists("version",$workerra-ci_info_array))
		{
			setenv("APP_FULL_VERSION_NO", $workerra-ci_info_array["version"]);
		}
	 	 else
		{
		echo "Version does not exist!";
		}

		if (array_key_exists("build",$workerra-ci_info_array))
		{
			setenv("APP_FULL_BUILD_NO", $workerra-ci_info_array["build"]);
		}
	 	 else
		{
		echo "Build no does not exist!";
		}


        public function loadworkerra-ciInfo()
	{
		$json_decode = [];
		$workerra-ciJSPNFilePath = APPPATH . "workerra-ci.json";
		if (file_exists($workerra-ciJSPNFilePath)) {
			$json = file_get_contents ($workerra-ciJSPNFilePath);
		// Decode the JSON file
		$json_data = json_decode($json,true);
		}
		return json_decode;
	}	

	  
